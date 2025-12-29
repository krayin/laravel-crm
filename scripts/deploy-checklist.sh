#!/bin/bash
# ==============================================================================
# Deploy Checklist - Theme System
# ==============================================================================
# Execute após deploy para garantir que o sistema de temas funciona corretamente.
#
# Uso:
#   chmod +x scripts/deploy-checklist.sh
#   ./scripts/deploy-checklist.sh
#
# ==============================================================================

set -e

echo "========================================"
echo "  KRAYIN THEME SYSTEM - DEPLOY CHECK"
echo "========================================"
echo ""

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

ERRORS=0
WARNINGS=0

check_pass() {
    echo -e "${GREEN}✓${NC} $1"
}

check_fail() {
    echo -e "${RED}✗${NC} $1"
    ERRORS=$((ERRORS + 1))
}

check_warn() {
    echo -e "${YELLOW}⚠${NC} $1"
    WARNINGS=$((WARNINGS + 1))
}

# ==============================================================================
# 1. ENVIRONMENT CHECKS
# ==============================================================================
echo "1. Environment Checks"
echo "---------------------"

# Check APP_DEBUG
if grep -q "APP_DEBUG=false" .env 2>/dev/null; then
    check_pass "APP_DEBUG=false"
else
    check_warn "APP_DEBUG should be false in production"
fi

# Check CACHE_DRIVER
CACHE_DRIVER=$(grep "^CACHE_DRIVER=" .env 2>/dev/null | cut -d'=' -f2)
if [ "$CACHE_DRIVER" = "redis" ] || [ "$CACHE_DRIVER" = "memcached" ]; then
    check_pass "CACHE_DRIVER=$CACHE_DRIVER (recommended)"
elif [ "$CACHE_DRIVER" = "database" ]; then
    check_warn "CACHE_DRIVER=database (acceptable but slower)"
else
    check_fail "CACHE_DRIVER=$CACHE_DRIVER - Use redis/memcached for theme system!"
fi

# Check SESSION_DRIVER
SESSION_DRIVER=$(grep "^SESSION_DRIVER=" .env 2>/dev/null | cut -d'=' -f2)
if [ "$SESSION_DRIVER" = "redis" ] || [ "$SESSION_DRIVER" = "database" ]; then
    check_pass "SESSION_DRIVER=$SESSION_DRIVER"
else
    check_warn "SESSION_DRIVER=$SESSION_DRIVER - Consider redis for preview isolation"
fi

echo ""

# ==============================================================================
# 2. DATABASE CHECKS
# ==============================================================================
echo "2. Database Checks"
echo "------------------"

# Check migrations
php artisan migrate:status 2>/dev/null | grep -q "brand_kit_overrides" && \
    check_pass "brand_kit_overrides table exists" || \
    check_fail "brand_kit_overrides table missing - run migrations!"

php artisan migrate:status 2>/dev/null | grep -q "brand_kit_snapshots" && \
    check_pass "brand_kit_snapshots table exists" || \
    check_fail "brand_kit_snapshots table missing - run migrations!"

php artisan migrate:status 2>/dev/null | grep -q "brand_kit_custom_css" && \
    check_pass "brand_kit_custom_css table exists" || \
    check_fail "brand_kit_custom_css table missing - run migrations!"

php artisan migrate:status 2>/dev/null | grep -q "theme_configs" && \
    check_pass "theme_configs table exists" || \
    check_fail "theme_configs table missing - run migrations!"

echo ""

# ==============================================================================
# 3. ROUTES CHECK
# ==============================================================================
echo "3. Routes Check"
echo "---------------"

ROUTE_COUNT=$(php artisan route:list 2>/dev/null | grep -c "brand-kit" || echo "0")
if [ "$ROUTE_COUNT" -ge 9 ]; then
    check_pass "Brand-kit routes: $ROUTE_COUNT registered"
else
    check_fail "Brand-kit routes: only $ROUTE_COUNT found (expected 9+)"
fi

THEME_ROUTES=$(php artisan route:list 2>/dev/null | grep -c "settings/theme" || echo "0")
if [ "$THEME_ROUTES" -ge 3 ]; then
    check_pass "Theme routes: $THEME_ROUTES registered"
else
    check_warn "Theme routes: only $THEME_ROUTES found"
fi

echo ""

# ==============================================================================
# 4. STORAGE CHECKS
# ==============================================================================
echo "4. Storage Checks"
echo "-----------------"

# Check storage link
if [ -L "public/storage" ]; then
    check_pass "Storage symlink exists"
else
    check_fail "Storage symlink missing - run: php artisan storage:link"
fi

# Check theme directory
if [ -d "storage/app/public/themes" ]; then
    check_pass "Themes directory exists"
else
    check_warn "Themes directory missing (will be created on first theme upload)"
fi

# Check theme-manager uploads directory
if [ -d "storage/app/public/theme-manager" ]; then
    check_pass "Theme-manager uploads directory exists"
else
    check_warn "Theme-manager uploads directory missing (will be created on first upload)"
fi

# Check permissions
if [ -w "storage/app/public" ]; then
    check_pass "Storage directory is writable"
else
    check_fail "Storage directory not writable - fix permissions!"
fi

echo ""

# ==============================================================================
# 5. VIEW OVERRIDE CHECK
# ==============================================================================
echo "5. View Override Check"
echo "----------------------"

if [ -f "resources/views/vendor/theme-manager/admin/settings/theme/index.blade.php" ]; then
    check_pass "Theme settings view override exists"
else
    check_warn "Theme settings view override not found (using package default)"
fi

echo ""

# ==============================================================================
# 6. CACHE STATUS
# ==============================================================================
echo "6. Cache Status"
echo "---------------"

# Try to connect to cache
php artisan tinker --execute="Cache::put('deploy_check', 'ok', 10); echo Cache::get('deploy_check');" 2>/dev/null | grep -q "ok" && \
    check_pass "Cache connection working" || \
    check_fail "Cache connection failed!"

echo ""

# ==============================================================================
# 7. OPCACHE CHECK (if applicable)
# ==============================================================================
echo "7. OPcache Check"
echo "----------------"

php -m 2>/dev/null | grep -qi "opcache" && \
    check_pass "OPcache extension loaded" || \
    check_warn "OPcache not loaded (recommended for production)"

# Note about opcache.revalidate_freq
echo "   Note: If theme changes don't reflect, check opcache.revalidate_freq"

echo ""

# ==============================================================================
# 8. RECOMMENDED POST-DEPLOY COMMANDS
# ==============================================================================
echo "8. Post-Deploy Commands"
echo "-----------------------"
echo "   Run these commands after deploy:"
echo ""
echo "   php artisan config:cache"
echo "   php artisan route:cache"
echo "   php artisan view:cache"
echo "   php artisan optimize"
echo ""

# ==============================================================================
# SUMMARY
# ==============================================================================
echo "========================================"
echo "  SUMMARY"
echo "========================================"
echo ""

if [ $ERRORS -eq 0 ] && [ $WARNINGS -eq 0 ]; then
    echo -e "${GREEN}All checks passed!${NC}"
elif [ $ERRORS -eq 0 ]; then
    echo -e "${YELLOW}Passed with $WARNINGS warning(s)${NC}"
else
    echo -e "${RED}Failed with $ERRORS error(s) and $WARNINGS warning(s)${NC}"
    echo ""
    echo "Fix the errors before proceeding!"
    exit 1
fi

echo ""
echo "Deploy checklist complete."
