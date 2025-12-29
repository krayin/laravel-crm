<?php

namespace Webkul\ThemeManager\Repositories;

use App\Support\ThemeAssetValidator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
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
     * Asset validator instance.
     *
     * @var ThemeAssetValidator
     */
    protected $assetValidator;

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
     * Validation errors from last operation.
     *
     * @var array
     */
    protected $validationErrors = [];

    /**
     * Validation warnings from last operation.
     *
     * @var array
     */
    protected $validationWarnings = [];

    /**
     * Create a new repository instance.
     *
     * @return void
     */
    public function __construct(ThemeConfig $themeConfig, ThemeHelper $themeHelper)
    {
        $this->themeConfig = $themeConfig;
        $this->themeHelper = $themeHelper;
        $this->assetValidator = new ThemeAssetValidator;
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

        // Se o tema selecionado mudou, carregar configurações do theme.json
        if (isset($data['selected_theme'])) {
            $newTheme = $data['selected_theme'];
            $currentTheme = $config->selected_theme ?? 'default';

            if ($newTheme !== $currentTheme) {
                // Salva o tema anterior
                $data['previous_theme'] = $currentTheme;

                // Carrega as configurações do novo tema
                $themeSettings = $this->loadThemeSettings($newTheme);

                // IMPORTANTE: Quando mudamos de tema, o theme.json tem prioridade
                // sobre os valores do formulário (que são do tema antigo).
                // Mantemos apenas: selected_theme, previous_theme, is_active, _token
                $keepFromForm = [
                    'selected_theme',
                    'previous_theme',
                    'is_active',
                    '_token',
                    '_method',
                ];

                $formDataToKeep = array_intersect_key($data, array_flip($keepFromForm));

                // Substitui $data pelos valores do tema + dados essenciais do form
                $data = array_merge($themeSettings, $formDataToKeep);

                Log::info('[Theme] Loading theme settings from theme.json', [
                    'theme'           => $newTheme,
                    'loaded_settings' => array_keys($themeSettings),
                    'kept_from_form'  => array_keys($formDataToKeep),
                ]);
            }
        }

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
                $this->deleteFile($config->$field);
                $data[$field] = null;
                unset($data["{$field}_delete"]);
            }
            // Handle new file upload - use instanceof for proper type checking
            elseif (isset($data[$field]) && $data[$field] instanceof UploadedFile) {
                /** @var UploadedFile $file */
                $file = $data[$field];

                // Determine field type for validation
                $fieldType = $this->getFieldType($field);

                // Validate using ThemeAssetValidator
                if (! $this->assetValidator->validateUploadedFile($file, $fieldType)) {
                    $errors = $this->assetValidator->getErrors();
                    $this->validationErrors[$field] = $errors;
                    Log::warning('[Theme] Asset validation failed', [
                        'field'  => $field,
                        'errors' => $errors,
                    ]);

                    continue;
                }

                // Collect warnings (non-blocking)
                $warnings = $this->assetValidator->getWarnings();
                if (! empty($warnings)) {
                    $this->validationWarnings[$field] = $warnings;
                    Log::info('[Theme] Asset validation warnings', [
                        'field'    => $field,
                        'warnings' => $warnings,
                    ]);
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
            }
            // Handle string filename (from theme.json - already copied)
            elseif (isset($data[$field]) && is_string($data[$field]) && ! empty($data[$field])) {
                // File was loaded from theme.json and already copied to theme-manager/
                // Keep the value as-is, don't unset it
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

        // Clear ThemeHelper cache
        $this->themeHelper->clearCache();

        // Clear ThemeCache (app-level caches) if available
        if (class_exists(\App\Support\ThemeCache::class)) {
            \App\Support\ThemeCache::flush();
        }

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

    /**
     * Load theme settings from theme.json file.
     * Maps theme.json fields to database column names and copies theme assets.
     */
    protected function loadThemeSettings(string $slug): array
    {
        // Tema 'default' não tem theme.json - retorna valores padrão
        if ($slug === 'default') {
            return $this->getDefaultSettings();
        }

        $themePath = "themes/{$slug}";
        $jsonPath = "{$themePath}/theme.json";

        if (! Storage::disk('public')->exists($jsonPath)) {
            Log::warning('[Theme] theme.json not found, using defaults', ['slug' => $slug]);

            return [];
        }

        try {
            $contents = Storage::disk('public')->get($jsonPath);
            $themeData = json_decode($contents, true);

            if (! is_array($themeData)) {
                Log::warning('[Theme] Invalid theme.json format', ['slug' => $slug]);

                return [];
            }

            $settings = [];

            // Campos diretos que mapeiam 1:1
            $directFields = [
                'color_primary',
                'color_primary_dark',
                'color_primary_light',
                'color_success',
                'color_warning',
                'color_danger',
            ];

            foreach ($directFields as $field) {
                if (isset($themeData[$field])) {
                    $settings[$field] = $themeData[$field];
                }
            }

            // Campos de login - suporta tanto formato flat (login_bg_zoom)
            // quanto aninhado (login: { bg_zoom })
            // NOTA: Campos de arquivo (bg_image, card_bg_image) são processados
            // separadamente mais abaixo na seção $loginFileFields
            $loginFields = [
                // 'bg_image' => 'login_bg_image',  // Processado em $loginFileFields
                'bg_zoom'         => 'login_bg_zoom',
                'bg_opacity'      => 'login_bg_opacity',
                'show_powered_by' => 'login_show_powered_by',
                'card_enabled'    => 'login_card_enabled',
                // 'card_bg_image' => 'login_card_bg_image',  // Processado em $loginFileFields
                'card_bg_opacity'    => 'login_card_bg_opacity',
                'card_overlay_color' => 'login_card_overlay_color',
                'card_title'         => 'login_card_title',
                'card_subtitle'      => 'login_card_subtitle',
                'card_sparkles'      => 'login_card_sparkles',
                'card_help_link'     => 'login_card_help_link',
                'card_support_email' => 'login_card_support_email',
            ];

            // Verifica formato aninhado (login: {})
            $loginData = $themeData['login'] ?? [];

            foreach ($loginFields as $jsonKey => $dbKey) {
                // Primeiro tenta o formato aninhado (login.bg_zoom)
                if (isset($loginData[$jsonKey])) {
                    $settings[$dbKey] = $loginData[$jsonKey];
                }
                // Depois tenta o formato flat (login_bg_zoom)
                elseif (isset($themeData[$dbKey])) {
                    $settings[$dbKey] = $themeData[$dbKey];
                }
            }

            // Campos de arquivo (logo, favicon)
            $fileFields = [
                'logo_main',
                'logo_light',
                'logo_icon',
                'favicon',
            ];

            foreach ($fileFields as $field) {
                if (! empty($themeData[$field])) {
                    $copied = $this->copyThemeAsset($themePath, $themeData[$field], $field);
                    if ($copied) {
                        $settings[$field] = $copied;
                    }
                }
            }

            // Arquivos de login (suporta formato aninhado)
            // IMPORTANTE: Estes campos são de arquivo, então removemos qualquer valor
            // de texto que possa ter sido definido acima e substituímos pelo arquivo copiado.
            $loginFileFields = [
                'bg_image'      => 'login_bg_image',
                'card_bg_image' => 'login_card_bg_image',
            ];

            foreach ($loginFileFields as $jsonKey => $dbKey) {
                $filename = $loginData[$jsonKey] ?? $themeData[$dbKey] ?? null;

                if (! empty($filename)) {
                    $copied = $this->copyThemeAsset($themePath, $filename, $dbKey);
                    if ($copied) {
                        $settings[$dbKey] = $copied;
                    } else {
                        // Arquivo não encontrado no tema - define como null para limpar valor antigo
                        $settings[$dbKey] = null;
                        Log::warning('[Theme] Asset file not found, setting to null', [
                            'field'         => $dbKey,
                            'expected_file' => $filename,
                        ]);
                    }
                }
            }

            Log::info('[Theme] Loaded settings from theme.json', [
                'slug'           => $slug,
                'settings_count' => count($settings),
            ]);

            return $settings;

        } catch (\Throwable $e) {
            Log::error('[Theme] Error loading theme.json', [
                'slug'  => $slug,
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * Copy a theme asset file to theme-manager folder.
     *
     * @return string|null New filename or null if failed
     */
    protected function copyThemeAsset(string $themePath, string $filename, string $field): ?string
    {
        $sourceFile = "{$themePath}/{$filename}";

        if (! Storage::disk('public')->exists($sourceFile)) {
            Log::debug('[Theme] Asset file not found', ['source' => $sourceFile]);

            return null;
        }

        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $timestamp = time();
        $random = Str::random(8);
        $newFilename = "{$timestamp}_{$field}_{$random}.{$extension}";

        $content = Storage::disk('public')->get($sourceFile);
        Storage::disk('public')->put("theme-manager/{$newFilename}", $content);

        Log::debug('[Theme] Copied asset file', [
            'from' => $sourceFile,
            'to'   => "theme-manager/{$newFilename}",
        ]);

        return $newFilename;
    }

    /**
     * Get default theme settings.
     */
    protected function getDefaultSettings(): array
    {
        return [
            'color_primary'            => '#1E40AF',
            'color_primary_dark'       => '#1E3A8A',
            'color_primary_light'      => '#3B82F6',
            'color_success'            => '#10B981',
            'color_warning'            => '#F59E0B',
            'color_danger'             => '#EF4444',
            'logo_main'                => null,
            'logo_light'               => null,
            'logo_icon'                => null,
            'favicon'                  => null,
            'login_bg_image'           => null,
            'login_bg_zoom'            => 100,
            'login_bg_opacity'         => 50,
            'login_show_powered_by'    => true,
            'login_card_enabled'       => false,
            'login_card_bg_image'      => null,
            'login_card_bg_opacity'    => 62,
            'login_card_overlay_color' => 'rgba(10, 45, 15, 0.78)',
            'login_card_title'         => 'Bem-vindo',
            'login_card_subtitle'      => 'Acesse sua conta para continuar',
            'login_card_sparkles'      => false,
            'login_card_help_link'     => true,
            'login_card_support_email' => 'suporte@empresa.com.br',
        ];
    }

    /**
     * Get field type for validation.
     */
    protected function getFieldType(string $field): string
    {
        if (str_starts_with($field, 'logo_')) {
            return 'logo';
        }

        if ($field === 'favicon') {
            return 'favicon';
        }

        if (str_contains($field, 'bg_image') || str_contains($field, 'bg')) {
            return 'background';
        }

        return 'default';
    }

    /**
     * Get validation errors from last update.
     */
    public function getValidationErrors(): array
    {
        return $this->validationErrors;
    }

    /**
     * Get validation warnings from last update.
     */
    public function getValidationWarnings(): array
    {
        return $this->validationWarnings;
    }

    /**
     * Check if last update had validation errors.
     */
    public function hasValidationErrors(): bool
    {
        return ! empty($this->validationErrors);
    }

    /**
     * Validate a theme directory.
     *
     * @return array ['valid' => bool, 'errors' => array, 'warnings' => array]
     */
    public function validateTheme(string $slug): array
    {
        $themePath = storage_path("app/public/themes/{$slug}");

        $isValid = $this->assetValidator->validateThemeDirectory($themePath);

        return [
            'valid'    => $isValid,
            'errors'   => $this->assetValidator->getErrors(),
            'warnings' => $this->assetValidator->getWarnings(),
        ];
    }
}
