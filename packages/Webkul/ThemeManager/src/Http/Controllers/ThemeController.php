<?php

namespace Webkul\ThemeManager\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\ThemeManager\Repositories\ThemeConfigRepository;

class ThemeController extends Controller
{
    /**
     * ThemeConfigRepository instance.
     *
     * @var ThemeConfigRepository
     */
    protected $themeConfigRepository;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ThemeConfigRepository $themeConfigRepository)
    {
        $this->themeConfigRepository = $themeConfigRepository;
    }

    /**
     * Display the theme configuration form.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $config = $this->themeConfigRepository->get();
        $availableThemes = $this->getAvailableThemes();

        // Preparar dados para JavaScript com cores garantidas
        $themesForJs = [];
        foreach ($availableThemes as $theme) {
            $themesForJs[$theme['slug']] = [
                'slug' => $theme['slug'],
                'name' => $theme['name'] ?? 'Unnamed',
                'colors' => $theme['colors'] ?? [
                    'primary' => '#1E40AF',
                    'primary_dark' => '#1E3A8A',
                    'primary_light' => '#3B82F6',
                    'success' => '#10B981',
                    'warning' => '#F59E0B',
                    'danger' => '#EF4444'
                ]
            ];
        }

        return view(
            'theme-manager::admin.settings.theme.index',
            compact('config', 'availableThemes', 'themesForJs'),
        );
    }

    /**
     * Get available themes from storage.
     *
     * @return array
     */
    protected function getAvailableThemes(): array
    {
        $themesPath = storage_path('app/public/themes');
        $themes = [];

        // Always add 'default' theme first
        $themes['default'] = [
            'slug'        => 'default',
            'name'        => 'Padrão Krayin',
            'version'     => '1.0.0',
            'description' => 'Tema padrão do sistema - visual limpo e moderno',
            'colors'      => [
                'primary'       => '#1E40AF',
                'primary_dark'  => '#1E3A8A',
                'primary_light' => '#3B82F6',
                'success'       => '#10B981',
                'warning'       => '#F59E0B',
                'danger'        => '#EF4444',
            ],
        ];

        if (! File::isDirectory($themesPath)) {
            return $themes;
        }

        $directories = File::directories($themesPath);

        foreach ($directories as $directory) {
            $themeJsonPath = $directory.'/theme.json';
            $slug = basename($directory);

            // Skip 'default' folder if exists (we already added it)
            if ($slug === 'default') {
                continue;
            }

            if (File::exists($themeJsonPath)) {
                $themeData = json_decode(File::get($themeJsonPath), true);

                $themes[$slug] = [
                    'slug'        => $slug,
                    'name'        => $themeData['name'] ?? ucfirst($slug),
                    'version'     => $themeData['version'] ?? '1.0.0',
                    'description' => $themeData['description'] ?? '',
                    'colors'      => [
                        'primary'       => $themeData['color_primary'] ?? '#1E40AF',
                        'primary_dark'  => $themeData['color_primary_dark'] ?? '#1E3A8A',
                        'primary_light' => $themeData['color_primary_light'] ?? '#3B82F6',
                        'success'       => $themeData['color_success'] ?? '#10B981',
                        'warning'       => $themeData['color_warning'] ?? '#F59E0B',
                        'danger'        => $themeData['color_danger'] ?? '#EF4444',
                    ],
                ];
            }
        }

        return $themes;
    }

    /**
     * Update the theme configuration.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        // Hex color regex pattern
        $hexColorRegex = 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/';

        // RGBA color regex pattern
        $rgbaColorRegex =
            'regex:/^rgba?\(\s*\d{1,3}\s*,\s*\d{1,3}\s*,\s*\d{1,3}\s*(,\s*(0|1|0?\.\d+))?\s*\)$/';

        $this->validate($request, [
            // Ativação
            'is_active' => 'nullable|in:0,1',

            // Tema selecionado (slug sanitizado)
            'selected_theme' => 'nullable|string|max:50|regex:/^[a-z0-9\-_]+$/',

            // Cores - validação com regex para prevenir CSS injection
            'color_primary'      => ['nullable', 'string', 'max:7', $hexColorRegex],
            'color_primary_dark' => [
                'nullable',
                'string',
                'max:7',
                $hexColorRegex,
            ],
            'color_primary_light' => [
                'nullable',
                'string',
                'max:7',
                $hexColorRegex,
            ],
            'color_success' => ['nullable', 'string', 'max:7', $hexColorRegex],
            'color_warning' => ['nullable', 'string', 'max:7', $hexColorRegex],
            'color_danger'  => ['nullable', 'string', 'max:7', $hexColorRegex],

            // Logos - validação de mimes específicos
            'logo_main'  => 'nullable|file|mimes:svg,png,jpg,jpeg,webp|max:5120',
            'logo_light' => 'nullable|file|mimes:svg,png,jpg,jpeg,webp|max:5120',
            'logo_icon'  => 'nullable|file|mimes:svg,png,jpg,jpeg,ico|max:5120',
            'favicon'    => 'nullable|file|mimes:ico,png,svg|max:1024',

            // Login Background
            'login_bg_image'        => 'nullable|file|mimes:jpg,jpeg,png,webp|max:10240',
            'login_bg_zoom'         => 'nullable|integer|min:50|max:200',
            'login_bg_opacity'      => 'nullable|integer|min:0|max:100',
            'login_show_powered_by' => 'nullable|in:0,1',

            // Login Card
            'login_card_enabled'       => 'nullable|in:0,1',
            'login_card_bg_image'      => 'nullable|file|mimes:jpg,jpeg,png,webp|max:10240',
            'login_card_bg_opacity'    => 'nullable|integer|min:0|max:100',
            'login_card_overlay_color' => [
                'nullable',
                'string',
                'max:50',
                $rgbaColorRegex,
            ],
            'login_card_title'         => 'nullable|string|max:100',
            'login_card_subtitle'      => 'nullable|string|max:200',
            'login_card_sparkles'      => 'nullable|in:0,1',
            'login_card_help_link'     => 'nullable|in:0,1',
            'login_card_support_email' => 'nullable|email|max:100',
            'login_card_custom_code'   => 'nullable|string',

            // Empty States - apenas SVG para prevenir problemas
            'empty_state_activities'    => 'nullable|file|mimes:svg|max:2048',
            'empty_state_calls'         => 'nullable|file|mimes:svg|max:2048',
            'empty_state_emails'        => 'nullable|file|mimes:svg|max:2048',
            'empty_state_meetings'      => 'nullable|file|mimes:svg|max:2048',
            'empty_state_notes'         => 'nullable|file|mimes:svg|max:2048',
            'empty_state_organizations' => 'nullable|file|mimes:svg|max:2048',
            'empty_state_persons'       => 'nullable|file|mimes:svg|max:2048',
            'empty_state_leads'         => 'nullable|file|mimes:svg|max:2048',
            'empty_state_products'      => 'nullable|file|mimes:svg|max:2048',
        ]);

        // Event before update - allows other packages to react or modify data
        Event::dispatch('theme.update.before', $request->all());

        // NOTA: A lógica de delete de imagens está no Repository (ThemeConfigRepository)
        // nas linhas 148-154. O Repository processa campos *_delete automaticamente.

        // Merge request data with uploaded files and update
        $config = $this->themeConfigRepository->update(
            array_merge($request->all(), $request->allFiles()),
        );

        // Event after update - allows other packages to react to theme changes
        Event::dispatch('theme.update.after', $config);

        session()->flash(
            'success',
            trans('theme-manager::app.settings.update-success'),
        );

        return redirect()->back();
    }

    /**
     * Restore theme to default settings.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore()
    {
        $config = $this->themeConfigRepository->get();

        // Save current as previous before restoring
        $currentTheme = $config->selected_theme ?? 'default';

        // Reset to defaults
        $config->update([
            'is_active'      => false,
            'selected_theme' => 'default',
            'previous_theme' => $currentTheme,
        ]);

        // Dispatch event for cache invalidation
        Event::dispatch('theme.update.after', $config);

        // Clear theme caches
        if (class_exists(\App\Support\ThemeCache::class)) {
            \App\Support\ThemeCache::flush();
        }

        session()->flash(
            'success',
            trans('theme-manager::app.settings.restore-success', [], 'Tema restaurado para o padrão com sucesso.'),
        );

        return redirect()->route('admin.settings.theme.index');
    }

    /**
     * Rollback to previous theme.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function rollback()
    {
        $config = $this->themeConfigRepository->get();

        $previousTheme = $config->previous_theme ?? 'default';
        $currentTheme = $config->selected_theme ?? 'default';

        // Swap themes
        $config->update([
            'selected_theme' => $previousTheme,
            'previous_theme' => $currentTheme,
        ]);

        // Dispatch event for cache invalidation
        Event::dispatch('theme.update.after', $config);

        // Clear theme caches
        if (class_exists(\App\Support\ThemeCache::class)) {
            \App\Support\ThemeCache::flush();
        }

        session()->flash(
            'success',
            trans('theme-manager::app.settings.rollback-success', [], 'Tema revertido para: '.$previousTheme),
        );

        return redirect()->route('admin.settings.theme.index');
    }

    /**
     * Reset a specific field to theme default value.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resetField(Request $request)
    {
        // Validate field name - only allow specific image fields
        $this->validate($request, [
            'field_name' => [
                'required',
                'string',
                Rule::in([
                    'logo_main',
                    'logo_light',
                    'logo_icon',
                    'favicon',
                    'login_bg_image',
                    'login_card_bg_image',
                ]),
            ],
        ]);

        $fieldName = $request->input('field_name');

        // Reset field via Repository
        $success = $this->themeConfigRepository->resetFieldToTheme($fieldName);

        if ($success) {
            session()->flash(
                'success',
                '✅ Campo restaurado para o tema com sucesso!'
            );
        } else {
            session()->flash(
                'warning',
                '⚠️ O tema selecionado não possui valor para este campo. Campo foi limpo.'
            );
        }

        // Dispatch event for cache invalidation
        Event::dispatch('theme.field.reset', $fieldName);

        return redirect()->back();
    }
}
