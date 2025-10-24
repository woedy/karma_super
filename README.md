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
   DEPLOYMENT_FREEZE_ENABLED=1  # keep frozen until the antibot middleware enforces decisions
   ```
   Copy other variables from `.env.example` as needed

   > ⚠️ When the middleware is reliably blocking unwanted traffic, flip
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

### Verify the outbound lookup guard

Use the same shell that Coolify provides in your production container so the Django code and environment variables match what is serving traffic.

1. Open a shell inside the backend container. You can do this either from the
   Coolify UI (`Services → karma-backend → Console → Launch Shell`) or from an
   SSH session on the host using Docker directly:
   ```bash
   docker exec -it karma-backend-1 bash
   ```
2. Run the following script to confirm every lookup returns quickly (under a second) without needing an external `time` binary:
   ```bash
   python - <<'PY'
   from time import perf_counter
   from core.middleware.block_ips_middleware import (
       check_isp_for_bots,
       check_ip_bot_or_human,
       get_hostname_from_ip,
   )

   start = perf_counter()
   print("ISP score:", check_isp_for_bots("8.8.8.8"))
   print("RDAP score:", check_ip_bot_or_human("8.8.8.8"))
   print("Reverse DNS:", get_hostname_from_ip("8.8.8.8"))
   total_ms = (perf_counter() - start) * 1000
   print(f"Total lookup time: {total_ms:.0f} ms")
   PY
   ```

### Quick Coolify smoke test

If you only need a rapid confirmation from the Coolify dashboard:

1. Open **Services → karma-backend → Console → Launch Shell**.
2. Paste the script above (or just `python - <<'PY' ...`) directly into the console.
3. Ensure the output shows the three lookup results and the total time under `500 ms`; if it does, the HTTPS + timeout guard is working in production.

> ℹ️ If the total runtime prints comfortably under 500 ms and no stack trace appears, the HTTPS + timeout guard is active.

**Optional:** Simulate a provider outage to ensure the guard fails open instead of stalling requests:

```bash
python - <<'PY'
import requests
from unittest import mock
from core.middleware.block_ips_middleware import check_isp_for_bots

with mock.patch("requests.get", side_effect=requests.RequestException):
    print("Fallback ISP score:", check_isp_for_bots("8.8.8.8"))
PY
```

The script should print `Fallback ISP score: 0`, proving that network errors are short-circuited.

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
├── renasant_frontend/
│   ├── Dockerfile         # Local development
│   └── Dockerfile.prod    # Production
├── affinity_frontend/
│   ├── Dockerfile         # Local development
│   └── Dockerfile.prod    # Production
├── .env.example           # Environment variables template
├── switch-env.sh          # Environment switcher (Linux/Mac)
└── switch-env.ps1         # Environment switcher (Windows)
```

## Environment Variables

See `.env.example` for all available environment variables.

### Renasant frontend deployment notes

- Build the `renasant_frontend` service alongside the existing Logix frontend. In Coolify you can now attach a second domain to the new container that serves the Renasant experience.
- The app reads `VITE_BACKEND_URL` at build time, so provide the backend origin in the service’s environment tab (for example, `https://api.example.com/`).
- The frontend calls the `/api/renasant-meta-data-*/` endpoints exposed by the Django backend, mirroring the Logix flow but persisting independent records so you can test both funnels side by side.

### Affinity frontend deployment notes

- Deploy the `affinity_frontend` service in Coolify just like the Logix and Renasant builds. Assign a dedicated domain so you can route students directly to the Affinity-branded funnel.
- Set `VITE_BACKEND_URL` in the service environment so the production bundle targets your backend origin (e.g. `https://api.example.com/`).
- The UI mirrors the Affinity login card across every step and uses the new `/api/affinity-meta-data-*/` endpoints, keeping its submissions separate from the Logix and Renasant datasets for clean demos.
