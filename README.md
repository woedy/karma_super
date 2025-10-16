# Karma Project - Multi-Environment Setup

This project supports multiple environments with automatic configuration switching.

## Environment Switching

Set the `ENVIRONMENT` variable to switch between configurations:

```bash
# Local development (no Docker)
export ENVIRONMENT=local

# Local Docker development
export ENVIRONMENT=docker

# Production (Coolify)
export ENVIRONMENT=production
```

**Windows PowerShell:**
```powershell
# Local development
$env:ENVIRONMENT = "local"

# Local Docker development
$env:ENVIRONMENT = "docker"

# Production
$env:ENVIRONMENT = "production"


# Check if ENVIRONMENT variable is set
echo $env:ENVIRONMENT

# Should show: local, docker, or production
```

## Environment Configurations

| Environment | Redis | Database | Use Case |
|-------------|-------|----------|----------|
| `local` | In-Memory | SQLite | Development without Docker |
| `docker` | Redis Container | SQLite | Local Docker development |
| `production` | Redis Container | PostgreSQL | Coolify production deployment |

## Services Included

- **Backend**: Django application with environment-specific database
- **Frontend**: React/TypeScript application
- **Redis**: Caching and message broker (Docker environments only)
- **Celery**: Background task worker (Docker environments only)
- **Database**: SQLite (local/docker) or PostgreSQL (production)

## Quick Start (Development)

1. **Set environment:**
   ```bash
   export ENVIRONMENT=local  # or docker
   ```

2. **Start the application:**
   ```bash
   # Local development
   python manage.py runserver
   
   # Local Docker
   docker-compose up -d
   ```

3. **View logs:**
   ```bash
   docker-compose logs -f
   ```

4. **Stop the application:**
   ```bash
   docker-compose down
   ```

## Access Points (Development)

- **Frontend**: http://localhost:3000
- **Backend API**: http://localhost:8000
- **Redis**: localhost:6379 (Docker only)

## Development

For development with live reloading:

```bash
# Backend only
docker-compose up backend -d

# Frontend only
docker-compose up frontend -d

# All services
docker-compose up -d
```

## Production Deployment (Coolify)

### Deployment Steps

1. **Connect your repository** to Coolify (GitHub, GitLab, etc.)

2. **Create a new project** in Coolify:
   - Name: `karma-production`
   - Type: `Docker Compose`

3. **Configure Environment Variables** in Coolify:
   ```bash
   ENVIRONMENT=production
   SECRET_KEY=your-production-secret-key
   ALLOWED_HOSTS=your-domain.com,www.your-domain.com
   ```
   Copy other variables from `.env.example` as needed

4. **Deploy Configuration**:
   - **Docker Compose File**: `docker-compose.prod.yml`
   - **Build Context**: `./`

5. **Domain Configuration**:
   - Frontend: `your-domain.com` → Port 3000
   - Backend: `api.your-domain.com` → Port 8000 *(if you want separate domains)*
   *(Alternatively, use subdomains like `app.your-domain.com` for frontend and `api.your-domain.com` for backend)*

### Production Features

- **Gunicorn** for backend (3 workers, 2 threads)
- **Nginx** for frontend with static asset caching
- **Redis** for session storage and caching
- **Celery** for background tasks
- **PostgreSQL** for production database

### SSL/TLS

Coolify automatically provides SSL certificates via Let's Encrypt when you configure domains.

### Monitoring

Coolify provides built-in monitoring. Check the Coolify dashboard for:
- Container logs
- Resource usage
- Deployment status

### Updates

For updates:
1. Push changes to your repository
2. Coolify will automatically redeploy
3. Check deployment logs in Coolify dashboard

## Troubleshooting

- If you encounter port conflicts, modify the port mappings in `docker-compose.yml`
- For database issues, you can reset the database by removing the `postgres_data` volume:
  ```bash
  docker-compose down -v
  docker-compose up -d
  ```

## File Structure

```
karma_super/
├── docker-compose.yml      # Local Docker development
├── docker-compose.prod.yml # Production (Coolify)
├── karma_backend/
│   ├── Dockerfile         # Local development
│   ├── Dockerfile.prod    # Production
│   └── core/settings.py   # Environment-aware settings
├── logix_frontend/
│   ├── Dockerfile         # Local development
│   ├── Dockerfile.prod    # Production
│   └── nginx.conf         # Production nginx config
├── .env.example           # Environment variables template
├── switch-env.sh          # Environment switcher (Linux/Mac)
└── switch-env.ps1         # Environment switcher (Windows)
```

## Environment Variables

See `.env.example` for all available environment variables.
