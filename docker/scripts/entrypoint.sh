#!/bin/bash
# =============================================================================
# Krayin CRM - Docker Entrypoint Script
# =============================================================================
set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

log_info() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

log_warn() {
    echo -e "${YELLOW}[WARN]${NC} $1"
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# =============================================================================
# Wait for dependencies
# =============================================================================
wait_for_db() {
    log_info "Waiting for MySQL to be ready..."

    MAX_TRIES=30
    TRIES=0

    while [ $TRIES -lt $MAX_TRIES ]; do
        if php -r "
            \$host = getenv('DB_HOST') ?: 'mysql';
            \$port = getenv('DB_PORT') ?: '3306';
            \$conn = @fsockopen(\$host, \$port, \$errno, \$errstr, 5);
            if (\$conn) { fclose(\$conn); exit(0); }
            exit(1);
        " 2>/dev/null; then
            log_info "MySQL is ready!"
            return 0
        fi

        TRIES=$((TRIES + 1))
        log_warn "MySQL not ready yet... (attempt $TRIES/$MAX_TRIES)"
        sleep 2
    done

    log_error "MySQL did not become ready in time"
    return 1
}

wait_for_redis() {
    if [ -n "$REDIS_HOST" ]; then
        log_info "Waiting for Redis to be ready..."

        MAX_TRIES=15
        TRIES=0

        while [ $TRIES -lt $MAX_TRIES ]; do
            if php -r "
                \$host = getenv('REDIS_HOST') ?: 'redis';
                \$port = getenv('REDIS_PORT') ?: '6379';
                \$conn = @fsockopen(\$host, \$port, \$errno, \$errstr, 5);
                if (\$conn) { fclose(\$conn); exit(0); }
                exit(1);
            " 2>/dev/null; then
                log_info "Redis is ready!"
                return 0
            fi

            TRIES=$((TRIES + 1))
            log_warn "Redis not ready yet... (attempt $TRIES/$MAX_TRIES)"
            sleep 1
        done

        log_warn "Redis did not become ready - continuing anyway"
    fi
}

# =============================================================================
# Laravel initialization
# =============================================================================
init_laravel() {
    log_info "Initializing Laravel application..."

    # Generate app key if not set
    if [ -z "$APP_KEY" ] && [ ! -f ".env" ]; then
        log_warn "APP_KEY not set, generating..."
        cp .env.example .env 2>/dev/null || true
        php artisan key:generate --force
    fi

    # Create storage link if not exists
    if [ ! -L "public/storage" ]; then
        log_info "Creating storage symlink..."
        php artisan storage:link 2>/dev/null || true
    fi

    # Ensure directories exist with correct permissions
    log_info "Setting up directories..."
    mkdir -p storage/app/public \
             storage/framework/cache/data \
             storage/framework/sessions \
             storage/framework/views \
             storage/logs \
             bootstrap/cache

    # Fix permissions if running as root
    if [ "$(id -u)" = "0" ]; then
        chown -R krayin:krayin storage bootstrap/cache
    fi
    chmod -R 775 storage bootstrap/cache
}

# =============================================================================
# Cache optimization (production only)
# =============================================================================
optimize_laravel() {
    if [ "$APP_ENV" = "production" ]; then
        log_info "Optimizing Laravel for production..."

        # Clear any stale caches
        php artisan config:clear 2>/dev/null || true
        php artisan route:clear 2>/dev/null || true
        php artisan view:clear 2>/dev/null || true

        # Build fresh caches
        php artisan config:cache
        php artisan route:cache
        php artisan view:cache

        log_info "Optimization complete!"
    else
        log_info "Development mode - skipping optimization"
        # Clear caches for development
        php artisan config:clear 2>/dev/null || true
        php artisan route:clear 2>/dev/null || true
        php artisan view:clear 2>/dev/null || true
    fi
}

# =============================================================================
# Database migrations (optional, controlled by env)
# =============================================================================
run_migrations() {
    if [ "$RUN_MIGRATIONS" = "true" ] || [ "$RUN_MIGRATIONS" = "1" ]; then
        log_info "Running database migrations..."
        php artisan migrate --force
        log_info "Migrations complete!"
    else
        log_info "Skipping migrations (RUN_MIGRATIONS not set)"
    fi
}

# =============================================================================
# Main execution
# =============================================================================
main() {
    log_info "=========================================="
    log_info "Krayin CRM - Container Starting"
    log_info "Environment: ${APP_ENV:-local}"
    log_info "=========================================="

    cd /var/www/html

    # Wait for dependencies
    wait_for_db
    wait_for_redis

    # Initialize Laravel
    init_laravel

    # Run migrations if enabled
    run_migrations

    # Optimize for production
    optimize_laravel

    log_info "=========================================="
    log_info "Krayin CRM - Ready!"
    log_info "=========================================="

    # Execute the main command
    exec "$@"
}

# Run main function with all arguments
main "$@"
