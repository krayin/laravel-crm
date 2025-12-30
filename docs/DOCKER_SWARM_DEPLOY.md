# Docker Swarm Deployment Guide

This guide covers deploying Krayin CRM to a Docker Swarm cluster.

## Table of Contents

- [Prerequisites](#prerequisites)
- [Quick Start](#quick-start)
- [Architecture](#architecture)
- [Configuration](#configuration)
- [Deployment](#deployment)
- [Scaling](#scaling)
- [Updates & Rollbacks](#updates--rollbacks)
- [Theme System in Swarm](#theme-system-in-swarm)
- [Monitoring](#monitoring)
- [Troubleshooting](#troubleshooting)

---

## Prerequisites

### Hardware Requirements

| Component | Minimum | Recommended |
|-----------|---------|-------------|
| Nodes | 1 manager + 1 worker | 3 managers + 3 workers |
| RAM per node | 4GB | 8GB+ |
| CPU per node | 2 cores | 4+ cores |
| Storage | 50GB | 100GB+ SSD |

### Software Requirements

- Docker Engine 24.0+
- Docker Compose v2.20+
- Docker Swarm initialized
- (Optional) Shared storage (NFS, GlusterFS, Ceph)

### Initialize Swarm

```bash
# On manager node
docker swarm init --advertise-addr <MANAGER-IP>

# On worker nodes (use token from manager)
docker swarm join --token <TOKEN> <MANAGER-IP>:2377

# Verify nodes
docker node ls
```

---

## Quick Start

```bash
# 1. Clone repository
git clone https://github.com/your-org/laravel-crm.git
cd laravel-crm

# 2. Initialize environment
./docker/scripts/deploy-swarm.sh --init

# 3. Edit configuration
nano .env.swarm

# 4. Build and deploy
./docker/scripts/deploy-swarm.sh --build

# 5. Check status
./docker/scripts/deploy-swarm.sh --status
```

---

## Architecture

```
                    ┌─────────────────┐
                    │   Traefik LB    │
                    │  (Port 80/443)  │
                    └────────┬────────┘
                             │
              ┌──────────────┼──────────────┐
              ▼              ▼              ▼
        ┌──────────┐   ┌──────────┐   ┌──────────┐
        │  Nginx   │   │  Nginx   │   │  Nginx   │
        │ (static) │   │ (static) │   │ (static) │
        └────┬─────┘   └────┬─────┘   └────┬─────┘
             │              │              │
             ▼              ▼              ▼
        ┌──────────┐   ┌──────────┐   ┌──────────┐
        │ PHP-FPM  │   │ PHP-FPM  │   │ PHP-FPM  │
        │  (app)   │   │  (app)   │   │  (app)   │
        └────┬─────┘   └────┬─────┘   └────┬─────┘
             │              │              │
             └──────────────┼──────────────┘
                            │
              ┌─────────────┼─────────────┐
              ▼             ▼             ▼
        ┌──────────┐  ┌──────────┐  ┌──────────┐
        │  MySQL   │  │  Redis   │  │  Queue   │
        │ (single) │  │ (cache)  │  │ Workers  │
        └──────────┘  └──────────┘  └──────────┘
```

### Services

| Service | Replicas | Port | Notes |
|---------|----------|------|-------|
| `app` | 2-N | 9000 | PHP-FPM, scalable |
| `nginx` | 2-N | 80 | Static files, scalable |
| `mysql` | 1 | 3306 | Database, single |
| `redis` | 1 | 6379 | Cache/sessions |
| `queue` | 2-N | - | Queue workers, scalable |
| `scheduler` | 1 | - | Cron jobs, single |
| `traefik` | 1/node | 80, 443 | Load balancer |

---

## Configuration

### Environment Variables (.env.swarm)

```bash
# Application
APP_VERSION=latest          # Docker image tag
APP_DOMAIN=crm.example.com  # Your domain
APP_KEY=base64:xxxxx        # Laravel app key

# Database
DB_DATABASE=krayin
DB_USERNAME=krayin
DB_PASSWORD=secure-password-here

# Redis
REDIS_PASSWORD=null         # or set a password

# Scaling
APP_REPLICAS=2
QUEUE_REPLICAS=2

# SSL (Traefik)
ACME_EMAIL=admin@example.com
```

### Docker Secrets

Secrets are created automatically by the deploy script:

```bash
# Manual creation if needed
echo -n "your-app-key" | docker secret create app_key -
echo -n "your-db-password" | docker secret create db_password -
echo -n "your-root-password" | docker secret create db_root_password -

# List secrets
docker secret ls

# Remove secret (if updating)
docker secret rm app_key
```

### Node Labels

For placement constraints:

```bash
# Mark node for database
docker node update --label-add db=true node-manager-1

# Mark nodes for workers
docker node update --label-add role=worker node-worker-1
docker node update --label-add role=worker node-worker-2
```

---

## Deployment

### First Deploy

```bash
# Initialize (creates secrets, networks, directories)
./docker/scripts/deploy-swarm.sh --init

# Build image
./docker/scripts/deploy-swarm.sh --build

# Or just deploy (if image exists)
./docker/scripts/deploy-swarm.sh deploy
```

### Manual Deploy

```bash
# Build image
docker build -t krayin/crm:v2.1.5 .

# Push to registry (if using)
docker push registry.example.com/krayin/crm:v2.1.5

# Deploy stack
docker stack deploy -c docker-stack.yml krayin

# Verify
docker stack services krayin
```

---

## Scaling

### Scale Services

```bash
# Scale app replicas
docker service scale krayin_app=5

# Scale queue workers
docker service scale krayin_queue=4

# Scale nginx
docker service scale krayin_nginx=3

# Using deploy script
./docker/scripts/deploy-swarm.sh --scale app=5
```

### Auto-Scaling (Future)

Consider using:
- Docker Swarm autoscaler
- Prometheus + custom metrics
- Kubernetes for advanced auto-scaling

---

## Updates & Rollbacks

### Rolling Update

```bash
# Update with new image
docker service update \
  --image krayin/crm:v2.1.6 \
  --update-parallelism 1 \
  --update-delay 30s \
  --update-failure-action rollback \
  krayin_app

# Update all services
./docker/scripts/deploy-swarm.sh --build
```

### Rollback

```bash
# Rollback single service
docker service rollback krayin_app

# Rollback all
./docker/scripts/deploy-swarm.sh --rollback
```

### Zero-Downtime Deploy

The stack is configured for zero-downtime:

```yaml
update_config:
  parallelism: 1        # Update one at a time
  delay: 30s            # Wait between updates
  failure_action: rollback
  order: start-first    # Start new before stopping old
```

---

## Theme System in Swarm

### Shared Storage

Theme assets must be accessible across all nodes:

```yaml
volumes:
  app-storage:
    driver: local
    driver_opts:
      type: nfs
      o: addr=nfs-server.local,rw
      device: ":/data/krayin/storage"
```

### Cache Invalidation

After theme changes, clear cache across all replicas:

```bash
# Using deploy script
./docker/scripts/deploy-swarm.sh --clear-cache

# Manual (runs on one container, Redis syncs to all)
docker exec $(docker ps -q -f "name=krayin_app" | head -1) \
  php artisan cache:clear
```

### Theme Verification Checklist

After deploying theme updates:

- [ ] Access `/admin/settings/theme`
- [ ] Verify theme cards show color circles
- [ ] Select and save a theme
- [ ] Check login page reflects changes
- [ ] Verify across multiple replicas (use different browsers)

---

## Monitoring

### View Logs

```bash
# All services
docker stack ps krayin

# Specific service
docker service logs -f krayin_app

# Using deploy script
./docker/scripts/deploy-swarm.sh --logs app
./docker/scripts/deploy-swarm.sh --logs queue
```

### Health Checks

Each service has built-in health checks:

```bash
# View health status
docker inspect --format='{{.State.Health.Status}}' <container_id>

# View service health
docker service ps krayin_app --format "{{.Name}}: {{.CurrentState}}"
```

### Resource Usage

```bash
# Per-service stats
docker stats $(docker ps -q -f "name=krayin")

# Node resources
docker node inspect --format '{{.Status.State}}' node-1
```

---

## Troubleshooting

### Common Issues

#### Services not starting

```bash
# Check service logs
docker service logs krayin_app --tail 100

# Check task status
docker service ps krayin_app --no-trunc

# Common causes:
# - Secrets not created
# - Network not created
# - Volume mount path doesn't exist
```

#### Database connection failed

```bash
# Check MySQL is running
docker service logs krayin_mysql

# Test connection from app container
docker exec -it $(docker ps -q -f "name=krayin_app" | head -1) \
  php artisan tinker --execute="DB::connection()->getPdo()"
```

#### Theme not updating

```bash
# Clear all caches
./docker/scripts/deploy-swarm.sh --clear-cache

# Force re-read from database
docker exec -it $(docker ps -q -f "name=krayin_app" | head -1) \
  php artisan cache:forget theme.config.v2

# Verify config
docker exec -it $(docker ps -q -f "name=krayin_app" | head -1) \
  php artisan tinker --execute="dd(app('theme.config')->get())"
```

#### Image not found

```bash
# If using private registry
docker login registry.example.com

# Re-deploy with auth
docker stack deploy -c docker-stack.yml krayin --with-registry-auth
```

### Reset Stack

```bash
# Remove stack completely
docker stack rm krayin

# Wait for cleanup
sleep 30

# Remove volumes (CAUTION: data loss)
docker volume rm krayin_mysql-data krayin_redis-data

# Re-deploy
./docker/scripts/deploy-swarm.sh --build
```

---

## Security Considerations

### Secrets Management

- Never commit `.env.swarm` to git
- Use Docker secrets for sensitive data
- Rotate secrets periodically

### Network Isolation

- `krayin-internal`: Internal only, no external access
- `traefik-public`: Exposed to load balancer

### Updates

```bash
# Update base images regularly
docker pull php:8.3-fpm-alpine
docker pull nginx:1.25-alpine
docker pull mysql:8.0
docker pull redis:7-alpine

# Rebuild application image
./docker/scripts/deploy-swarm.sh --build
```

---

## Related Documentation

- [THEMES.md](krayin/THEMES.md) - Theme system documentation
- [docker-stack.yml](../docker-stack.yml) - Stack configuration
- [Dockerfile](../Dockerfile) - Application image
