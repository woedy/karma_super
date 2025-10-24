"""Middleware to freeze deployments that should not be publicly accessible yet."""

from django.conf import settings
from django.http import JsonResponse


FREEZE_MESSAGE = (
    "Deployment temporarily frozen while the access gate is hardened. "
    "Set DEPLOYMENT_FREEZE_ENABLED=0 once the gate enforces decisions."
)


def deployment_freeze_middleware(get_response):
    """Return HTTP 503 while the deployment freeze flag is enabled."""

    def middleware(request):
        if getattr(settings, "DEPLOYMENT_FREEZE_ENABLED", False):
            return JsonResponse({"detail": FREEZE_MESSAGE}, status=503)
        return get_response(request)

    return middleware
