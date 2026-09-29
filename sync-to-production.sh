#!/bin/bash

###############################################################################
# STYLEUS CRM - Quick Sync to Production
# Синхронизирует локальные изменения с production сервером
###############################################################################

SERVER="root@45.55.62.115"
REMOTE_PATH="/var/www/styleus"
GITHUB_REPO="https://github.com/rsv-tech/styleuscrm.git"

echo "🔄 Syncing STYLEUS CRM to production..."
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

# Sync files
rsync -avz --progress \
    --exclude 'node_modules' \
    --exclude 'vendor' \
    --exclude '.git' \
    --exclude 'storage/logs/*' \
    --exclude 'storage/framework/cache/*' \
    --exclude 'storage/framework/sessions/*' \
    --exclude 'storage/framework/views/*' \
    --exclude '.env' \
    --exclude '.env.backup' \
    --exclude '*.log' \
    . ${SERVER}:${REMOTE_PATH}/

echo ""
echo "✅ Files synced!"
echo "🔄 Updating server..."

# Run commands on server
ssh ${SERVER} << 'ENDSSH'
cd /var/www/styleus

# Clear caches
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Restart queue workers
sudo supervisorctl restart styleuscrm-worker:* 2>/dev/null || true

echo "✅ Server updated!"
ENDSSH

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "🎉 Deployment complete!"
echo "🌐 Visit: https://crm.styleus.us"
echo ""
