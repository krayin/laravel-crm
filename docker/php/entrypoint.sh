#!/usr/bin/env bash
set -e

# 1. Ensure the directory structure exists in the named volume
mkdir -p /var/www/html/storage/logs
mkdir -p /var/www/html/storage/framework/{sessions,views,cache}
mkdir -p /var/www/html/bootstrap/cache

# 2. Fix permissions for the internal www-data user
# This works flawlessly with Named Volumes
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# 3. Wait for MySQL to be ready
echo "Checking database connection..."
max_tries=30
count=0
# We use the env variables injected by docker-compose
until php -r "new PDO('mysql:host=$DB_HOST;port=$DB_PORT;dbname=$DB_DATABASE', '$DB_USERNAME', '$DB_PASSWORD');" > /dev/null 2>&1; do
    sleep 2
    count=$((count+1))
    if [ $count -gt $max_tries ]; then
        echo "Error: Database not reachable."
        exit 1
    fi
    echo "Waiting for database..."
done

# 4. Production-ready commands
if [ -f /var/www/html/artisan ]; then
    # Create storage link if missing
    php artisan storage:link --force || true
    
    # Run migrations automatically (The Production Way)
    echo "Running migrations..."
    php artisan migrate --force
fi

echo "Starting Application..."
exec "$@"