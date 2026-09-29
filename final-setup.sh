#!/bin/bash

###############################################################################
# STYLEUS CRM - Final Setup Script
# Завершает установку CRM после развертывания кода
###############################################################################

set -e

echo "🚀 Starting STYLEUS CRM final setup..."

cd /var/www/styleus

# Update .env file
echo "📝 Updating .env configuration..."
sed -i "s/APP_NAME=.*/APP_NAME=STYLEUS/" .env
sed -i "s/APP_ENV=.*/APP_ENV=production/" .env
sed -i "s/APP_DEBUG=.*/APP_DEBUG=false/" .env
sed -i "s|APP_URL=.*|APP_URL=https://crm.styleus.us|" .env
sed -i "s/APP_TIMEZONE=.*/APP_TIMEZONE=America\/New_York/" .env
sed -i "s/DB_DATABASE=.*/DB_DATABASE=styleuscrm_prod/" .env
sed -i "s/DB_USERNAME=.*/DB_USERNAME=styleuscrm_user/" .env
sed -i "s/DB_PASSWORD=.*/DB_PASSWORD=Pobeda8888/" .env

echo "✅ .env updated!"

# Clear config cache
echo "🔄 Clearing configuration cache..."
php artisan config:clear

# Run migrations
echo "🗄️  Running database migrations..."
php artisan migrate --force

echo "✅ Migrations complete!"

# Set permissions
echo "🔒 Setting correct permissions..."
chown -R www-data:www-data /var/www/styleus
chmod -R 755 /var/www/styleus
chmod -R 775 /var/www/styleus/storage
chmod -R 775 /var/www/styleus/bootstrap/cache

echo "✅ Permissions set!"

# Restart services
echo "🔄 Restarting services..."
systemctl restart php8.3-fpm
systemctl restart nginx

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "✅ Setup complete!"
echo ""
echo "📋 Next step: Run Krayin CRM installer"
echo "   Command: php artisan krayin-crm:install"
echo ""
echo "   Use these details:"
echo "   - Application name: STYLEUS"
echo "   - Application URL: https://crm.styleus.us"
echo "   - Default Locale: English"
echo "   - Default Currency: USD"
echo "   - Admin Name: svmod"
echo "   - Admin Email: svmod@styleus.us"
echo "   - Admin Password: Idlikepobeda88"
echo ""
echo "🌐 After installation, visit: https://crm.styleus.us"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
