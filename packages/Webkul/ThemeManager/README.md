# ThemeManager for Krayin CRM

A comprehensive visual customization package for Krayin CRM that allows complete branding and styling control.

## Features

- **Theme Activation**: Enable/disable custom theme globally
- **Color Customization**: Primary, dark, light, success, warning, and danger colors
- **Logo Management**: Main logo, light mode logo, icon, and favicon
- **Login Page Styling**: Background image, zoom, opacity, "Powered By" toggle
- **Custom Login Card**: Background, overlay, title, subtitle, sparkles effect, help link
- **Empty States**: Custom SVG images for all empty state screens
- **Security**: SVG sanitization, CSS injection prevention, input validation

## Requirements

- PHP 8.1+
- Laravel 10+ or 11+
- Krayin CRM 2.0+

## Installation

See [INSTALL.md](INSTALL.md) for detailed installation instructions.

### Quick Install

1. Copy the package to `packages/Webkul/ThemeManager/`

2. Add to `composer.json` autoload:
```json
"autoload": {
    "psr-4": {
        "Webkul\\ThemeManager\\": "packages/Webkul/ThemeManager/src"
    }
}
```

3. Add providers to `config/app.php`:
```php
'providers' => [
    // ...
    Webkul\ThemeManager\Providers\ThemeManagerServiceProvider::class,
],
```

4. Add to `config/concord.php`:
```php
'modules' => [
    // ...
    \Webkul\ThemeManager\Providers\ModuleServiceProvider::class,
],
```

5. Run commands:
```bash
composer dump-autoload
php artisan migrate
php artisan storage:link
php artisan optimize:clear
```

6. Access: `Admin Panel > Settings > Theme`

## Usage

### Accessing Theme Helper

```php
// Via facade
app('theme')->isActive();
app('theme')->getConfig();
app('theme')->getLogo('main');
app('theme')->getFavicon();

// Get CSS variables
app('theme')->getCssVariables();

// Get login configuration
app('theme')->getLoginConfig();

// Get empty state image
app('theme')->getEmptyState('leads');
```

### In Blade Templates

```blade
@if(app('theme')->isActive())
    <img src="{{ app('theme')->getLogo('main') }}" alt="Logo">
@endif
```

## Configuration Options

### Colors
| Field | Default | Description |
|-------|---------|-------------|
| `color_primary` | #1E40AF | Main brand color |
| `color_primary_dark` | #1E3A8A | Darker variant |
| `color_primary_light` | #3B82F6 | Lighter variant |
| `color_success` | #10B981 | Success actions |
| `color_warning` | #F59E0B | Warning states |
| `color_danger` | #EF4444 | Error/danger states |

### Logos
| Field | Formats | Description |
|-------|---------|-------------|
| `logo_main` | SVG, PNG, JPG, WebP | Main header logo |
| `logo_light` | SVG, PNG, JPG, WebP | Logo for dark mode |
| `logo_icon` | SVG, PNG, JPG, ICO | Small icon version |
| `favicon` | ICO, PNG, SVG | Browser tab icon |

### Login Page
| Field | Default | Description |
|-------|---------|-------------|
| `login_bg_image` | null | Background image |
| `login_bg_zoom` | 100 | Image zoom (50-200%) |
| `login_bg_opacity` | 50 | Overlay opacity (0-100%) |
| `login_show_powered_by` | true | Show "Powered by Krayin" |

### Login Card
| Field | Default | Description |
|-------|---------|-------------|
| `login_card_enabled` | false | Enable custom card |
| `login_card_bg_image` | null | Card background image |
| `login_card_bg_opacity` | 62 | Background opacity |
| `login_card_overlay_color` | rgba(10,45,15,0.78) | Overlay color |
| `login_card_title` | "Bem-vindo" | Welcome title |
| `login_card_subtitle` | "Acesse sua conta..." | Subtitle text |
| `login_card_sparkles` | false | Sparkle animation |
| `login_card_help_link` | true | Show help link |
| `login_card_support_email` | suporte@empresa.com.br | Support email |
| `login_card_custom_code` | null | Custom HTML/CSS/JS |

### Empty States
Custom SVG images for:
- Activities
- Calls
- Emails
- Meetings
- Notes
- Organizations
- Persons
- Leads
- Products

## Security

The package includes several security measures:

- **SVG Sanitization**: Removes scripts, event handlers, and dangerous content
- **Color Validation**: Regex validation for hex and rgba colors
- **Input Sanitization**: XSS prevention on text fields
- **File Type Validation**: Whitelist of allowed extensions
- **Bounds Checking**: Min/max validation for numeric fields

## File Structure

```
packages/Webkul/ThemeManager/
├── src/
│   ├── Config/
│   │   ├── menu.php
│   │   └── system.php
│   ├── Contracts/
│   │   └── ThemeConfig.php
│   ├── Helpers/
│   │   └── ThemeHelper.php
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── ThemeController.php
│   │   └── Middleware/
│   │       └── ThemeMiddleware.php
│   ├── Models/
│   │   ├── ThemeConfig.php
│   │   └── ThemeConfigProxy.php
│   ├── Providers/
│   │   ├── ModuleServiceProvider.php
│   │   └── ThemeManagerServiceProvider.php
│   ├── Repositories/
│   │   └── ThemeConfigRepository.php
│   └── Routes/
│       └── web.php
├── Database/
│   └── Migrations/
│       └── 2024_12_20_000001_create_theme_configs_table.php
├── Resources/
│   ├── lang/
│   │   ├── en/
│   │   │   └── app.php
│   │   └── pt_BR/
│   │       └── app.php
│   └── views/
│       ├── admin/
│       │   ├── sessions/
│       │   │   └── login.blade.php
│       │   └── settings/
│       │       └── theme/
│       │           └── index.blade.php
│       └── components/
│           └── theme-styles.blade.php
├── composer.json
├── module.json
├── README.md
└── INSTALL.md
```

## Troubleshooting

| Problem | Solution |
|---------|----------|
| Menu not showing | Check `Config/menu.php` and run `php artisan optimize:clear` |
| 404 error | Run `php artisan route:list \| grep theme` |
| Class not found | Run `composer dump-autoload` |
| Table not found | Run `php artisan migrate` |
| CSS not applying | Check if theme is active and middleware is registered |
| Upload fails | Run `chmod -R 775 storage/` and `php artisan storage:link` |
| Logos not showing | Check symlink: `ls -la public/storage` |

## License

MIT License

## Credits

- **Author**: Webkul
- **Framework**: Laravel
- **CRM**: Krayin CRM
