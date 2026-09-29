# STYLEUS CRM - Production Deployment & Integration Guide

## 📋 Table of Contents

1. [Server Requirements & Setup](#server-requirements--setup)
2. [Domain & SSL Configuration](#domain--ssl-configuration)
3. [Application Deployment](#application-deployment)
4. [Google Workspace Integration](#google-workspace-integration)
5. [Communication Platforms](#communication-platforms)
6. [AI Services Integration](#ai-services-integration)
7. [Marketing & Sales Tools](#marketing--sales-tools)
8. [Payment Processing](#payment-processing)
9. [File Storage & Backup](#file-storage--backup)
10. [Security & Monitoring](#security--monitoring)

---

## Server Requirements & Setup

### Recommended Hosting Providers

| Provider | Recommended Plan | Monthly Cost | Notes |
|----------|-----------------|--------------|-------|
| **DigitalOcean** | Droplet 4GB RAM | $24 | Best for Laravel apps |
| **AWS Lightsail** | 2GB RAM | $20 | Easy to scale |
| **Linode** | Dedicated 4GB | $24 | Great performance |
| **Vultr** | Cloud Compute 4GB | $24 | Good global coverage |

### Server Specifications (Minimum)

```yaml
Operating System: Ubuntu 22.04 LTS
CPU: 2 cores
RAM: 4GB
Storage: 80GB SSD
Bandwidth: 4TB/month
```

### Initial Server Setup

```bash
# 1. Update system
sudo apt update && sudo apt upgrade -y

# 2. Install required packages
sudo apt install -y nginx mysql-server php8.3-fpm php8.3-mysql \
  php8.3-mbstring php8.3-xml php8.3-curl php8.3-zip php8.3-gd \
  php8.3-bcmath php8.3-soap php8.3-intl redis-server supervisor \
  certbot python3-certbot-nginx git unzip

# 3. Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer

# 4. Install Node.js & npm
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs

# 5. Secure MySQL
sudo mysql_secure_installation
```

---

## Domain & SSL Configuration

### 1. Register Domain

**Recommended Registrars:**
- **Namecheap** (https://namecheap.com) - $8-15/year
- **Google Domains** (https://domains.google) - $12/year
- **Cloudflare Registrar** (https://cloudflare.com) - At cost pricing

### 2. DNS Configuration

Point your domain to your server IP:

```
A Record:
  Name: @
  Value: YOUR_SERVER_IP
  TTL: 3600

A Record:
  Name: www
  Value: YOUR_SERVER_IP
  TTL: 3600

CNAME Record:
  Name: admin
  Value: yourdomain.com
  TTL: 3600
```

### 3. SSL Certificate (Free with Let's Encrypt)

```bash
# Install SSL certificate
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com

# Auto-renewal (cron job)
sudo certbot renew --dry-run
```

---

## Application Deployment

### 1. Clone Repository

```bash
# Create application directory
sudo mkdir -p /var/www/styleuscrm
sudo chown -R $USER:$USER /var/www/styleuscrm

# Clone your repository
cd /var/www
git clone YOUR_REPOSITORY_URL styleuscrm
cd styleuscrm
```

### 2. Configure Environment

```bash
# Copy environment file
cp .env.example .env

# Edit .env with production settings
nano .env
```

**Production .env Configuration:**

```env
APP_NAME=STYLEUS
APP_ENV=production
APP_KEY=  # Will generate
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=styleuscrm_prod
DB_USERNAME=styleuscrm_user
DB_PASSWORD=STRONG_PASSWORD_HERE

# Redis (for caching & queues)
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# Mail Configuration (using Gmail SMTP)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"

# File Storage
FILESYSTEM_DISK=google
GOOGLE_DRIVE_CLIENT_ID=
GOOGLE_DRIVE_CLIENT_SECRET=
GOOGLE_DRIVE_REFRESH_TOKEN=
GOOGLE_DRIVE_FOLDER_ID=
```

### 3. Install Dependencies & Deploy

```bash
# Install PHP dependencies (production only)
composer install --no-dev --optimize-autoloader

# Install Node dependencies
npm ci --production

# Build frontend assets
npm run build

# Generate application key
php artisan key:generate

# Create database
sudo mysql -u root -p
```

```sql
CREATE DATABASE styleuscrm_prod CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'styleuscrm_user'@'localhost' IDENTIFIED BY 'STRONG_PASSWORD_HERE';
GRANT ALL PRIVILEGES ON styleuscrm_prod.* TO 'styleuscrm_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

```bash
# Run migrations
php artisan migrate --force

# Optimize application
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set permissions
sudo chown -R www-data:www-data /var/www/styleuscrm
sudo chmod -R 755 /var/www/styleuscrm
sudo chmod -R 775 /var/www/styleuscrm/storage
sudo chmod -R 775 /var/www/styleuscrm/bootstrap/cache
```

### 4. Nginx Configuration

Create `/etc/nginx/sites-available/styleuscrm`:

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name yourdomain.com www.yourdomain.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name yourdomain.com www.yourdomain.com;
    
    root /var/www/styleuscrm/public;
    index index.php index.html;

    # SSL Configuration (managed by Certbot)
    ssl_certificate /etc/letsencrypt/live/yourdomain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/yourdomain.com/privkey.pem;
    
    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    
    # Application
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

```bash
# Enable site
sudo ln -s /etc/nginx/sites-available/styleuscrm /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

### 5. Queue Worker (Supervisor)

Create `/etc/supervisor/conf.d/styleuscrm-worker.conf`:

```ini
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
```

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start styleuscrm-worker:*
```

---

## Google Workspace Integration

### Required Services

1. **Google Cloud Project**: https://console.cloud.google.com
2. **Google Workspace Admin**: https://admin.google.com

### Setup Steps

#### 1. Create Google Cloud Project

```
1. Go to https://console.cloud.google.com
2. Click "New Project"
3. Name: "STYLEUS CRM Integration"
4. Click "Create"
```

#### 2. Enable Required APIs

Navigate to **APIs & Services > Library** and enable:

- ✅ Google Drive API
- ✅ Google Sheets API
- ✅ Google Calendar API
- ✅ Gmail API
- ✅ Google Contacts API
- ✅ Google Apps Script API
- ✅ Google People API
- ✅ Google Cloud Storage API

#### 3. Create Service Account

```
1. Go to APIs & Services > Credentials
2. Click "+ CREATE CREDENTIALS" > Service Account
3. Name: "STYLEUS CRM Service"
4. Grant role: "Project > Editor"
5. Click "Done"
6. Click on created service account
7. Go to "Keys" tab
8. Add Key > Create new key > JSON
9. Download and save securely
```

#### 4. Enable Domain-Wide Delegation

```
1. Edit the service account
2. Check "Enable Google Workspace Domain-wide Delegation"
3. Note the Client ID
4. Go to Google Workspace Admin Console
5. Security > API Controls > Domain-wide Delegation
6. Add new:
   - Client ID: [from service account]
   - OAuth Scopes:
     https://www.googleapis.com/auth/drive
     https://www.googleapis.com/auth/spreadsheets
     https://www.googleapis.com/auth/calendar
     https://mail.google.com/
     https://www.googleapis.com/auth/contacts
```

#### 5. Create OAuth 2.0 Client

```
1. APIs & Services > Credentials
2. "+ CREATE CREDENTIALS" > OAuth 2.0 Client ID
3. Application type: Web application
4. Name: "STYLEUS CRM Web Client"
5. Authorized redirect URIs:
   - https://yourdomain.com/auth/google/callback
   - https://yourdomain.com/admin/google/callback
6. Click "Create"
7. Copy Client ID and Client Secret to .env
```

#### 6. Google Drive Setup

```bash
# Install Google Drive package
composer require nao-pon/flysystem-google-drive
```

Update `config/filesystems.php`:

```php
'google' => [
    'driver' => 'google',
    'clientId' => env('GOOGLE_DRIVE_CLIENT_ID'),
    'clientSecret' => env('GOOGLE_DRIVE_CLIENT_SECRET'),
    'refreshToken' => env('GOOGLE_DRIVE_REFRESH_TOKEN'),
    'folder' => env('GOOGLE_DRIVE_FOLDER_ID'),
],
```

**Generate Refresh Token:**

```bash
php artisan google:drive:auth
```

#### 7. Google Sheets Integration

Create a dedicated folder structure:

```
STYLEUS CRM/
├── Leads/
├── Contacts/
├── Products/
├── Quotes/
├── Reports/
└── Exports/
```

Share the main folder with your service account email.

### Google Apps Script Setup

#### Create Standalone Script

1. Go to https://script.google.com
2. New Project > Name: "STYLEUS CRM Connector"
3. Add webhook endpoint:

```javascript
function doPost(e) {
  const data = JSON.parse(e.postData.contents);
  
  // Route to CRM webhook
  const url = 'https://yourdomain.com/api/webhooks/google-script';
  const options = {
    method: 'post',
    contentType: 'application/json',
    payload: JSON.stringify(data),
    headers: {
      'Authorization': 'Bearer YOUR_API_TOKEN'
    }
  };
  
  UrlFetchApp.fetch(url, options);
  
  return ContentService.createTextOutput(JSON.stringify({success: true}))
    .setMimeType(ContentService.MimeType.JSON);
}
```

4. Deploy as Web App:
   - Execute as: Me
   - Who has access: Anyone
   - Copy Web App URL to CRM config

---

## Communication Platforms

### Telegram Integration

#### 1. Create Telegram Bot

```
1. Open Telegram, search for @BotFather
2. Send: /newbot
3. Choose name: STYLEUS CRM Bot
4. Choose username: styleus_crm_bot
5. Copy the API Token
```

#### 2. Configure Webhook

```bash
curl -X POST "https://api.telegram.org/bot<YOUR_BOT_TOKEN>/setWebhook" \
  -d "url=https://yourdomain.com/api/webhooks/telegram" \
  -d "allowed_updates=[\"message\",\"callback_query\"]"
```

Add to `.env`:

```env
TELEGRAM_BOT_TOKEN=your_bot_token_here
TELEGRAM_WEBHOOK_URL=https://yourdomain.com/api/webhooks/telegram
```

### WhatsApp Business API

#### Via Twilio

1. **Sign up**: https://www.twilio.com/console
2. **Get WhatsApp Sandbox**: Console > Messaging > Try it out > WhatsApp
3. **Activate Sandbox**: Send join code to sandbox number
4. **Production Setup**: Apply for WhatsApp Business API access

```env
TWILIO_ACCOUNT_SID=your_account_sid
TWILIO_AUTH_TOKEN=your_auth_token
TWILIO_WHATSAPP_FROM=whatsapp:+14155238886
TWILIO_WEBHOOK_URL=https://yourdomain.com/api/webhooks/whatsapp
```

#### Configure Webhook

```
Twilio Console > Phone Numbers > WhatsApp Sandbox Settings
  When a message comes in: https://yourdomain.com/api/webhooks/whatsapp
```

### WhatsApp Business (Official)

1. **Meta Business Account**: https://business.facebook.com
2. **WhatsApp Business API**: https://developers.facebook.com/products/whatsapp
3. **Requirements**:
   - Verified business
   - Dedicated phone number
   - Official business documentation

```env
META_APP_ID=your_app_id
META_APP_SECRET=your_app_secret
WHATSAPP_PHONE_NUMBER_ID=your_phone_number_id
WHATSAPP_BUSINESS_ACCOUNT_ID=your_business_account_id
WHATSAPP_ACCESS_TOKEN=your_permanent_access_token
```

### Apple Messages for Business

1. **Register**: https://register.apple.com/business-chat
2. **Requirements**:
   - Apple Business Register account
   - D-U-N-S Number
   - Business verification
3. **Integration**: Through messaging service providers (Twilio, Salesforce, etc.)

```env
APPLE_MESSAGES_CSP_ID=your_csp_id
APPLE_MESSAGES_TOKEN=your_auth_token
```

### Zoom Integration

1. **Create Zoom App**: https://marketplace.zoom.us/develop/create
2. **App Type**: OAuth or JWT
3. **Required Scopes**:
   - `meeting:write`
   - `meeting:read`
   - `user:read`

```env
ZOOM_CLIENT_ID=your_client_id
ZOOM_CLIENT_SECRET=your_client_secret
ZOOM_REDIRECT_URL=https://yourdomain.com/auth/zoom/callback
```

---

## AI Services Integration

### OpenAI (ChatGPT) API

1. **Sign up**: https://platform.openai.com/signup
2. **API Keys**: https://platform.openai.com/api-keys
3. **Billing**: Add payment method

```env
OPENAI_API_KEY=sk-...your_key_here
OPENAI_ORGANIZATION=org-...your_org_id
OPENAI_MODEL=gpt-4-turbo-preview
```

**Usage Example:**

```php
use OpenAI\Laravel\Facades\OpenAI;

$result = OpenAI::chat()->create([
    'model' => 'gpt-4',
    'messages' => [
        ['role' => 'user', 'content' => 'Summarize this lead'],
    ],
]);
```

### Anthropic (Claude) API

1. **Sign up**: https://console.anthropic.com
2. **Get API Key**: Account Settings > API Keys
3. **Set budget limits**

```env
ANTHROPIC_API_KEY=sk-ant-...your_key_here
ANTHROPIC_MODEL=claude-3-opus-20240229
```

```bash
composer require anthropic-ai/client
```

---

## Marketing & Sales Tools

### Facebook & Instagram (Meta)

#### 1. Create Meta App

```
1. https://developers.facebook.com/apps
2. Create App > Business Type
3. Add Products:
   - Facebook Login
   - Instagram Basic Display
   - Marketing API
```

#### 2. Required Permissions

- `pages_show_list`
- `pages_read_engagement`
- `pages_manage_posts`
- `instagram_basic`
- `instagram_manage_messages`
- `leads_retrieval`

```env
FACEBOOK_APP_ID=your_app_id
FACEBOOK_APP_SECRET=your_app_secret
FACEBOOK_PAGE_ID=your_page_id
FACEBOOK_ACCESS_TOKEN=your_long_lived_token

INSTAGRAM_BUSINESS_ACCOUNT_ID=your_instagram_id
INSTAGRAM_ACCESS_TOKEN=your_access_token
```

#### 3. Lead Ads Webhook

```
App Dashboard > Webhooks > Page > Subscribe to:
  - leadgen (lead ads)
  - messages
  
Callback URL: https://yourdomain.com/api/webhooks/facebook
Verify Token: your_custom_verify_token
```

### Tilda Integration

1. **Export API Key**: Tilda Project Settings > Export > API
2. **Webhook**: Forms > Webhook URL

```env
TILDA_PUBLIC_KEY=your_public_key
TILDA_SECRET_KEY=your_secret_key
TILDA_WEBHOOK_URL=https://yourdomain.com/api/webhooks/tilda
```

### Calendly

1. **Developer Access**: https://calendly.com/integrations/api_webhooks
2. **Create Webhook**:

```env
CALENDLY_API_KEY=your_api_key
CALENDLY_WEBHOOK_SIGNING_KEY=your_signing_key
```

**Webhook Events:**
- `invitee.created`
- `invitee.canceled`

---

## Payment Processing

### Stripe Integration

1. **Sign up**: https://stripe.com
2. **Get API Keys**: Dashboard > Developers > API keys
3. **Configure Webhooks**: Developers > Webhooks

```env
STRIPE_KEY=pk_live_...your_publishable_key
STRIPE_SECRET=sk_live_...your_secret_key
STRIPE_WEBHOOK_SECRET=whsec_...your_webhook_secret
```

**Webhook Endpoint**: `https://yourdomain.com/api/webhooks/stripe`

**Events to Subscribe**:
- `payment_intent.succeeded`
- `payment_intent.payment_failed`
- `customer.created`
- `invoice.paid`
- `subscription.created`

```bash
composer require stripe/stripe-php
```

### Square Integration

1. **Sign up**: https://squareup.com/signup
2. **Developer Dashboard**: https://developer.squareup.com/apps
3. **Create Application**

```env
SQUARE_APPLICATION_ID=your_app_id
SQUARE_ACCESS_TOKEN=your_access_token
SQUARE_LOCATION_ID=your_location_id
SQUARE_WEBHOOK_SIGNATURE_KEY=your_signature_key
```

**Webhook Endpoint**: `https://yourdomain.com/api/webhooks/square`

---

## File Storage & Backup

### Google Drive as Primary Storage

Already configured in [Google Workspace Integration](#google-workspace-integration).

**Folder Structure:**

```
STYLEUS CRM Cloud/
├── Leads/
│   ├── Documents/
│   └── Attachments/
├── Quotes/
│   └── PDFs/
├── Invoices/
├── Contracts/
├── Backups/
│   ├── Database/
│   └── Files/
└── Reports/
```

### Automated Backups

#### Database Backup Script

Create `/var/www/styleuscrm/scripts/backup-database.sh`:

```bash
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/var/www/styleuscrm/storage/backups"
DB_NAME="styleuscrm_prod"
DB_USER="styleuscrm_user"
DB_PASS="YOUR_PASSWORD"

mkdir -p $BACKUP_DIR

# Create backup
mysqldump -u $DB_USER -p$DB_PASS $DB_NAME | gzip > $BACKUP_DIR/db_backup_$DATE.sql.gz

# Upload to Google Drive
php /var/www/styleuscrm/artisan backup:upload $BACKUP_DIR/db_backup_$DATE.sql.gz

# Keep only last 7 days locally
find $BACKUP_DIR -name "*.sql.gz" -mtime +7 -delete
```

```bash
chmod +x /var/www/styleuscrm/scripts/backup-database.sh
```

#### Cron Job for Automated Backups

```bash
sudo crontab -e
```

Add:

```cron
# Database backup daily at 2 AM
0 2 * * * /var/www/styleuscrm/scripts/backup-database.sh

# Laravel scheduled tasks
* * * * * cd /var/www/styleuscrm && php artisan schedule:run >> /dev/null 2>&1
```

---

## Security & Monitoring

### Firewall Configuration

```bash
# Enable UFW
sudo ufw allow 22
sudo ufw allow 80
sudo ufw allow 443
sudo ufw enable
```

### Fail2Ban (Brute Force Protection)

```bash
sudo apt install fail2ban

# Create jail for Nginx
sudo nano /etc/fail2ban/jail.local
```

```ini
[nginx-limit-req]
enabled = true
filter = nginx-limit-req
logpath = /var/log/nginx/error.log
maxretry = 5
findtime = 600
bantime = 3600
```

### Application Monitoring

#### Install Laravel Horizon (Queue Monitoring)

```bash
composer require laravel/horizon
php artisan horizon:install
```

#### Logging & Error Tracking

**Sentry** (Recommended):

1. Sign up: https://sentry.io
2. Create project for Laravel

```bash
composer require sentry/sentry-laravel
```

```env
SENTRY_LARAVEL_DSN=your_sentry_dsn
SENTRY_TRACES_SAMPLE_RATE=1.0
```

### SSL & Security Headers

Already configured in Nginx config above. Additional headers in `config/cors.php`.

### Regular Updates

```bash
# Create update script
nano /var/www/styleuscrm/scripts/update.sh
```

```bash
#!/bin/bash
cd /var/www/styleuscrm

# Backup before update
./scripts/backup-database.sh

# Pull latest code
git pull origin main

# Update dependencies
composer install --no-dev
npm ci --production
npm run build

# Run migrations
php artisan migrate --force

# Clear & rebuild caches
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Restart services
sudo supervisorctl restart styleuscrm-worker:*
sudo systemctl reload php8.3-fpm
```

---

## 📊 Cost Summary

### Monthly Costs (Estimated)

| Service | Cost | Required |
|---------|------|----------|
| **Server (DigitalOcean/AWS)** | $24 | ✅ Yes |
| **Domain** | $1 | ✅ Yes |
| **SSL** | FREE (Let's Encrypt) | ✅ Yes |
| **Google Workspace** | $6/user | ✅ Yes |
| **Twilio (WhatsApp)** | $20-100 | ⚠️ Usage-based |
| **OpenAI API** | $20-200 | ⚠️ Usage-based |
| **Anthropic API** | $20-200 | ⚠️ Usage-based |
| **Stripe** | 2.9% + $0.30 | ⚠️ Per transaction |
| **Square** | 2.6% + $0.10 | ⚠️ Per transaction |
| **Zoom** | $14.99/host | 🔵 Optional |
| **Sentry** | FREE-$26 | 🔵 Optional |

**Total Base Cost**: ~$51/month + usage fees

---

## 🚀 Quick Deployment Checklist

- [ ] Register domain and configure DNS
- [ ] Provision server (DigitalOcean/AWS/Linode)
- [ ] Install required packages (PHP, MySQL, Nginx, etc.)
- [ ] Clone repository and configure `.env`
- [ ] Install SSL certificate
- [ ] Create database and run migrations
- [ ] Configure Nginx and restart
- [ ] Set up Supervisor for queue workers
- [ ] Create Google Cloud project and enable APIs
- [ ] Configure Google Workspace integration
- [ ] Set up Google Drive storage
- [ ] Register and configure Telegram bot
- [ ] Set up Twilio for WhatsApp
- [ ] Create OpenAI and Anthropic API keys
- [ ] Configure Meta (Facebook/Instagram) app
- [ ] Set up Stripe and Square payment gateways
- [ ] Configure Tilda and Calendly webhooks
- [ ] Set up automated backups
- [ ] Enable monitoring (Sentry, Horizon)
- [ ] Configure firewall and Fail2Ban
- [ ] Test all integrations
- [ ] Document admin credentials securely

---

## 📞 Support & Resources

### Official Documentation
- **Krayin CRM**: https://devdocs.krayincrm.com
- **Laravel**: https://laravel.com/docs
- **Google Cloud**: https://cloud.google.com/docs
- **Twilio**: https://www.twilio.com/docs
- **Stripe**: https://stripe.com/docs/api

### Community
- **Krayin Forums**: https://forums.krayincrm.com
- **Laravel Community**: https://laracasts.com

---

**Document Version**: 1.0  
**Last Updated**: 2025-12-01  
**Author**: STYLEUS Development Team
