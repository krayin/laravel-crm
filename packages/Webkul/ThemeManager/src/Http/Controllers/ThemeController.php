<?php

namespace Webkul\ThemeManager\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
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
     * @param  ThemeConfigRepository  $themeConfigRepository
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

        return view('theme-manager::admin.settings.theme.index', compact('config'));
    }

    /**
     * Update the theme configuration.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $this->validate($request, [
            // Ativação
            'is_active' => 'nullable|boolean',

            // Cores
            'color_primary' => 'nullable|string|max:20',
            'color_primary_dark' => 'nullable|string|max:20',
            'color_primary_light' => 'nullable|string|max:20',
            'color_success' => 'nullable|string|max:20',
            'color_warning' => 'nullable|string|max:20',
            'color_danger' => 'nullable|string|max:20',

            // Logos
            'logo_main' => 'nullable|image|max:5120',
            'logo_light' => 'nullable|image|max:5120',
            'logo_icon' => 'nullable|image|max:5120',
            'favicon' => 'nullable|image|max:5120',

            // Login Background
            'login_bg_image' => 'nullable|image|max:5120',
            'login_bg_zoom' => 'nullable|integer|min:50|max:200',
            'login_bg_opacity' => 'nullable|integer|min:0|max:100',
            'login_show_powered_by' => 'nullable|boolean',

            // Login Card
            'login_card_enabled' => 'nullable|boolean',
            'login_card_bg_image' => 'nullable|image|max:5120',
            'login_card_bg_opacity' => 'nullable|integer|min:0|max:100',
            'login_card_overlay_color' => 'nullable|string|max:50',
            'login_card_title' => 'nullable|string|max:100',
            'login_card_subtitle' => 'nullable|string|max:200',
            'login_card_sparkles' => 'nullable|boolean',
            'login_card_help_link' => 'nullable|boolean',
            'login_card_support_email' => 'nullable|email|max:100',

            // Empty States
            'empty_state_activities' => 'nullable|image|max:5120',
            'empty_state_calls' => 'nullable|image|max:5120',
            'empty_state_emails' => 'nullable|image|max:5120',
            'empty_state_meetings' => 'nullable|image|max:5120',
            'empty_state_notes' => 'nullable|image|max:5120',
            'empty_state_organizations' => 'nullable|image|max:5120',
            'empty_state_persons' => 'nullable|image|max:5120',
            'empty_state_leads' => 'nullable|image|max:5120',
            'empty_state_products' => 'nullable|image|max:5120',
        ]);

        $this->themeConfigRepository->update($request->all());

        session()->flash('success', trans('theme-manager::app.settings.update-success'));

        return redirect()->back();
    }
}
