<?php

namespace Webkul\ThemeManager\Repositories;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Webkul\ThemeManager\Helpers\ThemeHelper;
use Webkul\ThemeManager\Models\ThemeConfig;

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
     * Allowed file extensions for different field types.
     *
     * @var array
     */
    protected $allowedExtensions = [
        'logo'        => ['svg', 'png', 'jpg', 'jpeg', 'webp'],
        'favicon'     => ['ico', 'png', 'svg'],
        'background'  => ['jpg', 'jpeg', 'png', 'webp'],
        'empty_state' => ['svg'],
    ];

    /**
     * Create a new repository instance.
     *
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
            // DEBUG - Remover depois
            if ($field === 'login_bg_image') {
                \Log::info('🔍 DEBUG login_bg_image', [
                    'field'           => $field,
                    'isset'           => isset($data[$field]),
                    'is_uploadedfile' => isset($data[$field]) && $data[$field] instanceof UploadedFile,
                    'data_type'       => isset($data[$field]) ? get_class($data[$field]) : 'not set',
                    'all_data_keys'   => array_keys($data),
                ]);
            }

            // Handle delete checkbox
            if (isset($data["{$field}_delete"]) && $data["{$field}_delete"]) {
                $this->deleteFile($config->$field);
                $data[$field] = null;
                unset($data["{$field}_delete"]);
            }
            // Handle new file upload - use instanceof for proper type checking
            elseif (isset($data[$field]) && $data[$field] instanceof UploadedFile) {
                /** @var UploadedFile $file */
                $file = $data[$field];

                // Validate file extension
                if (! $this->isAllowedExtension($field, $file->getClientOriginalExtension())) {
                    continue;
                }

                // Validate file is not empty
                if ($file->getSize() === 0) {
                    continue;
                }

                // Delete old file
                $this->deleteFile($config->$field);

                // Generate safe filename
                $filename = $this->generateSafeFilename($field, $file);

                // Sanitize SVG files to prevent XSS
                if (strtolower($file->getClientOriginalExtension()) === 'svg') {
                    $content = $this->sanitizeSvg($file->get());
                    Storage::disk('public')->put('theme-manager/'.$filename, $content);
                } else {
                    $file->storeAs('theme-manager', $filename, 'public');
                }

                $data[$field] = $filename;
            } else {
                // Keep existing value - remove from update data
                unset($data[$field]);
            }
        }

        // Remove delete checkboxes from data
        $data = array_filter($data, function ($key) {
            return ! str_ends_with($key, '_delete');
        }, ARRAY_FILTER_USE_KEY);

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

        // Sanitize integer fields
        $integerFields = [
            'login_bg_zoom'         => [50, 200, 100],
            'login_bg_opacity'      => [0, 100, 50],
            'login_card_bg_opacity' => [0, 100, 62],
        ];

        foreach ($integerFields as $field => [$min, $max, $default]) {
            if (isset($data[$field])) {
                $data[$field] = max($min, min($max, (int) $data[$field]));
            }
        }

        // Update configuration
        $config->update($data);

        // Clear cache
        $this->themeHelper->clearCache();

        return $config;
    }

    /**
     * Delete a file from storage.
     */
    protected function deleteFile(?string $filename): void
    {
        if ($filename) {
            Storage::disk('public')->delete('theme-manager/'.$filename);
        }
    }

    /**
     * Generate a safe filename for uploaded file.
     */
    protected function generateSafeFilename(string $field, UploadedFile $file): string
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $timestamp = time();
        $random = Str::random(8);

        return "{$timestamp}_{$field}_{$random}.{$extension}";
    }

    /**
     * Check if file extension is allowed for the field type.
     */
    protected function isAllowedExtension(string $field, string $extension): bool
    {
        $extension = strtolower($extension);

        if (str_starts_with($field, 'logo_')) {
            return in_array($extension, $this->allowedExtensions['logo'], true);
        }

        if ($field === 'favicon') {
            return in_array($extension, $this->allowedExtensions['favicon'], true);
        }

        if (str_contains($field, 'bg_image')) {
            return in_array($extension, $this->allowedExtensions['background'], true);
        }

        if (str_starts_with($field, 'empty_state_')) {
            return in_array($extension, $this->allowedExtensions['empty_state'], true);
        }

        return false;
    }

    /**
     * Sanitize SVG content to prevent XSS attacks.
     * Removes potentially dangerous elements and attributes.
     */
    protected function sanitizeSvg(string $content): string
    {
        // Remove XML declaration and DOCTYPE
        $content = preg_replace('/<\?xml[^>]*\?>/i', '', $content);
        $content = preg_replace('/<!DOCTYPE[^>]*>/i', '', $content);

        // Remove script tags and their contents
        $content = preg_replace('/<script[^>]*>.*?<\/script>/is', '', $content);

        // Remove event handlers (onclick, onload, onerror, etc.)
        $content = preg_replace('/\s+on\w+\s*=\s*["\'][^"\']*["\']/i', '', $content);
        $content = preg_replace('/\s+on\w+\s*=\s*[^\s>]*/i', '', $content);

        // Remove javascript: and data: URLs in href and xlink:href
        $content = preg_replace('/href\s*=\s*["\']?\s*javascript:[^"\'>\s]*/i', 'href="#"', $content);
        $content = preg_replace('/xlink:href\s*=\s*["\']?\s*javascript:[^"\'>\s]*/i', 'xlink:href="#"', $content);
        $content = preg_replace('/href\s*=\s*["\']?\s*data:[^"\'>\s]*/i', 'href="#"', $content);

        // Remove use elements that reference external files
        $content = preg_replace('/<use[^>]*xlink:href\s*=\s*["\'][^#][^"\']*["\'][^>]*>/i', '', $content);

        // Remove foreignObject elements (can contain HTML)
        $content = preg_replace('/<foreignObject[^>]*>.*?<\/foreignObject>/is', '', $content);

        // Remove set and animate elements with event handlers
        $content = preg_replace('/<set[^>]*>/i', '', $content);
        $content = preg_replace('/<animate[^>]*on\w+[^>]*>/i', '', $content);

        return trim($content);
    }
}
