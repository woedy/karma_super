from django.urls import path

from access.views import AccessStatusView, AccessTokenView


app_name = 'access'

urlpatterns = [
    path('check-access/', AccessStatusView.as_view(), name="access-status"),
    path('access-token/', AccessTokenView.as_view(), name="access-token"),
]
