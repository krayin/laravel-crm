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
     * Get the theme configuration with caching.
     *
     * @return ThemeConfig
     */
    public function getConfig()
    {
        return Cache::remember($this->cacheKey, $this->cacheTtl, function () {
            return ThemeConfig::getInstance();
        });
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
        return $this->getConfig()->is_active;
    }

    /**
     * Get CSS variables as a string for :root.
     *
     * @return string
     */
    public function getCssVariables()
    {
        $config = $this->getConfig();

        // Helper function to convert hex to RGB
        $hexToRgb = function ($hex) {
            $hex = ltrim($hex, '#');

            if (strlen($hex) === 3) {
                $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
            }

            return implode(', ', [
                hexdec(substr($hex, 0, 2)),
                hexdec(substr($hex, 2, 2)),
                hexdec(substr($hex, 4, 2))
            ]);
        };

        $variables = [
            // Primary colors
            '--primary-color' => $config->color_primary,
            '--primary-dark-color' => $config->color_primary_dark,
            '--primary-light-color' => $config->color_primary_light,
            '--primary-rgb' => $hexToRgb($config->color_primary),

            // Success
            '--success-color' => $config->color_success,
            '--success-rgb' => $hexToRgb($config->color_success),

            // Warning
            '--warning-color' => $config->color_warning,
            '--warning-rgb' => $hexToRgb($config->color_warning),

            // Danger
            '--danger-color' => $config->color_danger,
            '--danger-rgb' => $hexToRgb($config->color_danger),
        ];

        $css = ':root {' . PHP_EOL;

        foreach ($variables as $variable => $value) {
            $css .= "    {$variable}: {$value};" . PHP_EOL;
        }

        $css .= '}';

        return $css;
    }

    /**
     * Get logo URL by type.
     *
     * @param  string  $type (main, light, icon, favicon)
     * @return string|null
     */
    public function getLogo(string $type)
    {
        $config = $this->getConfig();
        $field = "logo_{$type}";

        if (! empty($config->$field)) {
            return asset('storage/theme-manager/' . $config->$field);
        }

        return null;
    }

    /**
     * Get all login configuration.
     *
     * @return array
     */
    public function getLoginConfig()
    {
        $config = $this->getConfig();

        return [
            'bg_image' => $config->login_bg_image ? asset('storage/theme-manager/' . $config->login_bg_image) : null,
            'bg_zoom' => $config->login_bg_zoom,
            'bg_opacity' => $config->login_bg_opacity,
            'show_powered_by' => $config->login_show_powered_by,
            'card_enabled' => $config->login_card_enabled,
            'card_bg_image' => $config->login_card_bg_image ? asset('storage/theme-manager/' . $config->login_card_bg_image) : null,
            'card_bg_opacity' => $config->login_card_bg_opacity,
            'card_overlay_color' => $config->login_card_overlay_color,
            'card_title' => $config->login_card_title,
            'card_subtitle' => $config->login_card_subtitle,
            'card_sparkles' => $config->login_card_sparkles,
            'card_help_link' => $config->login_card_help_link,
            'card_support_email' => $config->login_card_support_email,
        ];
    }

    /**
     * Get empty state SVG path by type.
     *
     * @param  string  $type
     * @return string|null
     */
    public function getEmptyState(string $type)
    {
        $config = $this->getConfig();
        $field = "empty_state_{$type}";

        if (! empty($config->$field)) {
            return asset('storage/theme-manager/' . $config->$field);
        }

        return null;
    }

    /**
     * Get a specific configuration value.
     *
     * @param  string  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        $config = $this->getConfig();

        return $config->$key ?? $default;
    }
}
