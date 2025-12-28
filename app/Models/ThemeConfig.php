<?php

namespace App\Models;

use Webkul\ThemeManager\Models\ThemeConfig as BaseThemeConfig;

/**
 * Extended ThemeConfig model with selected_theme support.
 *
 * This overrides the Webkul model to add missing fillable fields.
 */
class ThemeConfig extends BaseThemeConfig
{
    /**
     * The attributes that are mass assignable.
     * Extended to include selected_theme and previous_theme.
     *
     * @var array
     */
    protected $fillable = [
        'is_active',
        'selected_theme',      // Added
        'previous_theme',      // Added
        'color_primary',
        'color_primary_dark',
        'color_primary_light',
        'color_success',
        'color_warning',
        'color_danger',
        'logo_main',
        'logo_light',
        'logo_icon',
        'favicon',
        'login_bg_image',
        'login_bg_zoom',
        'login_bg_opacity',
        'login_show_powered_by',
        'login_card_enabled',
        'login_card_bg_image',
        'login_card_bg_opacity',
        'login_card_overlay_color',
        'login_card_title',
        'login_card_subtitle',
        'login_card_sparkles',
        'login_card_help_link',
        'login_card_support_email',
        'login_card_custom_code',
        'empty_state_activities',
        'empty_state_calls',
        'empty_state_emails',
        'empty_state_meetings',
        'empty_state_notes',
        'empty_state_organizations',
        'empty_state_persons',
        'empty_state_leads',
        'empty_state_products',
    ];

    /**
     * Get the singleton instance of ThemeConfig.
     * Returns the first record or creates it if it doesn't exist.
     *
     * @return self
     */
    public static function getInstance(): self
    {
        return static::firstOrCreate(['id' => 1], [
            'is_active' => false,
            'selected_theme' => 'default',
            'color_primary' => '#1E40AF',
            'color_primary_dark' => '#1E3A8A',
            'color_primary_light' => '#3B82F6',
            'color_success' => '#10B981',
            'color_warning' => '#F59E0B',
            'color_danger' => '#EF4444',
            'login_bg_zoom' => 100,
            'login_bg_opacity' => 50,
            'login_show_powered_by' => true,
            'login_card_enabled' => false,
            'login_card_bg_opacity' => 62,
            'login_card_overlay_color' => 'rgba(10, 45, 15, 0.78)',
            'login_card_title' => 'Bem-vindo',
            'login_card_subtitle' => 'Acesse sua conta para continuar',
            'login_card_sparkles' => false,
            'login_card_help_link' => true,
            'login_card_support_email' => 'suporte@empresa.com.br',
        ]);
    }
}
