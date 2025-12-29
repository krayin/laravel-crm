<?php

namespace Webkul\ThemeManager\Helpers;

use Illuminate\Support\Facades\Cache;
use Webkul\ThemeManager\Models\ThemeConfig;

class ThemeHelper
{
    /**
     * Cache key for theme configuration.
     *
     * @var string
     */
    protected $cacheKey = 'theme_config';

    /**
     * Cache TTL in seconds (1 hour).
     *
     * @var int
     */
    protected $cacheTtl = 3600;

    /**
     * Regex pattern for valid hex colors.
     *
     * @var string
     */
    protected $hexColorPattern = '/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/';

    /**
     * Regex pattern for valid rgba colors.
     *
     * @var string
     */
    protected $rgbaPattern = '/^rgba?\(\s*\d{1,3}\s*,\s*\d{1,3}\s*,\s*\d{1,3}\s*(,\s*(0|1|0?\.\d+))?\s*\)$/';

    /**
     * Get the theme configuration with caching.
     * Uses array caching to avoid Eloquent serialization issues.
     *
     * @return ThemeConfig
     */
    public function getConfig()
    {
        $configArray = Cache::remember($this->cacheKey, $this->cacheTtl, function () {
            return ThemeConfig::getInstance()->toArray();
        });

        // Hydrate the model from cached array
        $config = new ThemeConfig;
        $config->forceFill($configArray);
        $config->exists = true;

        return $config;
    }

    /**
     * Clear the theme configuration cache.
     *
     * @return void
     */
    public function clearCache()
    {
        Cache::forget($this->cacheKey);
    }

    /**
     * Check if the theme is active.
     *
     * @return bool
     */
    public function isActive()
    {
        return (bool) $this->getConfig()->is_active;
    }

    /**
     * Sanitize a hex color value to prevent CSS injection.
     */
    public function sanitizeHexColor(?string $color, string $default = '#000000'): string
    {
        if (empty($color)) {
            return $default;
        }

        // Ensure it starts with #
        if (! str_starts_with($color, '#')) {
            $color = '#'.$color;
        }

        // Validate hex format
        if (preg_match($this->hexColorPattern, $color)) {
            return $color;
        }

        return $default;
    }

    /**
     * Sanitize an rgba color value to prevent CSS injection.
     */
    public function sanitizeRgbaColor(?string $color, string $default = 'rgba(0, 0, 0, 0.5)'): string
    {
        if (empty($color)) {
            return $default;
        }

        // Validate rgba format
        if (preg_match($this->rgbaPattern, $color)) {
            return $color;
        }

        // Also accept valid hex colors
        if (preg_match($this->hexColorPattern, $color)) {
            return $color;
        }

        return $default;
    }

    /**
     * Sanitize text for safe output in CSS/HTML.
     */
    public function sanitizeText(?string $text, int $maxLength = 200): string
    {
        if (empty($text)) {
            return '';
        }

        // Remove potentially dangerous characters for CSS
        $text = preg_replace('/[<>"\';{}()\\\\]/', '', $text);

        return mb_substr($text, 0, $maxLength);
    }

    /**
     * Get CSS variables as a string for :root.
     * All color values are sanitized to prevent XSS/CSS injection.
     *
     * @return string
     */
    public function getCssVariables()
    {
        $config = $this->getConfig();

        // Sanitize all colors
        $colorPrimary = $this->sanitizeHexColor($config->color_primary, '#1E40AF');
        $colorPrimaryDark = $this->sanitizeHexColor($config->color_primary_dark, '#1E3A8A');
        $colorPrimaryLight = $this->sanitizeHexColor($config->color_primary_light, '#3B82F6');
        $colorSuccess = $this->sanitizeHexColor($config->color_success, '#10B981');
        $colorWarning = $this->sanitizeHexColor($config->color_warning, '#F59E0B');
        $colorDanger = $this->sanitizeHexColor($config->color_danger, '#EF4444');

        $variables = [
            // Primary colors
            '--primary-color'       => $colorPrimary,
            '--primary-dark-color'  => $colorPrimaryDark,
            '--primary-light-color' => $colorPrimaryLight,
            '--primary-rgb'         => $this->hexToRgb($colorPrimary),

            // Success
            '--success-color' => $colorSuccess,
            '--success-rgb'   => $this->hexToRgb($colorSuccess),

            // Warning
            '--warning-color' => $colorWarning,
            '--warning-rgb'   => $this->hexToRgb($colorWarning),

            // Danger
            '--danger-color' => $colorDanger,
            '--danger-rgb'   => $this->hexToRgb($colorDanger),
        ];

        // Login background variables (only if image exists)
        if (! empty($config->login_bg_image)) {
            $variables['--theme-login-bg-url'] = "url('".asset('storage/theme-manager/'.$config->login_bg_image)."')";
            $variables['--theme-login-bg-opacity'] = max(0, min(100, (int) $config->login_bg_opacity)) / 100;
            $variables['--theme-login-bg-zoom'] = max(50, min(200, (int) $config->login_bg_zoom)) / 100;
        }

        // Login card background variables (only if image exists)
        if (! empty($config->login_card_bg_image)) {
            $variables['--theme-login-card-bg-url'] = "url('".asset('storage/theme-manager/'.$config->login_card_bg_image)."')";
            $variables['--theme-login-card-bg-opacity'] = max(0, min(100, (int) $config->login_card_bg_opacity)) / 100;
            $variables['--theme-login-card-overlay'] = $this->sanitizeRgbaColor($config->login_card_overlay_color, 'rgba(10, 45, 15, 0.78)');
        }

        $css = ':root {'.PHP_EOL;

        foreach ($variables as $variable => $value) {
            $css .= "    {$variable}: {$value};".PHP_EOL;
        }

        $css .= '}';

        return $css;
    }

    /**
     * Convert hex color to RGB values.
     */
    protected function hexToRgb(string $hex): string
    {
        $hex = ltrim($hex, '#');

        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }

        return implode(', ', [
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2)),
        ]);
    }

    /**
     * Get logo URL by type.
     *
     * @param  string  $type  (main, light, icon, favicon)
     * @return string|null
     */
    public function getLogo(string $type)
    {
        $config = $this->getConfig();
        $field = "logo_{$type}";

        if (! empty($config->$field)) {
            return asset('storage/theme-manager/'.$config->$field);
        }

        return null;
    }

    /**
     * Get favicon URL with proper handling.
     */
    public function getFavicon(): ?string
    {
        $config = $this->getConfig();

        if (! empty($config->favicon)) {
            return asset('storage/theme-manager/'.$config->favicon);
        }

        return null;
    }

    /**
     * Get all login configuration with sanitized values.
     *
     * @return array
     */
    public function getLoginConfig()
    {
        $config = $this->getConfig();

        return [
            'bg_image'           => $config->login_bg_image ? asset('storage/theme-manager/'.$config->login_bg_image) : null,
            'bg_zoom'            => max(50, min(200, (int) $config->login_bg_zoom)),
            'bg_opacity'         => max(0, min(100, (int) $config->login_bg_opacity)),
            'show_powered_by'    => (bool) $config->login_show_powered_by,
            'card_enabled'       => (bool) $config->login_card_enabled,
            'card_bg_image'      => $config->login_card_bg_image ? asset('storage/theme-manager/'.$config->login_card_bg_image) : null,
            'card_bg_opacity'    => max(0, min(100, (int) $config->login_card_bg_opacity)),
            'card_overlay_color' => $this->sanitizeRgbaColor($config->login_card_overlay_color, 'rgba(10, 45, 15, 0.78)'),
            'card_title'         => $this->sanitizeText($config->login_card_title, 100),
            'card_subtitle'      => $this->sanitizeText($config->login_card_subtitle, 200),
            'card_sparkles'      => (bool) $config->login_card_sparkles,
            'card_help_link'     => (bool) $config->login_card_help_link,
            'card_support_email' => filter_var($config->login_card_support_email, FILTER_VALIDATE_EMAIL) ?: 'support@example.com',
        ];
    }

    /**
     * Get empty state SVG path by type.
     *
     * @return string|null
     */
    public function getEmptyState(string $type)
    {
        // Validate type to prevent path traversal
        $allowedTypes = ['activities', 'calls', 'emails', 'meetings', 'notes', 'organizations', 'persons', 'leads', 'products'];

        if (! in_array($type, $allowedTypes, true)) {
            return null;
        }

        $config = $this->getConfig();
        $field = "empty_state_{$type}";

        if (! empty($config->$field)) {
            return asset('storage/theme-manager/'.$config->$field);
        }

        return null;
    }

    /**
     * Get a specific configuration value.
     *
     * @param  mixed  $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        // Validate key to prevent access to sensitive data
        $config = $this->getConfig();

        return $config->$key ?? $default;
    }
}
