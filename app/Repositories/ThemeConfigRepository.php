<?php

namespace App\Repositories;

use App\Models\ThemeConfig;
use Webkul\ThemeManager\Repositories\ThemeConfigRepository as BaseThemeConfigRepository;

/**
 * Extended ThemeConfigRepository that uses App\Models\ThemeConfig
 * instead of the Webkul model, ensuring selected_theme is fillable.
 */
class ThemeConfigRepository extends BaseThemeConfigRepository
{
    /**
     * Get the theme configuration.
     * Overrides parent to use our extended ThemeConfig model.
     *
     * @return ThemeConfig
     */
    public function get()
    {
        return ThemeConfig::getInstance();
    }
}
