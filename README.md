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
   DEPLOYMENT_FREEZE_ENABLED=1  # keep frozen until the access gate enforces decisions
   ```
   Copy other variables from `.env.example` as needed

   > ⚠️ When the access gate stops allowing every request through, flip
   > `DEPLOYMENT_FREEZE_ENABLED` to `0` and redeploy to lift the freeze.

4. **Deploy Configuration**:
   - **Docker Compose File**: `docker-compose.prod.yml`
   - **Build Context**: `./`

5. **Domain Configuration**:
   - Coolify **automatically** assigns a domain like `your-project-name.coolify.example.com`
   - **No manual port configuration needed**
   - Both frontend and backend API are accessible through the same domain
   - **Frontend**: `https://your-project-name.coolify.example.com/`
   - **API**: `https://your-project-name.coolify.example.com/api/`

### Access Points (Coolify)

Coolify automatically assigns available ports and provides access through:
- **Application URL**: `https://your-project-name.coolify.example.com`
- **API endpoints** accessible at the same domain under `/api/` path
- **All services** (frontend, backend, redis, celery) run internally without port conflicts

### Production Features

- **Automatic port assignment** - No port conflicts with other projects
- **Reverse proxy** - Coolify routes requests to appropriate services
- **Service discovery** - Backend and frontend communicate via service names
- **SSL/TLS** - Automatic HTTPS certificates via Coolify

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

- **Build failures**: Check the deployment logs in Coolify dashboard for specific error messages
- **Environment variables**: Ensure all required variables are set in Coolify (see `.env.example`)
- **Service connectivity**: Coolify handles internal service communication automatically
- **Domain access**: If you can't access your application, check Coolify's service status and logs

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
