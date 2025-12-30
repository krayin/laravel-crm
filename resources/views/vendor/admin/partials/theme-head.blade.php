{{--
    Theme Head Partial - CSS Variables, Estilos Base e Custom CSS
    Injetado no <head> dos layouts admin e anonymous.
    Usa ThemeContext value object (sempre disponivel via middleware).
--}}

@if (isset($themeContext) && $themeContext->enabled)
{{-- CSS Variables (Brand Kit) --}}
@if (method_exists($themeContext, 'cssVariables'))
<style id="brand-kit-css-vars">{!! $themeContext->cssVariables() !!}</style>
@endif

{{-- CSS Variables estendidas (login/theme) --}}
<style id="theme-css-vars">
:root {
    /* Cores do tema (alias para compatibilidade) */
    --theme-primary: {{ $themeContext->get('color_primary', '#1E40AF') }};
    --theme-primary-dark: {{ $themeContext->get('color_primary_dark', '#1E3A8A') }};
    --theme-primary-light: {{ $themeContext->get('color_primary_light', '#3B82F6') }};
    --theme-success: {{ $themeContext->get('color_success', '#10B981') }};
    --theme-warning: {{ $themeContext->get('color_warning', '#F59E0B') }};
    --theme-danger: {{ $themeContext->get('color_danger', '#EF4444') }};

    /* Login background */
    @if($bgUrl = $themeContext->loginBgUrl())
    --theme-login-bg-url: url('{{ $bgUrl }}');
    @endif
    @php
        // Opacidade vem como 0-100, converte para 0-1
        // O overlay escurece, então inverte: 100% opacidade = overlay 0, 0% opacidade = overlay 1
        $bgOpacity = $themeContext->get('login_bg_opacity', 50);
        $bgOpacityNormalized = (100 - $bgOpacity) / 100;
        $bgZoom = $themeContext->get('login_bg_zoom', 100) / 100;
    @endphp
    --theme-login-bg-opacity: {{ $bgOpacityNormalized }};
    --theme-login-bg-zoom: {{ $bgZoom }};

    /* Login card */
    @if($themeContext->hasCustomCard())
    @if($cardBgUrl = $themeContext->loginCardBgUrl())
    --theme-login-card-bg-url: url('{{ $cardBgUrl }}');
    @endif
    @php
        $cardBgOpacity = $themeContext->get('login_card_bg_opacity', 62) / 100;
    @endphp
    --theme-login-card-bg-opacity: {{ $cardBgOpacity }};
    --theme-login-card-overlay: {{ $themeContext->get('login_card_overlay_color', 'rgba(10, 45, 15, 0.78)') }};
    @endif
}
</style>

{{-- Estilos Base do Tema (inline, nao depende de Vite) --}}
<style id="theme-login-styles">
/* ==========================================================================
   BACKGROUND VIA PSEUDO-ELEMENTS
   ========================================================================== */

body.theme-login-bg::before {
    content: '';
    position: fixed;
    inset: 0;
    z-index: 0;
    background-image: var(--theme-login-bg-url);
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    transform: scale(var(--theme-login-bg-zoom, 1));
    transform-origin: center center;
}

body.theme-login-bg::after {
    content: '';
    position: fixed;
    inset: 0;
    z-index: 1;
    background-color: rgba(0, 0, 0, var(--theme-login-bg-opacity, 0.5));
    pointer-events: none;
}

/* ==========================================================================
   LAYOUT PRINCIPAL
   ========================================================================== */

.theme-login-layer {
    position: relative;
    z-index: 10;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 2rem;
}

/* ==========================================================================
   LOGO
   ========================================================================== */

.theme-login-logo {
    margin-bottom: 2rem;
    text-align: center;
}

.theme-login-logo img {
    max-height: 60px;
    max-width: 200px;
    width: auto;
    height: auto;
}

body.theme-login-bg .theme-login-logo img {
    filter: drop-shadow(0 4px 6px rgba(0, 0, 0, 0.3));
}

/* ==========================================================================
   CARD DE LOGIN
   ========================================================================== */

.theme-login-card {
    width: 100%;
    max-width: 400px;
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
    overflow: hidden;
}

.dark .theme-login-card {
    background: #1f2937;
}

/* ==========================================================================
   CARD CUSTOMIZADO
   ========================================================================== */

body.theme-login-card-custom .theme-login-card {
    position: relative;
    overflow: hidden;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
}

/* Background image via pseudo-element */
body.theme-login-card-custom .theme-login-card::before {
    content: '';
    position: absolute;
    inset: 0;
    z-index: 0;
    background-image: var(--theme-login-card-bg-url);
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    opacity: var(--theme-login-card-bg-opacity, 0.62);
}

/* Overlay via pseudo-element */
body.theme-login-card-custom .theme-login-card::after {
    content: '';
    position: absolute;
    inset: 0;
    z-index: 1;
    background-color: var(--theme-login-card-overlay, rgba(10, 45, 15, 0.78));
}

body.theme-login-card-custom .theme-login-card-content {
    position: relative;
    z-index: 2;
}

/* ==========================================================================
   HEADER DO CARD
   ========================================================================== */

.theme-login-card-header {
    padding: 2.5rem 2rem 1.5rem;
    text-align: center;
}

body.theme-login-card-custom .theme-login-card-header {
    position: relative;
    z-index: 3;
    color: #ffffff;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.theme-login-card-title {
    margin: 0 0 0.5rem;
    font-size: 1.75rem;
    font-weight: 700;
    line-height: 1.2;
}

body:not(.theme-login-card-custom) .theme-login-card-title {
    color: #1f2937;
}

.dark body:not(.theme-login-card-custom) .theme-login-card-title {
    color: #ffffff;
}

.theme-login-card-subtitle {
    margin: 0;
    font-size: 1rem;
    opacity: 0.9;
}

body:not(.theme-login-card-custom) .theme-login-card-subtitle {
    color: #6b7280;
}

/* ==========================================================================
   CORPO DO CARD
   ========================================================================== */

.theme-login-card-body {
    padding: 1.5rem 2rem 2rem;
}

body.theme-login-card-custom .theme-login-card-body {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
}

body.theme-login-card-custom .theme-login-card-body:last-child {
    border-radius: 0 0 12px 12px;
}

/* ==========================================================================
   FORM ELEMENTS
   ========================================================================== */

.theme-login-form-group { margin-bottom: 1.25rem; }
.theme-login-label { display: block; margin-bottom: 0.5rem; font-size: 0.875rem; font-weight: 600; color: #374151; }
.dark .theme-login-label { color: #d1d5db; }
.theme-login-input { width: 100%; padding: 0.75rem 1rem; font-size: 1rem; border: 1px solid #d1d5db; border-radius: 8px; background: #ffffff; color: #1f2937; transition: border-color 0.2s, box-shadow 0.2s; }
.dark .theme-login-input { background: #374151; border-color: #4b5563; color: #ffffff; }
.theme-login-input:focus { outline: none; border-color: var(--theme-primary, #1E40AF); box-shadow: 0 0 0 3px rgba(30, 64, 175, 0.1); }
.theme-login-input-wrapper { position: relative; }
.theme-login-password-toggle { position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); cursor: pointer; font-size: 1.25rem; color: #6b7280; background: none; border: none; padding: 0; }

/* ==========================================================================
   ACTIONS
   ========================================================================== */

.theme-login-actions { display: flex; justify-content: space-between; align-items: center; margin-top: 1.5rem; }
.theme-login-forgot-link { font-size: 0.875rem; font-weight: 600; color: var(--theme-primary, #1E40AF); text-decoration: none; transition: color 0.2s; }
.theme-login-forgot-link:hover { color: var(--theme-primary-dark, #1E3A8A); text-decoration: underline; }
.theme-login-submit { padding: 0.75rem 2rem; font-size: 1rem; font-weight: 600; color: #ffffff; background: var(--theme-primary, #1E40AF); border: none; border-radius: 8px; cursor: pointer; transition: background 0.2s, transform 0.1s, box-shadow 0.2s; }
.theme-login-submit:hover { background: var(--theme-primary-dark, #1E3A8A); transform: translateY(-1px); box-shadow: 0 4px 12px rgba(30, 64, 175, 0.4); }
.theme-login-submit:active { transform: translateY(0); }

/* ==========================================================================
   HELP & POWERED BY
   ========================================================================== */

.theme-login-help { padding: 1rem 2rem; text-align: center; background: rgba(255, 255, 255, 0.9); border-top: 1px solid rgba(0, 0, 0, 0.1); }
.theme-login-help a { font-size: 0.875rem; font-weight: 600; color: var(--theme-primary, #1E40AF); text-decoration: none; }
.theme-login-help a:hover { text-decoration: underline; }
.theme-login-powered { margin-top: 2rem; text-align: center; font-size: 0.875rem; }
body.theme-login-bg .theme-login-powered { color: rgba(255, 255, 255, 0.9); text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3); }
body.theme-login-bg .theme-login-powered a { color: #ffffff; font-weight: 600; text-decoration: none; }
body:not(.theme-login-bg) .theme-login-powered { color: #6b7280; }
body:not(.theme-login-bg) .theme-login-powered a { color: var(--theme-primary, #1E40AF); font-weight: 600; text-decoration: none; }

/* ==========================================================================
   ERROR MESSAGES
   ========================================================================== */

.theme-login-error { color: var(--theme-danger, #EF4444); font-size: 0.875rem; margin-top: 0.25rem; }
.theme-login-alert { padding: 1rem; margin-bottom: 1rem; border-radius: 8px; font-size: 0.875rem; }
.theme-login-alert-error { background-color: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
.theme-login-alert-success { background-color: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }

/* ==========================================================================
   RESPONSIVE
   ========================================================================== */

@media (max-width: 480px) {
    .theme-login-layer { padding: 1rem; }
    .theme-login-card { max-width: 100%; }
    .theme-login-card-header { padding: 2rem 1.5rem 1rem; }
    .theme-login-card-title { font-size: 1.5rem; }
    .theme-login-card-body { padding: 1rem 1.5rem 1.5rem; }
    .theme-login-actions { flex-direction: column; gap: 1rem; }
    .theme-login-submit { width: 100%; }
}
</style>

{{-- CSS externo do tema (opcional) --}}
@if (method_exists($themeContext, 'cssUrl') && $themeContext->cssUrl())
<link rel="stylesheet" href="{{ $themeContext->cssUrl() }}">
@endif

{{-- Custom CSS (Admin ou Login, dependendo da rota) --}}
@php
    $isLogin = request()->routeIs('admin.session.create') || str_contains(url()->current(), '/login');
    $customCss = $isLogin ? ($themeContext->customCssLogin ?? '') : ($themeContext->customCssAdmin ?? '');
@endphp

@if (!empty($customCss))
<style id="brand-kit-custom-css">{!! $customCss !!}</style>
@endif

@endif
