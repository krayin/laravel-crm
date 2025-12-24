#!/bin/bash
# =============================================================================
# Krayin CRM - Docker Healthcheck Script
# =============================================================================

set -e

# Configuration
PHP_FPM_HOST="${PHP_FPM_HOST:-127.0.0.1}"
PHP_FPM_PORT="${PHP_FPM_PORT:-9000}"
HEALTH_CHECK_PATH="${HEALTH_CHECK_PATH:-/var/www/html/public/index.php}"

# =============================================================================
# Check PHP-FPM is running
# =============================================================================
check_php_fpm() {
    # Check if PHP-FPM master process exists
    if ! pgrep -x "php-fpm" > /dev/null 2>&1; then
        echo "UNHEALTHY: PHP-FPM process not running"
        exit 1
    fi

    # Check if PHP-FPM is listening on the port
    if ! nc -z "$PHP_FPM_HOST" "$PHP_FPM_PORT" 2>/dev/null; then
        # Fallback: try using PHP to check
        if ! php -r "
            \$conn = @fsockopen('$PHP_FPM_HOST', $PHP_FPM_PORT, \$errno, \$errstr, 5);
            if (\$conn) { fclose(\$conn); exit(0); }
            exit(1);
        " 2>/dev/null; then
            echo "UNHEALTHY: PHP-FPM not listening on port $PHP_FPM_PORT"
            exit 1
        fi
    fi
}

# =============================================================================
# Check Laravel application health
# =============================================================================
check_laravel() {
    cd /var/www/html

    # Check if artisan exists
    if [ ! -f "artisan" ]; then
        echo "UNHEALTHY: artisan file not found"
        exit 1
    fi

    # Run Laravel's built-in health check (if available)
    if php artisan list 2>/dev/null | grep -q "health"; then
        if ! php artisan health:check 2>/dev/null; then
            echo "UNHEALTHY: Laravel health check failed"
            exit 1
        fi
    fi

    # Basic PHP execution test
    if ! php -r "echo 'OK';" > /dev/null 2>&1; then
        echo "UNHEALTHY: PHP execution failed"
        exit 1
    fi
}

# =============================================================================
# Check required directories
# =============================================================================
check_directories() {
    REQUIRED_DIRS=(
        "/var/www/html/storage/framework/cache"
        "/var/www/html/storage/framework/sessions"
        "/var/www/html/storage/framework/views"
        "/var/www/html/storage/logs"
        "/var/www/html/bootstrap/cache"
    )

    for dir in "${REQUIRED_DIRS[@]}"; do
        if [ ! -d "$dir" ] || [ ! -w "$dir" ]; then
            echo "UNHEALTHY: Directory $dir is not writable"
            exit 1
        fi
    done
}

# =============================================================================
# Check database connectivity
# =============================================================================
check_database() {
    cd /var/www/html

    # Skip if DB_HOST not set
    if [ -z "$DB_HOST" ]; then
        return 0
    fi

    # Quick connection test
    if ! php -r "
        try {
            \$host = getenv('DB_HOST');
            \$port = getenv('DB_PORT') ?: '3306';
            \$database = getenv('DB_DATABASE');
            \$username = getenv('DB_USERNAME');
            \$password = getenv('DB_PASSWORD');

            \$dsn = \"mysql:host=\$host;port=\$port;dbname=\$database\";
            \$pdo = new PDO(\$dsn, \$username, \$password, [
                PDO::ATTR_TIMEOUT => 5,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
            \$pdo->query('SELECT 1');
            exit(0);
        } catch (Exception \$e) {
            exit(1);
        }
    " 2>/dev/null; then
        echo "UNHEALTHY: Database connection failed"
        exit 1
    fi
}

# =============================================================================
# Check Redis connectivity
# =============================================================================
check_redis() {
    # Skip if Redis not configured
    if [ -z "$REDIS_HOST" ]; then
        return 0
    fi

    # Quick connection test
    if ! php -r "
        \$host = getenv('REDIS_HOST');
        \$port = getenv('REDIS_PORT') ?: '6379';
        \$conn = @fsockopen(\$host, \$port, \$errno, \$errstr, 5);
        if (\$conn) { fclose(\$conn); exit(0); }
        exit(1);
    " 2>/dev/null; then
        echo "WARNING: Redis connection failed (non-fatal)"
        # Don't exit - Redis might be optional
    fi
}

# =============================================================================
# Check disk space
# =============================================================================
check_disk_space() {
    # Get available space in MB
    AVAILABLE=$(df -m /var/www/html 2>/dev/null | awk 'NR==2 {print $4}')

    if [ -n "$AVAILABLE" ] && [ "$AVAILABLE" -lt 100 ]; then
        echo "UNHEALTHY: Low disk space (${AVAILABLE}MB available)"
        exit 1
    fi
}

# =============================================================================
# Main healthcheck
# =============================================================================
main() {
    # Core checks (must pass)
    check_php_fpm
    check_directories

    # Application checks
    check_laravel

    # Optional connectivity checks
    check_database
    check_redis

    # Resource checks
    check_disk_space

    echo "HEALTHY: All checks passed"
    exit 0
}

# Run healthcheck
main
