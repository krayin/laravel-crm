<?php

namespace Webkul\ThemeManager\Repositories;

use Illuminate\Support\Facades\Storage;
use Webkul\ThemeManager\Models\ThemeConfig;
use Webkul\ThemeManager\Helpers\ThemeHelper;

class ThemeConfigRepository
{
    /**
     * ThemeConfig model instance.
     *
     * @var ThemeConfig
     */
    protected $themeConfig;

    /**
     * ThemeHelper instance.
     *
     * @var ThemeHelper
     */
    protected $themeHelper;

    /**
     * Create a new repository instance.
     *
     * @param  ThemeConfig  $themeConfig
     * @param  ThemeHelper  $themeHelper
     * @return void
     */
    public function __construct(ThemeConfig $themeConfig, ThemeHelper $themeHelper)
    {
        $this->themeConfig = $themeConfig;
        $this->themeHelper = $themeHelper;
    }

    /**
     * Get the theme configuration.
     *
     * @return ThemeConfig
     */
    public function get()
    {
        return ThemeConfig::getInstance();
    }

    /**
     * Update the theme configuration.
     *
     * @param  array  $data
     * @return ThemeConfig
     */
    public function update(array $data)
    {
        $config = $this->get();

        // Handle file uploads
        $fileFields = [
            'logo_main',
            'logo_light',
            'logo_icon',
            'favicon',
            'login_bg_image',
            'login_card_bg_image',
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

        foreach ($fileFields as $field) {
            // Handle delete checkbox
            if (isset($data["{$field}_delete"]) && $data["{$field}_delete"]) {
                // Delete old file
                if ($config->$field) {
                    Storage::disk('public')->delete('theme-manager/' . $config->$field);
                }
                $data[$field] = null;
            }
            // Handle new file upload
            elseif (isset($data[$field]) && is_file($data[$field])) {
                // Delete old file
                if ($config->$field) {
                    Storage::disk('public')->delete('theme-manager/' . $config->$field);
                }

                // Store new file
                $file = $data[$field];
                $filename = time() . '_' . $field . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('theme-manager', $filename, 'public');
                $data[$field] = basename($path);
            } else {
                // Keep existing value
                unset($data[$field]);
            }
        }

        // Convert boolean fields
        $booleanFields = [
            'is_active',
            'login_show_powered_by',
            'login_card_enabled',
            'login_card_sparkles',
            'login_card_help_link',
        ];

        foreach ($booleanFields as $field) {
            if (isset($data[$field])) {
                $data[$field] = in_array($data[$field], ['on', '1', 1, true, 'true'], true);
            } else {
                $data[$field] = false;
            }
        }

        // Update configuration
        $config->update($data);

        // Clear cache
        $this->themeHelper->clearCache();

        return $config;
    }
}
