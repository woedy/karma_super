# Environment Switcher for Karma Project (Windows)
# Set the environment by running this in your terminal or IDE

# Option 1: Set environment variable in current session
# $env:ENVIRONMENT = "local"

# Option 2: Set it permanently in your system
# setx ENVIRONMENT "local"

# Available environments:
# - local: No Redis, in-memory cache, SQLite
# - docker: Local Docker with Redis, SQLite
# - production: Production with Redis, PostgreSQL

# Quick commands:
# Local development:
#   $env:ENVIRONMENT = "local"
#   python manage.py runserver

# Local Docker:
#   $env:ENVIRONMENT = "docker"
#   docker-compose up -d

# Production (Coolify):
#   $env:ENVIRONMENT = "production"
#   # Deploy via Coolify dashboard

Write-Host "Environment switcher loaded!" -ForegroundColor Green
Write-Host "Set `$env:ENVIRONMENT to 'local', 'docker', or 'production'" -ForegroundColor Yellow
