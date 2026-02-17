# ThemeManager Installation Guide

Complete installation instructions for the ThemeManager package for Krayin CRM.

## Prerequisites

- Krayin CRM 2.0+ installed and working
- PHP 8.1 or higher
- Composer 2.x
- Laravel 10+ or 11+
- MySQL 8+ or SQLite

## Installation Steps

### Step 1: Copy Package Files

Copy the entire `ThemeManager` folder to your Krayin installation:

```bash
cp -r ThemeManager /path/to/krayin/packages/Webkul/
```

Your structure should look like:
```
packages/
└── Webkul/
    ├── Admin/
    ├── Core/
    ├── ThemeManager/   <- New package
    └── ...
```

### Step 2: Register Autoload

Edit `composer.json` in the root of your Krayin installation.

Find the `autoload.psr-4` section and add:

```json
{
    "autoload": {
        "psr-4": {
            "Webkul\\ThemeManager\\": "packages/Webkul/ThemeManager/src"
        }
    }
}
```

### Step 3: Register Service Provider

Edit `config/app.php` and add the provider to the `providers` array:

```php
'providers' => [
    // ... other providers

    // ThemeManager - Add at the END of the array
    Webkul\ThemeManager\Providers\ThemeManagerServiceProvider::class,
],
```

### Step 4: Register Concord Module

Edit `config/concord.php` and add the module:

```php
'modules' => [
    // ... other modules

    // ThemeManager - Add at the END
    \Webkul\ThemeManager\Providers\ModuleServiceProvider::class,
],
```

### Step 5: Run Composer

Regenerate the autoloader:

```bash
composer dump-autoload
```

### Step 6: Run Migrations

Create the database table:

```bash
php artisan migrate
```

This creates the `theme_configs` table with all required fields.

### Step 7: Create Storage Symlink

Ensure the storage link exists for file uploads:

```bash
php artisan storage:link
```

### Step 8: Clear Cache

Clear all caches to load the new package:

```bash
php artisan optimize:clear
```

Or individually:
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

### Step 9: Set Permissions (Linux/Mac)

Ensure proper permissions for file uploads:

```bash
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
chown -R www-data:www-data storage/
```

## Verification

### Check Routes

Verify the routes are registered:

```bash
php artisan route:list | grep theme
```

Expected output:
```
GET  admin/settings/theme    admin.settings.theme.index
POST admin/settings/theme    admin.settings.theme.update
```

### Check Provider

Verify the provider is loaded:

```bash
php artisan tinker
>>> app('theme')
```

Expected: Returns a `ThemeHelper` instance.

### Access Admin Panel

Navigate to:
```
http://your-site.com/admin/settings/theme
```

You should see the Theme Configuration page.

## Uninstallation

To remove the package:

### 1. Remove from config files

Edit `config/app.php` - remove the provider line
Edit `config/concord.php` - remove the module line

### 2. Remove autoload

Edit `composer.json` - remove the PSR-4 autoload line

### 3. Drop database table

```bash
php artisan migrate:rollback --path=packages/Webkul/ThemeManager/Database/Migrations
```

Or manually:
```sql
DROP TABLE IF EXISTS theme_configs;
```

### 4. Remove files

```bash
rm -rf packages/Webkul/ThemeManager
rm -rf storage/app/public/theme-manager
```

### 5. Clear cache

```bash
composer dump-autoload
php artisan optimize:clear
```

## Troubleshooting

### "Class not found" errors

```bash
composer dump-autoload
php artisan optimize:clear
```

### 404 on theme settings page

1. Check routes: `php artisan route:list | grep theme`
2. Check provider is registered in `config/app.php`
3. Clear route cache: `php artisan route:clear`

### Menu item not showing

1. Check `packages/Webkul/ThemeManager/src/Config/menu.php` exists
2. Clear cache: `php artisan optimize:clear`
3. Check menu registration in ServiceProvider

### Uploads not working

1. Check symlink: `ls -la public/storage`
2. If missing: `php artisan storage:link`
3. Check permissions: `chmod -R 775 storage/`
4. Check directory exists: `ls storage/app/public/theme-manager/`

### CSS not applying

1. Verify theme is set to "Active" in settings
2. Check middleware is registered
3. Inspect page source for `<style>` tag with theme variables
4. Check browser console for JavaScript errors

### Database migration fails

1. Check database connection
2. Verify table doesn't already exist
3. Run: `php artisan migrate:status`

## Docker Installation

If using Docker:

```bash
# Enter container
docker exec -it krayin_app bash

# Run installation commands inside container
composer dump-autoload
php artisan migrate
php artisan storage:link
php artisan optimize:clear

# Fix permissions
chown -R www-data:www-data storage/
chmod -R 775 storage/
```

## Support

For issues or questions:
- Check [README.md](README.md) for usage instructions
- Review [CLAUDE.md](../../CLAUDE.md) for development guidelines
- Check Laravel logs: `tail -f storage/logs/laravel.log`
