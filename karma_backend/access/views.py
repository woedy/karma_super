from rest_framework import status
from rest_framework.response import Response
from rest_framework.views import APIView


class AccessPageView(APIView):
    authentication_classes = []
    permission_classes = []

    def get(self, request):
        return Response({"message": "Access granted"}, status=status.HTTP_200_OK)
