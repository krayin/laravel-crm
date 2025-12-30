#!/bin/bash
# =============================================================================
# Krayin CRM - Docker Swarm Deploy Script
# =============================================================================
# Usage:
#   ./deploy-swarm.sh                    # Deploy/Update stack
#   ./deploy-swarm.sh --build            # Build and deploy
#   ./deploy-swarm.sh --rollback         # Rollback to previous version
#   ./deploy-swarm.sh --status           # Show stack status
#   ./deploy-swarm.sh --logs [service]   # Show logs
#   ./deploy-swarm.sh --scale app=3      # Scale service
#   ./deploy-swarm.sh --remove           # Remove entire stack
# =============================================================================
set -e

# Configuration
STACK_NAME="${STACK_NAME:-krayin}"
DOCKER_REGISTRY="${DOCKER_REGISTRY:-}"
APP_VERSION="${APP_VERSION:-latest}"
IMAGE_NAME="${DOCKER_REGISTRY}krayin/crm:${APP_VERSION}"

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

log_info() { echo -e "${GREEN}[INFO]${NC} $1"; }
log_warn() { echo -e "${YELLOW}[WARN]${NC} $1"; }
log_error() { echo -e "${RED}[ERROR]${NC} $1"; }
log_step() { echo -e "${BLUE}[STEP]${NC} $1"; }

# =============================================================================
# Pre-flight checks
# =============================================================================
preflight_check() {
    log_step "Running pre-flight checks..."

    # Check if Docker is running
    if ! docker info >/dev/null 2>&1; then
        log_error "Docker is not running or not accessible"
        exit 1
    fi

    # Check if node is a swarm manager
    if ! docker node ls >/dev/null 2>&1; then
        log_error "This node is not a swarm manager. Run: docker swarm init"
        exit 1
    fi

    # Check if .env.swarm exists
    if [ ! -f ".env.swarm" ]; then
        log_warn ".env.swarm not found. Creating from template..."
        create_env_template
    fi

    # Load environment variables
    set -a
    source .env.swarm
    set +a

    log_info "Pre-flight checks passed!"
}

# =============================================================================
# Create environment template
# =============================================================================
create_env_template() {
    cat > .env.swarm << 'EOF'
# =============================================================================
# Krayin CRM - Docker Swarm Environment Variables
# =============================================================================

# Application
APP_VERSION=latest
APP_DOMAIN=crm.example.com
APP_KEY=base64:your-app-key-here

# Database
DB_DATABASE=krayin
DB_USERNAME=krayin
DB_PASSWORD=your-secure-password

# Redis (optional - set to null if not using auth)
REDIS_PASSWORD=null

# Docker Registry (optional)
DOCKER_REGISTRY=

# Traefik (if using)
ACME_EMAIL=admin@example.com
TRAEFIK_DASHBOARD_AUTH=admin:$$apr1$$xyz

# Scaling
APP_REPLICAS=2
QUEUE_REPLICAS=2
EOF
    log_warn "Please edit .env.swarm with your settings before deploying!"
}

# =============================================================================
# Create secrets
# =============================================================================
create_secrets() {
    log_step "Creating Docker secrets..."

    # Check if secrets exist
    if ! docker secret inspect app_key >/dev/null 2>&1; then
        log_info "Creating app_key secret..."
        echo -n "$APP_KEY" | docker secret create app_key -
    else
        log_info "Secret 'app_key' already exists"
    fi

    if ! docker secret inspect db_password >/dev/null 2>&1; then
        log_info "Creating db_password secret..."
        echo -n "$DB_PASSWORD" | docker secret create db_password -
    else
        log_info "Secret 'db_password' already exists"
    fi

    if ! docker secret inspect db_root_password >/dev/null 2>&1; then
        log_info "Creating db_root_password secret..."
        echo -n "${DB_ROOT_PASSWORD:-$DB_PASSWORD}" | docker secret create db_root_password -
    else
        log_info "Secret 'db_root_password' already exists"
    fi

    log_info "Secrets ready!"
}

# =============================================================================
# Create networks
# =============================================================================
create_networks() {
    log_step "Creating Docker networks..."

    if ! docker network inspect traefik-public >/dev/null 2>&1; then
        log_info "Creating traefik-public network..."
        docker network create --driver=overlay --attachable traefik-public
    else
        log_info "Network 'traefik-public' already exists"
    fi

    log_info "Networks ready!"
}

# =============================================================================
# Create data directories
# =============================================================================
create_directories() {
    log_step "Creating data directories..."

    # These should be on shared storage in multi-node setup
    sudo mkdir -p /data/krayin/mysql
    sudo mkdir -p /data/krayin/storage
    sudo chown -R 1000:1000 /data/krayin/storage

    log_info "Directories ready!"
}

# =============================================================================
# Build image
# =============================================================================
build_image() {
    log_step "Building Docker image..."

    docker build \
        --target production \
        --tag "$IMAGE_NAME" \
        --cache-from "$IMAGE_NAME" \
        --build-arg BUILDKIT_INLINE_CACHE=1 \
        .

    if [ -n "$DOCKER_REGISTRY" ]; then
        log_info "Pushing image to registry..."
        docker push "$IMAGE_NAME"
    fi

    log_info "Image built: $IMAGE_NAME"
}

# =============================================================================
# Deploy stack
# =============================================================================
deploy_stack() {
    log_step "Deploying stack..."

    # Export variables for docker-stack.yml
    export DOCKER_REGISTRY APP_VERSION APP_DOMAIN DB_DATABASE DB_USERNAME

    docker stack deploy \
        --compose-file docker-stack.yml \
        --with-registry-auth \
        "$STACK_NAME"

    log_info "Stack deployed!"
    log_info "Waiting for services to stabilize..."
    sleep 10
    show_status
}

# =============================================================================
# Update services (rolling update)
# =============================================================================
update_services() {
    log_step "Performing rolling update..."

    # Update app service
    docker service update \
        --image "$IMAGE_NAME" \
        --update-parallelism 1 \
        --update-delay 30s \
        --update-failure-action rollback \
        "${STACK_NAME}_app"

    # Update queue workers
    docker service update \
        --image "$IMAGE_NAME" \
        --update-parallelism 1 \
        --update-delay 10s \
        "${STACK_NAME}_queue"

    # Update scheduler
    docker service update \
        --image "$IMAGE_NAME" \
        "${STACK_NAME}_scheduler"

    log_info "Rolling update complete!"
}

# =============================================================================
# Rollback
# =============================================================================
rollback() {
    log_step "Rolling back services..."

    docker service rollback "${STACK_NAME}_app"
    docker service rollback "${STACK_NAME}_queue"
    docker service rollback "${STACK_NAME}_scheduler"

    log_info "Rollback complete!"
}

# =============================================================================
# Show status
# =============================================================================
show_status() {
    log_step "Stack Status:"
    echo ""
    docker stack services "$STACK_NAME"
    echo ""
    log_step "Service Tasks:"
    docker stack ps "$STACK_NAME" --no-trunc 2>/dev/null | head -20
}

# =============================================================================
# Show logs
# =============================================================================
show_logs() {
    local service="${1:-app}"
    log_step "Showing logs for ${STACK_NAME}_${service}..."
    docker service logs -f --tail 100 "${STACK_NAME}_${service}"
}

# =============================================================================
# Scale service
# =============================================================================
scale_service() {
    local scale_spec="$1"
    log_step "Scaling service: $scale_spec"
    docker service scale "${STACK_NAME}_${scale_spec}"
}

# =============================================================================
# Remove stack
# =============================================================================
remove_stack() {
    log_warn "This will remove the entire stack!"
    read -p "Are you sure? (y/N) " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        log_step "Removing stack..."
        docker stack rm "$STACK_NAME"
        log_info "Stack removed!"
    else
        log_info "Aborted."
    fi
}

# =============================================================================
# Clear theme cache (run inside container)
# =============================================================================
clear_theme_cache() {
    log_step "Clearing theme cache..."

    # Get first running app container
    CONTAINER=$(docker ps -q -f "name=${STACK_NAME}_app" | head -1)

    if [ -n "$CONTAINER" ]; then
        docker exec "$CONTAINER" php artisan cache:clear
        docker exec "$CONTAINER" php artisan config:clear
        docker exec "$CONTAINER" php artisan view:clear
        log_info "Theme cache cleared!"
    else
        log_error "No running app container found"
        exit 1
    fi
}

# =============================================================================
# Main
# =============================================================================
main() {
    cd "$(dirname "$0")/../.."

    case "${1:-deploy}" in
        --build|-b)
            preflight_check
            build_image
            deploy_stack
            ;;
        --rollback|-r)
            rollback
            ;;
        --status|-s)
            show_status
            ;;
        --logs|-l)
            show_logs "${2:-app}"
            ;;
        --scale)
            scale_service "$2"
            ;;
        --remove|--rm)
            remove_stack
            ;;
        --clear-cache|--cc)
            clear_theme_cache
            ;;
        --init)
            preflight_check
            create_secrets
            create_networks
            create_directories
            log_info "Initialization complete! Run: ./deploy-swarm.sh --build"
            ;;
        deploy|--deploy|-d|"")
            preflight_check
            create_secrets
            create_networks
            deploy_stack
            ;;
        *)
            echo "Usage: $0 [command]"
            echo ""
            echo "Commands:"
            echo "  --init          Initialize secrets, networks, directories"
            echo "  --build, -b     Build image and deploy"
            echo "  deploy, -d      Deploy/update stack (default)"
            echo "  --rollback, -r  Rollback to previous version"
            echo "  --status, -s    Show stack status"
            echo "  --logs, -l      Show service logs (default: app)"
            echo "  --scale         Scale service (e.g., --scale app=3)"
            echo "  --clear-cache   Clear theme/Laravel cache"
            echo "  --remove        Remove entire stack"
            exit 1
            ;;
    esac
}

main "$@"
