#!/bin/bash

###############################################################################
# STYLEUS CRM - Automated Deployment Script for DigitalOcean
# Domain: crm.styleus.us
# Server IP: 45.55.62.115
###############################################################################

set -e

echo "🚀 Starting STYLEUS CRM deployment..."

# Configuration
DOMAIN="crm.styleus.us"
APP_DIR="/var/www/styleus"
DB_NAME="styleuscrm_prod"
DB_USER="styleuscrm_user"
DB_PASS=$(openssl rand -base64 32)
ADMIN_EMAIL="admin@styleus.us"
ADMIN_PASS="Pobeda88!88"

echo "📦 Step 1/8: Updating system packages..."
apt update && apt upgrade -y

echo "📦 Step 2/8: Installing required packages..."
apt install -y nginx mysql-server php8.3-fpm php8.3-mysql php8.3-mbstring \
  php8.3-xml php8.3-curl php8.3-zip php8.3-gd php8.3-bcmath php8.3-soap \
  php8.3-intl php8.3-readline php8.3-cli redis-server supervisor git unzip \
  certbot python3-certbot-nginx curl

echo "📦 Step 3/8: Installing Composer..."
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
chmod +x /usr/local/bin/composer

echo "📦 Step 4/8: Installing Node.js & npm..."
curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
apt install -y nodejs

echo "🗄️  Step 5/8: Configuring MySQL..."
mysql -e "CREATE DATABASE ${DB_NAME} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -e "CREATE USER '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';"
mysql -e "GRANT ALL PRIVILEGES ON ${DB_NAME}.* TO '${DB_USER}'@'localhost';"
mysql -e "FLUSH PRIVILEGES;"

echo "📁 Step 6/8: Setting up application directory..."
mkdir -p ${APP_DIR}
cd ${APP_DIR}

# Note: You'll need to upload your code here
# For now, we'll create a placeholder
echo "⚠️  IMPORTANT: Upload your application code to ${APP_DIR}"
echo "   You can use: scp -r /path/to/styleuscrm root@45.55.62.115:${APP_DIR}"

# Create .env file
cat > ${APP_DIR}/.env << EOF
APP_NAME=STYLEUS
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://${DOMAIN}
APP_TIMEZONE=America/New_York
APP_LOCALE=en
APP_CURRENCY=USD

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=${DB_NAME}
DB_USERNAME=${DB_USER}
DB_PASSWORD=${DB_PASS}
DB_PREFIX=

BROADCAST_DRIVER=log
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
SESSION_LIFETIME=120

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=${ADMIN_EMAIL}
MAIL_FROM_NAME="\${APP_NAME}"
EOF

echo "🔒 Step 7/8: Configuring Nginx..."
cat > /etc/nginx/sites-available/styleuscrm << 'NGINX_EOF'
server {
    listen 80;
    listen [::]:80;
    server_name crm.styleus.us;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name crm.styleus.us;
    
    root /var/www/styleuscrm/public;
    index index.php index.html;

    # SSL Configuration (will be added by Certbot)
    
    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    
    # Logging
    access_log /var/log/nginx/styleuscrm-access.log;
    error_log /var/log/nginx/styleuscrm-error.log;
    
    # Application
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
    
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
NGINX_EOF

ln -sf /etc/nginx/sites-available/styleuscrm /etc/nginx/sites-enabled/
rm -f /etc/nginx/sites-enabled/default
nginx -t

echo "🔐 Step 8/8: Setting up SSL certificate..."
echo "⚠️  Make sure DNS for crm.styleus.us is pointing to this server before continuing!"
read -p "Press Enter when DNS is ready (or Ctrl+C to cancel)..."

certbot --nginx -d ${DOMAIN} --non-interactive --agree-tos --email ${ADMIN_EMAIL} --redirect

echo "⚙️  Configuring Supervisor for queue workers..."
cat > /etc/supervisor/conf.d/styleuscrm-worker.conf << 'SUPERVISOR_EOF'
[program:styleuscrm-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/styleuscrm/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/styleuscrm/storage/logs/worker.log
stopwaitsecs=3600
SUPERVISOR_EOF

supervisorctl reread
supervisorctl update

echo "🔄 Setting up cron jobs..."
(crontab -l 2>/dev/null; echo "* * * * * cd ${APP_DIR} && php artisan schedule:run >> /dev/null 2>&1") | crontab -

echo "🔒 Setting permissions..."
chown -R www-data:www-data ${APP_DIR}
chmod -R 755 ${APP_DIR}
chmod -R 775 ${APP_DIR}/storage 2>/dev/null || true
chmod -R 775 ${APP_DIR}/bootstrap/cache 2>/dev/null || true

echo "🔥 Configuring firewall..."
ufw allow 22
ufw allow 80
ufw allow 443
ufw --force enable

echo ""
echo "✅ Server setup complete!"
echo ""
echo "📋 IMPORTANT INFORMATION:"
echo "================================"
echo "Domain: https://${DOMAIN}"
echo "Database Name: ${DB_NAME}"
echo "Database User: ${DB_USER}"
echo "Database Password: ${DB_PASS}"
echo ""
echo "⚠️  SAVE THIS PASSWORD SECURELY!"
echo ""
echo "📝 Next steps:"
echo "1. Upload your application code to: ${APP_DIR}"
echo "2. Run: cd ${APP_DIR} && composer install --no-dev"
echo "3. Run: npm ci --production && npm run build"
echo "4. Run: php artisan key:generate"
echo "5. Run: php artisan migrate --force"
echo "6. Run: php artisan krayin-crm:install"
echo ""
echo "🚀 After completing these steps, visit: https://${DOMAIN}"
echo ""

# Save credentials to file
cat > /root/styleuscrm-credentials.txt << EOF
STYLEUS CRM Credentials
=======================
Domain: https://${DOMAIN}
Server IP: 45.55.62.115

Database:
- Name: ${DB_NAME}
- User: ${DB_USER}
- Password: ${DB_PASS}

Admin:
- Email: ${ADMIN_EMAIL}
- Password: ${ADMIN_PASS}

Application Directory: ${APP_DIR}

Generated: $(date)
EOF

echo "💾 Credentials saved to: /root/styleuscrm-credentials.txt"
