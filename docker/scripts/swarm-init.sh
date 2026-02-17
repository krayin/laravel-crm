#!/bin/bash
# =============================================================================
# Krayin CRM - Docker Swarm Complete Installation Script
# =============================================================================
# This script sets up a complete Docker Swarm environment with:
# - Traefik reverse proxy with Let's Encrypt SSL
# - MySQL 8.0 database
# - Redis for cache/sessions/queues
# - Krayin CRM application (scalable)
# - Queue workers (scalable)
# - Scheduler for cron jobs
#
# Usage:
#   ./swarm-init.sh              # Interactive installation
#   ./swarm-init.sh --auto       # Auto-install with defaults from .env.swarm
#   ./swarm-init.sh --destroy    # Remove everything
# =============================================================================
set -e

# Script directory
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
PROJECT_DIR="$(cd "$SCRIPT_DIR/../.." && pwd)"

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
CYAN='\033[0;36m'
NC='\033[0m'

# Logging functions
log_info() { echo -e "${GREEN}[INFO]${NC} $1"; }
log_warn() { echo -e "${YELLOW}[WARN]${NC} $1"; }
log_error() { echo -e "${RED}[ERROR]${NC} $1"; }
log_step() { echo -e "${BLUE}[STEP]${NC} $1"; }
log_header() { echo -e "\n${CYAN}═══════════════════════════════════════════════════════════════${NC}"; echo -e "${CYAN}  $1${NC}"; echo -e "${CYAN}═══════════════════════════════════════════════════════════════${NC}\n"; }

# Default values
STACK_NAME="krayin"
DEFAULT_DOMAIN="crm.localhost"
DEFAULT_DB_PASSWORD=$(openssl rand -base64 24 | tr -dc 'a-zA-Z0-9' | head -c 24)
DEFAULT_ROOT_PASSWORD=$(openssl rand -base64 24 | tr -dc 'a-zA-Z0-9' | head -c 24)

# =============================================================================
# Pre-flight Checks
# =============================================================================
preflight_checks() {
    log_header "Pre-flight Checks"

    # Check if running as root or with sudo
    if [ "$EUID" -ne 0 ]; then
        log_warn "Not running as root. Some operations may require sudo."
    fi

    # Check Docker
    if ! command -v docker &> /dev/null; then
        log_error "Docker is not installed. Please install Docker first."
        exit 1
    fi
    log_info "✓ Docker installed: $(docker --version)"

    # Check Docker Compose
    if ! docker compose version &> /dev/null; then
        log_error "Docker Compose is not installed. Please install Docker Compose V2."
        exit 1
    fi
    log_info "✓ Docker Compose installed: $(docker compose version --short)"

    # Check if Docker daemon is running
    if ! docker info &> /dev/null; then
        log_error "Docker daemon is not running. Please start Docker."
        exit 1
    fi
    log_info "✓ Docker daemon is running"

    # Check if Swarm is initialized
    if ! docker node ls &> /dev/null 2>&1; then
        log_warn "Docker Swarm is not initialized."
        read -p "Initialize Swarm now? (y/N) " -n 1 -r
        echo
        if [[ $REPLY =~ ^[Yy]$ ]]; then
            init_swarm
        else
            log_error "Docker Swarm is required. Run: docker swarm init"
            exit 1
        fi
    fi
    log_info "✓ Docker Swarm is active"

    # Check node count
    NODE_COUNT=$(docker node ls --format "{{.ID}}" | wc -l)
    log_info "✓ Swarm nodes: $NODE_COUNT"
}

# =============================================================================
# Initialize Docker Swarm
# =============================================================================
init_swarm() {
    log_step "Initializing Docker Swarm..."

    # Get the primary IP address
    PRIMARY_IP=$(hostname -I | awk '{print $1}')

    docker swarm init --advertise-addr "$PRIMARY_IP" || {
        log_warn "Swarm may already be initialized or there's an issue with the network."
        log_info "If you're on a single node, try: docker swarm init --advertise-addr 127.0.0.1"
    }

    log_info "Swarm initialized with IP: $PRIMARY_IP"
}

# =============================================================================
# Create Environment Configuration
# =============================================================================
create_env_config() {
    log_header "Environment Configuration"

    ENV_FILE="$PROJECT_DIR/.env.swarm"

    if [ -f "$ENV_FILE" ] && [ "$AUTO_MODE" != "true" ]; then
        log_info "Found existing .env.swarm"
        read -p "Use existing configuration? (Y/n) " -n 1 -r
        echo
        if [[ ! $REPLY =~ ^[Nn]$ ]]; then
            source "$ENV_FILE"
            return
        fi
    fi

    if [ "$AUTO_MODE" == "true" ] && [ -f "$ENV_FILE" ]; then
        log_info "Using existing .env.swarm (auto mode)"
        source "$ENV_FILE"
        return
    fi

    # Interactive configuration
    log_step "Creating environment configuration..."

    echo ""
    read -p "Enter your domain (default: $DEFAULT_DOMAIN): " APP_DOMAIN
    APP_DOMAIN=${APP_DOMAIN:-$DEFAULT_DOMAIN}

    read -p "Enter admin email for SSL certificates: " ACME_EMAIL
    ACME_EMAIL=${ACME_EMAIL:-"admin@$APP_DOMAIN"}

    read -p "Enter database name (default: krayin): " DB_DATABASE
    DB_DATABASE=${DB_DATABASE:-krayin}

    read -p "Enter database user (default: krayin): " DB_USERNAME
    DB_USERNAME=${DB_USERNAME:-krayin}

    read -sp "Enter database password (leave empty for auto-generate): " DB_PASSWORD
    echo
    DB_PASSWORD=${DB_PASSWORD:-$DEFAULT_DB_PASSWORD}

    read -sp "Enter database root password (leave empty for auto-generate): " DB_ROOT_PASSWORD
    echo
    DB_ROOT_PASSWORD=${DB_ROOT_PASSWORD:-$DEFAULT_ROOT_PASSWORD}

    # Generate APP_KEY
    APP_KEY=$(openssl rand -base64 32)

    # Write .env.swarm
    cat > "$ENV_FILE" << EOF
# Generated by swarm-init.sh on $(date)
APP_NAME="Krayin CRM"
APP_ENV=production
APP_DEBUG=false
APP_VERSION=latest
APP_KEY=base64:$APP_KEY
APP_DOMAIN=$APP_DOMAIN
ADMIN_PATH=admin

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=$DB_DATABASE
DB_USERNAME=$DB_USERNAME
DB_PASSWORD=$DB_PASSWORD
DB_ROOT_PASSWORD=$DB_ROOT_PASSWORD

REDIS_HOST=redis
REDIS_PORT=6379
REDIS_PASSWORD=null

CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

ACME_EMAIL=$ACME_EMAIL
TRAEFIK_DASHBOARD_AUTH=admin:\$\$apr1\$\$auto\$\$generated

DOCKER_REGISTRY=
APP_REPLICAS=2
NGINX_REPLICAS=2
QUEUE_REPLICAS=2

DATA_PATH=/data/krayin
EOF

    log_info "Configuration saved to .env.swarm"
    source "$ENV_FILE"
}

# =============================================================================
# Create Docker Networks
# =============================================================================
create_networks() {
    log_header "Creating Docker Networks"

    # Create traefik-public network (external, for load balancer)
    if ! docker network inspect traefik-public &> /dev/null; then
        log_step "Creating traefik-public network..."
        docker network create \
            --driver=overlay \
            --attachable \
            traefik-public
        log_info "✓ Created traefik-public network"
    else
        log_info "✓ traefik-public network already exists"
    fi
}

# =============================================================================
# Create Docker Secrets
# =============================================================================
create_secrets() {
    log_header "Creating Docker Secrets"

    # APP_KEY
    if ! docker secret inspect app_key &> /dev/null; then
        log_step "Creating app_key secret..."
        echo -n "$APP_KEY" | docker secret create app_key -
        log_info "✓ Created app_key secret"
    else
        log_info "✓ app_key secret already exists"
    fi

    # DB_PASSWORD
    if ! docker secret inspect db_password &> /dev/null; then
        log_step "Creating db_password secret..."
        echo -n "$DB_PASSWORD" | docker secret create db_password -
        log_info "✓ Created db_password secret"
    else
        log_info "✓ db_password secret already exists"
    fi

    # DB_ROOT_PASSWORD
    if ! docker secret inspect db_root_password &> /dev/null; then
        log_step "Creating db_root_password secret..."
        echo -n "$DB_ROOT_PASSWORD" | docker secret create db_root_password -
        log_info "✓ Created db_root_password secret"
    else
        log_info "✓ db_root_password secret already exists"
    fi
}

# =============================================================================
# Create Data Directories
# =============================================================================
create_directories() {
    log_header "Creating Data Directories"

    DATA_PATH=${DATA_PATH:-/data/krayin}

    log_step "Creating directories in $DATA_PATH..."

    sudo mkdir -p "$DATA_PATH/mysql"
    sudo mkdir -p "$DATA_PATH/storage/app/public"
    sudo mkdir -p "$DATA_PATH/storage/framework/cache"
    sudo mkdir -p "$DATA_PATH/storage/framework/sessions"
    sudo mkdir -p "$DATA_PATH/storage/framework/views"
    sudo mkdir -p "$DATA_PATH/storage/logs"
    sudo mkdir -p "$DATA_PATH/backups"
    sudo mkdir -p "$DATA_PATH/themes"

    # Set permissions
    sudo chown -R 1000:1000 "$DATA_PATH/storage"
    sudo chmod -R 775 "$DATA_PATH/storage"

    log_info "✓ Created data directories"

    # Label node for database placement
    NODE_ID=$(docker node ls --format "{{.ID}}" | head -1)
    docker node update --label-add db=true "$NODE_ID" 2>/dev/null || true
    log_info "✓ Labeled node for database placement"
}

# =============================================================================
# Build Docker Image
# =============================================================================
build_image() {
    log_header "Building Docker Image"

    cd "$PROJECT_DIR"

    IMAGE_NAME="${DOCKER_REGISTRY:-}krayin/crm:${APP_VERSION:-latest}"

    log_step "Building image: $IMAGE_NAME"

    docker build \
        --target production \
        --tag "$IMAGE_NAME" \
        --build-arg BUILDKIT_INLINE_CACHE=1 \
        .

    log_info "✓ Image built: $IMAGE_NAME"
}

# =============================================================================
# Deploy Stack
# =============================================================================
deploy_stack() {
    log_header "Deploying Krayin CRM Stack"

    cd "$PROJECT_DIR"

    # Export environment variables for docker-stack.yml
    export APP_VERSION APP_DOMAIN DB_DATABASE DB_USERNAME DB_PASSWORD
    export DOCKER_REGISTRY ACME_EMAIL TRAEFIK_DASHBOARD_AUTH

    log_step "Deploying stack: $STACK_NAME"

    docker stack deploy \
        --compose-file docker-stack.yml \
        --with-registry-auth \
        "$STACK_NAME"

    log_info "✓ Stack deployed"

    # Wait for services to start
    log_step "Waiting for services to start (60s)..."
    sleep 10

    # Show status
    show_status
}

# =============================================================================
# Run Migrations
# =============================================================================
run_migrations() {
    log_header "Running Database Migrations"

    log_step "Waiting for database to be ready..."
    sleep 30

    # Find a running app container
    CONTAINER=""
    for i in {1..10}; do
        CONTAINER=$(docker ps -q -f "name=${STACK_NAME}_app" | head -1)
        if [ -n "$CONTAINER" ]; then
            break
        fi
        log_info "Waiting for app container... ($i/10)"
        sleep 10
    done

    if [ -z "$CONTAINER" ]; then
        log_error "No running app container found. Migrations will run on first request."
        return
    fi

    log_step "Running migrations..."
    docker exec "$CONTAINER" php artisan migrate --force || {
        log_warn "Migrations may have already been run or there's an issue."
    }

    log_step "Creating storage link..."
    docker exec "$CONTAINER" php artisan storage:link || true

    log_step "Clearing caches..."
    docker exec "$CONTAINER" php artisan config:cache
    docker exec "$CONTAINER" php artisan route:cache
    docker exec "$CONTAINER" php artisan view:cache

    log_info "✓ Migrations complete"
}

# =============================================================================
# Create Admin User
# =============================================================================
create_admin() {
    log_header "Creating Admin User"

    CONTAINER=$(docker ps -q -f "name=${STACK_NAME}_app" | head -1)

    if [ -z "$CONTAINER" ]; then
        log_warn "No running app container. Skip admin creation."
        return
    fi

    read -p "Create admin user now? (Y/n) " -n 1 -r
    echo

    if [[ $REPLY =~ ^[Nn]$ ]]; then
        log_info "Skipped admin creation. Create manually with:"
        log_info "  docker exec -it \$(docker ps -q -f 'name=${STACK_NAME}_app' | head -1) php artisan krayin:user"
        return
    fi

    docker exec -it "$CONTAINER" php artisan krayin:user || {
        log_warn "Could not create admin. Try manually later."
    }
}

# =============================================================================
# Show Status
# =============================================================================
show_status() {
    log_header "Stack Status"

    echo ""
    docker stack services "$STACK_NAME"
    echo ""

    log_info "Services are starting. Check status with:"
    log_info "  docker stack ps $STACK_NAME"
    echo ""
}

# =============================================================================
# Show Access Information
# =============================================================================
show_access_info() {
    log_header "Installation Complete!"

    echo -e "${GREEN}"
    echo "╔═══════════════════════════════════════════════════════════════╗"
    echo "║                    KRAYIN CRM INSTALLED                       ║"
    echo "╠═══════════════════════════════════════════════════════════════╣"
    echo "║                                                               ║"
    echo "║  CRM URL:      https://$APP_DOMAIN                            "
    echo "║  Admin Panel:  https://$APP_DOMAIN/admin                      "
    echo "║                                                               ║"
    echo "║  Database:     $DB_DATABASE                                   "
    echo "║  DB User:      $DB_USERNAME                                   "
    echo "║  DB Password:  (stored in Docker secret)                      "
    echo "║                                                               ║"
    echo "╠═══════════════════════════════════════════════════════════════╣"
    echo "║  USEFUL COMMANDS:                                             ║"
    echo "║                                                               ║"
    echo "║  Status:    docker stack services krayin                      ║"
    echo "║  Logs:      docker service logs -f krayin_app                 ║"
    echo "║  Scale:     docker service scale krayin_app=3                 ║"
    echo "║  Shell:     docker exec -it \$(docker ps -q -f              ║"
    echo "║             'name=krayin_app' | head -1) sh                   ║"
    echo "║  Remove:    docker stack rm krayin                            ║"
    echo "║                                                               ║"
    echo "╚═══════════════════════════════════════════════════════════════╝"
    echo -e "${NC}"
}

# =============================================================================
# Destroy Stack
# =============================================================================
destroy_stack() {
    log_header "Destroying Krayin CRM Stack"

    log_warn "This will remove:"
    log_warn "  - All services"
    log_warn "  - All secrets"
    log_warn "  - All configs"
    echo ""
    log_warn "Data in /data/krayin will NOT be deleted."
    echo ""

    read -p "Are you sure you want to destroy the stack? (y/N) " -n 1 -r
    echo

    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        log_info "Aborted."
        exit 0
    fi

    log_step "Removing stack..."
    docker stack rm "$STACK_NAME" 2>/dev/null || true

    sleep 10

    log_step "Removing secrets..."
    docker secret rm app_key db_password db_root_password 2>/dev/null || true

    log_info "✓ Stack destroyed"
    log_info "To remove data: sudo rm -rf /data/krayin"
}

# =============================================================================
# Main
# =============================================================================
main() {
    cd "$PROJECT_DIR"

    case "${1:-}" in
        --destroy|--remove|-d)
            destroy_stack
            exit 0
            ;;
        --auto|-a)
            AUTO_MODE=true
            ;;
        --help|-h)
            echo "Usage: $0 [options]"
            echo ""
            echo "Options:"
            echo "  --auto, -a     Auto-install with defaults from .env.swarm"
            echo "  --destroy, -d  Remove the entire stack"
            echo "  --help, -h     Show this help"
            exit 0
            ;;
    esac

    log_header "Krayin CRM - Docker Swarm Installation"

    preflight_checks
    create_env_config
    create_networks
    create_secrets
    create_directories
    build_image
    deploy_stack

    if [ "$AUTO_MODE" != "true" ]; then
        run_migrations
        create_admin
    fi

    show_access_info
}

main "$@"
