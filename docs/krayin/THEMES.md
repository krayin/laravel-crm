# Theme System Documentation

This document describes the theme preset system and how overrides work in Krayin CRM.

## Table of Contents

- [Theme Presets](#theme-presets)
- [theme.json Structure](#themejson-structure)
- [Override Architecture](#override-architecture)
- [Cache Commands](#cache-commands)
- [Creating a New Theme](#creating-a-new-theme)
- [Troubleshooting](#troubleshooting)
- [Deploy Checklist](#deploy-checklist)
- [Cache Invalidation](#cache-invalidation)
- [Testing](#testing)

---

## Theme Presets

Theme presets are stored in:

```
storage/app/public/themes/{slug}/theme.json
```

Each theme folder must contain a `theme.json` file with the theme metadata and colors.

### Directory Structure

```
storage/app/public/themes/
├── azul-oceano/
│   └── theme.json
├── roxo-moderno/
│   ├── theme.json
│   ├── logo_main.svg      (optional)
│   └── login_bg.jpg       (optional)
├── stelium-sanctuary/
│   ├── theme.json
│   ├── logo_main.svg
│   ├── logo_light.svg
│   └── login_bg.jpg
└── [your-theme-slug]/
    └── theme.json
```

---

## theme.json Structure

Each theme must have a `theme.json` file with the following shape:

```json
{
  "name": "Theme Display Name",
  "slug": "theme-slug",
  "version": "1.0.0",
  "description": "A brief description of the theme",
  "author": "Author Name",
  "preview": "preview.png",

  "color_primary": "#1E40AF",
  "color_primary_dark": "#1E3A8A",
  "color_primary_light": "#3B82F6",
  "color_success": "#10B981",
  "color_warning": "#F59E0B",
  "color_danger": "#EF4444",

  "logo_main": "logo_main.svg",
  "logo_light": "logo_light.svg",
  "favicon": "favicon.ico",

  "login": {
    "bg_image": "login_bg.jpg",
    "bg_zoom": 100,
    "bg_opacity": 50,
    "card_bg_image": "login_card_bg.jpg",
    "card_bg_opacity": 62,
    "card_overlay_color": "rgba(10, 45, 15, 0.78)",
    "card_title": "Welcome",
    "card_subtitle": "Sign in to your account"
  }
}
```

### Required Fields

| Field | Type | Description |
|-------|------|-------------|
| `name` | string | Display name for the theme |
| `color_primary` | hex | Primary brand color |
| `color_primary_dark` | hex | Darker shade of primary |
| `color_primary_light` | hex | Lighter shade of primary |
| `color_success` | hex | Success/positive color |
| `color_warning` | hex | Warning/caution color |
| `color_danger` | hex | Error/danger color |

### Colors Array (Internal)

The `ThemeCatalog` service normalizes colors into a `colors` subarray:

```php
[
    'slug' => 'azul-oceano',
    'name' => 'Azul Oceano',
    'version' => '1.0.0',
    'description' => '...',
    'author' => null,
    'preview' => null,
    'colors' => [
        'primary'       => '#1E40AF',
        'primary_dark'  => '#1E3A8A',
        'primary_light' => '#3B82F6',
        'success'       => '#10B981',
        'warning'       => '#F59E0B',
        'danger'        => '#EF4444',
    ],
]
```

---

## Override Architecture

The theme system is **upgrade-safe** - no modifications to `packages/Webkul/` are required.

### View Override

Views are overridden in:

```
resources/views/vendor/theme-manager/admin/settings/theme/index.blade.php
```

Priority is established by `ThemeBootProvider`:

```php
// app/Providers/ThemeBootProvider.php
View::prependNamespace('theme-manager', resource_path('views/vendor/theme-manager'));
```

### Route Override

Routes are overridden by `ThemeOverridesServiceProvider`:

```php
// app/Providers/ThemeOverridesServiceProvider.php
Route::get('theme', 'index')->name('admin.settings.theme.index');
// Points to: App\Http\Controllers\Admin\Settings\ThemeSettingsController
```

### Provider Registration Order

In `config/app.php`, providers must be registered in this order:

```php
// 1. Package provider (original)
Webkul\ThemeManager\Providers\ThemeManagerServiceProvider::class,

// 2. View overrides
App\Providers\ThemeBootProvider::class,

// 3. Route overrides (MUST be last)
App\Providers\ThemeOverridesServiceProvider::class,
```

### Key Files

| File | Purpose |
|------|---------|
| `app/Services/Theme/ThemeCatalog.php` | Single source of truth for theme catalog |
| `app/Http/Controllers/Admin/Settings/ThemeSettingsController.php` | Override controller |
| `app/Providers/ThemeOverridesServiceProvider.php` | Route override provider |
| `app/Providers/ThemeBootProvider.php` | View namespace override |
| `resources/views/vendor/theme-manager/` | View blade overrides |

---

## Cache Commands

After making changes to themes, providers, or views, clear the caches:

```bash
# Clear all caches (recommended)
php artisan optimize:clear

# Or individually:
php artisan cache:clear      # Application cache
php artisan config:clear     # Config cache
php artisan route:clear      # Route cache
php artisan view:clear       # Compiled views

# Browser cache
# Press Ctrl+F5 (or Cmd+Shift+R on Mac)
```

### When to Clear Cache

- After adding/removing a theme preset
- After modifying a `theme.json` file
- After changing provider registration
- After modifying blade views
- After changing routes

---

## Creating a New Theme

### Step 1: Create Theme Directory

```bash
mkdir -p storage/app/public/themes/my-custom-theme
```

### Step 2: Create theme.json

```bash
cat > storage/app/public/themes/my-custom-theme/theme.json << 'EOF'
{
  "name": "My Custom Theme",
  "slug": "my-custom-theme",
  "version": "1.0.0",
  "description": "A custom theme for my organization",
  "author": "Your Name",

  "color_primary": "#8B5CF6",
  "color_primary_dark": "#7C3AED",
  "color_primary_light": "#A78BFA",
  "color_success": "#10B981",
  "color_warning": "#F59E0B",
  "color_danger": "#EF4444"
}
EOF
```

### Step 3: Clear Cache

```bash
php artisan optimize:clear
```

### Step 4: Verify

Navigate to **Settings > Theme** in the admin panel. Your new theme should appear in the theme selector with its colors displayed.

---

## Troubleshooting

### Theme not appearing

1. Verify `theme.json` exists and is valid JSON
2. Check file permissions
3. Clear cache: `php artisan optimize:clear`

### Colors showing as default (blue)

1. Check that `color_*` fields are present in `theme.json`
2. Verify hex format: `#RRGGBB` or `#RGB`
3. Clear cache and browser cache (Ctrl+F5)

### View not updating

1. Clear view cache: `php artisan view:clear`
2. Check override file exists in `resources/views/vendor/theme-manager/`
3. Verify `ThemeBootProvider` is registered in `config/app.php`

### Route pointing to wrong controller

1. Verify `ThemeOverridesServiceProvider` is **last** in providers list
2. Clear route cache: `php artisan route:clear`
3. Check with: `php artisan route:list --name=admin.settings.theme`

---

## Deploy Checklist

When deploying theme system changes to production:

### Pre-Deploy

- [ ] All tests pass: `php artisan test tests/Unit/Services/Theme/ tests/Feature/Admin/Settings/`
- [ ] Provider order verified in `config/app.php` (ThemeOverridesServiceProvider is LAST)
- [ ] Theme presets have valid `theme.json` files
- [ ] Override views exist in `resources/views/vendor/theme-manager/`

### Deploy Steps

```bash
# 1. Pull latest code
git pull origin main

# 2. Install/update dependencies
composer install --no-dev --optimize-autoloader

# 3. Clear ALL caches
php artisan optimize:clear

# 4. Rebuild caches for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 5. Verify routes point to correct controller
php artisan route:list --name=admin.settings.theme
# Should show: App\Http\Controllers\Admin\Settings\ThemeSettingsController
```

### Post-Deploy Verification

- [ ] Access `/admin/settings/theme` - page loads without errors
- [ ] Theme cards show colored circles (6 colors per card)
- [ ] Selecting a theme updates the preview
- [ ] Saving theme persists changes to database
- [ ] Logout and verify login page reflects theme

---

## Cache Invalidation

The theme system uses caching for performance. Here's when cache is automatically invalidated:

### Automatic Invalidation

| Action | Cache Cleared |
|--------|---------------|
| Save theme settings | `ThemeHelper::clearCache()` |
| Delete logo/image | `ThemeHelper::clearCache()` |
| Load theme preset | `ThemeHelper::clearCache()` |

### Manual Invalidation Required

These actions require manual cache clearing:

| Action | Command |
|--------|---------|
| Add new theme preset | `php artisan cache:clear` |
| Modify `theme.json` | `php artisan cache:clear` |
| Change provider order | `php artisan optimize:clear` |
| Modify blade views | `php artisan view:clear` |
| Change routes | `php artisan route:clear` |

### Cache Keys Used

```php
// Application cache (1 hour TTL)
'theme.config'           // ThemeConfig singleton
'theme.css_variables'    // Generated CSS variables
'theme.login_config'     // Login page configuration

// Cleared by ThemeHelper::clearCache()
Cache::forget('theme.config');
Cache::forget('theme.css_variables');
Cache::forget('theme.login_config');
```

---

## Testing

### Running Tests

```bash
# All theme-related tests
php artisan test tests/Unit/Services/Theme/ tests/Feature/Admin/Settings/ --no-coverage

# Unit tests only (ThemeCatalog)
php artisan test tests/Unit/Services/Theme/ThemeCatalogTest.php

# Feature tests only (routes, controller)
php artisan test tests/Feature/Admin/Settings/ThemeSettingsTest.php
```

### Test Coverage

| Test File | Coverage |
|-----------|----------|
| `ThemeCatalogTest` | ThemeCatalog service (all, get, exists, slugs) |
| `ThemeSettingsTest` | Route binding, controller resolution, theme structure |

---

## Related Documentation

- [CLAUDE.md](../../CLAUDE.md) - Project conventions and architecture
- [BUG_VIEW_COMPOSER_OVERRIDE.md](../troubleshooting%20preview/BUG_VIEW_COMPOSER_OVERRIDE.md) - View Composer bug fix
- [THEME_COLORS_DEBUG.md](../troubleshooting%20preview/THEME_COLORS_DEBUG.md) - Color debugging guide
