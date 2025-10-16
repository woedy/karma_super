#!/bin/bash

# Environment Switcher for Karma Project
# Usage: ./switch-env.sh [local|docker|production]

ENVIRONMENT=${1:-local}

echo "🔄 Switching to $ENVIRONMENT environment..."

# Set environment variable
export ENVIRONMENT=$ENVIRONMENT

case $ENVIRONMENT in
    "local")
        echo "📝 Local development mode"
        echo "   - No Redis required"
        echo "   - In-memory cache and channels"
        echo "   - SQLite database"
        echo ""
        echo "🚀 Run: python manage.py runserver"
        ;;

    "docker")
        echo "🐳 Local Docker development mode"
        echo "   - Uses Redis container"
        echo "   - SQLite database"
        echo "   - All services in Docker"
        echo ""
        echo "🚀 Run: docker-compose up -d"
        ;;

    "production")
        echo "🚀 Production mode (Coolify)"
        echo "   - Uses Redis container"
        echo "   - PostgreSQL database"
        echo "   - Production optimizations"
        echo ""
        echo "📦 Deploy via Coolify dashboard"
        ;;

    *)
        echo "❌ Unknown environment: $ENVIRONMENT"
        echo "💡 Use: local, docker, or production"
        exit 1
        ;;
esac

echo ""
echo "✅ Environment set to: $ENVIRONMENT"
echo "🔧 Django settings will automatically configure based on ENVIRONMENT variable"
