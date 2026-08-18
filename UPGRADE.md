# UPGRADE Guide

- [Upgrading To v2.2 From v2.1](#upgrading-to-v22-from-v21)

## High Impact Changes

- [Laravel 12 Upgrade](#laravel-12-upgrade)

## Upgrading To v2.2 From v2.1

> [!NOTE]
> We strive to document every potential breaking change. However, as some of these alterations occur in lesser-known sections of Krayin, only a fraction of them may impact your application.

### Updating Dependencies

**Impact Probability: High**

#### PHP 8.3 Required

Krayin CRM v2.2 now requires PHP 8.3 or greater.

### Laravel 12 Upgrade

**Impact Probability: High**

Krayin CRM v2.2 has been upgraded to Laravel 12, which introduces stricter type checking and modernized date/time handling.

#### Key Changes

- **Bootstrap**: `bootstrap/app.php` now uses the new `Application::configure()` builder pattern. Service providers are listed in `bootstrap/providers.php`.

- **Kernels Removed**: `app/Http/Kernel.php` and `app/Console/Kernel.php` are removed. Middleware and scheduling are configured in `bootstrap/app.php` and `routes/console.php` respectively.

- **Exception Handler Removed**: `app/Exceptions/Handler.php` is removed. Exception handling is configured in `bootstrap/app.php`.

- **Middleware Classes Removed**: Built-in middleware wrappers (EncryptCookies, VerifyCsrfToken, TrimStrings, TrustProxies, etc.) are removed. Customizations are now done via `bootstrap/app.php`.

- **Service Providers Simplified**: `AuthServiceProvider`, `EventServiceProvider`, `RouteServiceProvider`, and `BroadcastServiceProvider` are removed. Their logic is handled in `bootstrap/app.php` or `AppServiceProvider`.

- **Config**: `config/app.php` no longer contains `providers` or `aliases` arrays.

- **doctrine/dbal** dependency has been removed (native column modification in Laravel 12).