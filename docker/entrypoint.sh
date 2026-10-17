#!/bin/bash
set -e

echo ">>> Starting Krayin CRM..."

# Generate APP_KEY if not set
if [ -z "$APP_KEY" ]; then
    echo ">>> Generating APP_KEY..."
    php artisan key:generate --force
fi

# Wait for database to be ready
echo ">>> Waiting for database..."
until php artisan migrate --force 2>/dev/null || [ $? -eq 255 ]; do
    echo "Waiting for MySQL..."
    sleep 2
done

# Run migrations
echo ">>> Running migrations..."
php artisan migrate --force --seed || true

# Clear and rebuild cache
echo ">>> Clearing cache..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Optimize for production (only if APP_KEY exists)
if [ -n "$APP_KEY" ]; then
    echo ">>> Optimizing for production..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    php artisan optimize
fi

echo ">>> Setup complete!"

# Execute the given command or serve the app
exec "$@"