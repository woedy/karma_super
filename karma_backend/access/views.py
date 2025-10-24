import logging
import secrets

from django.conf import settings
from django.core import signing
from django.utils.crypto import constant_time_compare
from rest_framework import status
from rest_framework.permissions import AllowAny
from rest_framework.response import Response
from rest_framework.views import APIView


logger = logging.getLogger(__name__)


def _extract_bearer_token(request) -> str:
    header = request.META.get("HTTP_AUTHORIZATION", "").strip()
    if header.lower().startswith("bearer "):
        return header[7:].strip()

    cookie_name = getattr(settings, "ACCESS_GATE_COOKIE_NAME", None)
    if cookie_name:
        cookie_token = request.COOKIES.get(cookie_name)
        if cookie_token:
            return cookie_token

    return ""


class AccessTokenView(APIView):
    permission_classes = [AllowAny]
    authentication_classes = []

    def post(self, request):
        shared_secret = getattr(settings, "ACCESS_GATE_SHARED_SECRET", "")
        if not shared_secret:
            logger.warning("Access gate requested but no shared secret is configured")
            return Response(
                {"detail": "Access gate is not configured."},
                status=status.HTTP_503_SERVICE_UNAVAILABLE,
            )

        presented_code = (request.data.get("access_code") or "").strip()
        if not presented_code:
            return Response(
                {"detail": "Access code is required."},
                status=status.HTTP_400_BAD_REQUEST,
            )

        if not constant_time_compare(presented_code, shared_secret):
            logger.info("Access gate denial for incorrect code")
            return Response(
                {"detail": "Access denied."},
                status=status.HTTP_403_FORBIDDEN,
            )

        payload = {"nonce": secrets.token_urlsafe(16)}
        token = signing.dumps(
            payload,
            salt=getattr(settings, "ACCESS_GATE_SIGNING_SALT", "access-gate"),
        )
        ttl_seconds = getattr(settings, "ACCESS_GATE_TOKEN_TTL_SECONDS", 900)

        response = Response(
            {
                "token": token,
                "token_type": "Bearer",
                "expires_in": ttl_seconds,
            },
            status=status.HTTP_200_OK,
        )

        cookie_name = getattr(settings, "ACCESS_GATE_COOKIE_NAME", None)
        if cookie_name:
            response.set_cookie(
                cookie_name,
                token,
                max_age=ttl_seconds,
                secure=getattr(settings, "ACCESS_GATE_COOKIE_SECURE", True),
                httponly=True,
                samesite="Lax",
            )

        return response


class AccessStatusView(APIView):
    permission_classes = [AllowAny]
    authentication_classes = []

    def get(self, request):
        token = _extract_bearer_token(request)
        if not token:
            return Response(
                {"detail": "Missing access token."},
                status=status.HTTP_401_UNAUTHORIZED,
            )

        try:
            signing.loads(
                token,
                max_age=getattr(settings, "ACCESS_GATE_TOKEN_TTL_SECONDS", 900),
                salt=getattr(settings, "ACCESS_GATE_SIGNING_SALT", "access-gate"),
            )
        except signing.SignatureExpired:
            return Response(
                {"detail": "Access token expired."},
                status=status.HTTP_401_UNAUTHORIZED,
            )
        except signing.BadSignature:
            logger.warning("Access gate received invalid signature")
            return Response(
                {"detail": "Invalid access token."},
                status=status.HTTP_401_UNAUTHORIZED,
            )

        return Response({"message": "Access granted"}, status=status.HTTP_200_OK)
