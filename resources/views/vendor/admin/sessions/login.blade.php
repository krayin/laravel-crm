{{--
    Login View Override (Upgrade-Safe)

    Este arquivo sobrescreve: packages/Webkul/Admin/src/Resources/views/sessions/login.blade.php
    Registrado via: App\Providers\ThemeBootProvider (View::prependNamespace)

    Comportamento:
    - $themeContext->enabled = true  → Renderiza login temático
    - $themeContext->enabled = false → Renderiza login padrão Krayin
--}}

@php
    // ThemeContext é injetado via ShareThemeContext middleware
    // Fallback seguro caso não exista
    $themeContext = $themeContext ?? \App\Support\ThemeContext::disabled();
@endphp

@if(!$themeContext->enabled)
    {{-- ================================================================
         TEMA DESATIVADO: Usa layout padrão do Admin (100% Krayin)
         ================================================================ --}}
    <x-admin::layouts.anonymous>
        <x-slot:title>
            @lang('admin::app.users.login.title')
        </x-slot>

        <div class="flex h-[100vh] flex-col items-center justify-center gap-10">
            <div class="flex flex-col items-center gap-5">
                @if ($logo = core()->getConfigData('general.design.admin_logo.logo_image'))
                    <img
                        class="h-10 w-[110px]"
                        src="{{ Storage::url($logo) }}"
                        alt="{{ config('app.name') }}"
                    />
                @else
                    <img
                        class="w-max"
                        src="{{ vite()->asset('images/logo.svg') }}"
                        alt="{{ config('app.name') }}"
                    />
                @endif

                <div class="box-shadow flex min-w-[300px] flex-col rounded-md bg-white dark:bg-gray-900">
                    {!! view_render_event('admin.sessions.login.form_controls.before') !!}

                    <x-admin::form :action="route('admin.session.store')">
                        <p class="p-4 text-xl font-bold text-gray-800 dark:text-white">
                            @lang('admin::app.users.login.title')
                        </p>

                        <div class="border-y p-4 dark:border-gray-800">
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label class="required">
                                    @lang('admin::app.users.login.email')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="email"
                                    class="w-[254px] max-w-full"
                                    id="email"
                                    name="email"
                                    rules="required|email"
                                    :label="trans('admin::app.users.login.email')"
                                    :placeholder="trans('admin::app.users.login.email')"
                                />

                                <x-admin::form.control-group.error control-name="email" />
                            </x-admin::form.control-group>

                            <x-admin::form.control-group class="relative w-full">
                                <x-admin::form.control-group.label class="required">
                                    @lang('admin::app.users.login.password')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="password"
                                    class="w-[254px] max-w-full ltr:pr-10 rtl:pl-10"
                                    id="password"
                                    name="password"
                                    rules="required|min:6"
                                    :label="trans('admin::app.users.login.password')"
                                    :placeholder="trans('admin::app.users.login.password')"
                                />

                                <span
                                    class="icon-eye-hide absolute top-11 -translate-y-2/4 cursor-pointer text-2xl ltr:right-3 rtl:left-3"
                                    onclick="switchVisibility()"
                                    id="visibilityIcon"
                                    role="presentation"
                                    tabindex="0"
                                >
                                </span>

                                <x-admin::form.control-group.error control-name="password" />
                            </x-admin::form.control-group>
                        </div>

                        <div class="flex items-center justify-between p-4">
                            <a
                                class="cursor-pointer text-xs font-semibold leading-6 text-brandColor"
                                href="{{ route('admin.forgot_password.create') }}"
                            >
                                @lang('admin::app.users.login.forget-password-link')
                            </a>

                            <button
                                class="primary-button"
                                aria-label="{{ trans('admin::app.users.login.submit-btn')}}"
                            >
                                @lang('admin::app.users.login.submit-btn')
                            </button>
                        </div>
                    </x-admin::form>

                    {!! view_render_event('admin.sessions.login.form_controls.after') !!}
                </div>
            </div>

            <div class="text-sm font-normal">
                @lang('admin::app.components.layouts.powered-by.description', [
                    'krayin' => '<a class="text-brandColor hover:underline " href="https://krayincrm.com/">Krayin</a>',
                    'webkul' => '<a class="text-brandColor hover:underline " href="https://webkul.com/">Webkul</a>',
                ])
            </div>
        </div>

        @push('scripts')
            <script>
                function switchVisibility() {
                    let passwordField = document.getElementById("password");
                    let visibilityIcon = document.getElementById("visibilityIcon");

                    passwordField.type = passwordField.type === "password" ? "text" : "password";
                    visibilityIcon.classList.toggle("icon-eye");
                }
            </script>
        @endpush
    </x-admin::layouts.anonymous>

@else
    {{-- ================================================================
         TEMA ATIVADO: Renderiza login temático completo
         Background via body::before/::after (CSS, não markup)
         ================================================================ --}}
    <!DOCTYPE html>
    <html
        lang="{{ app()->getLocale() }}"
        dir="{{ in_array(app()->getLocale(), ['fa', 'ar']) ? 'rtl' : 'ltr' }}"
        class="{{ request()->cookie('dark_mode') ? 'dark' : '' }}"
    >
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@lang('admin::app.users.login.title') - {{ config('app.name') }}</title>

        {{-- Favicon --}}
        @if($favicon = $themeContext->get('favicon'))
            @if($faviconUrl = Storage::disk('public')->url("theme-manager/{$favicon}"))
                <link rel="icon" href="{{ $faviconUrl }}" type="image/x-icon">
            @endif
        @else
            <link rel="icon" href="{{ vite()->asset('images/favicon.ico') }}" type="image/x-icon">
        @endif

        {{-- Krayin Admin Assets --}}
        {{ vite()->set(['src/Resources/assets/css/app.css', 'src/Resources/assets/js/app.js']) }}

        {{-- Theme CSS Variables e Estilos (inline, não depende de build) --}}
        @include('admin::partials.theme-head')
    </head>
    <body class="{{ $themeContext->bodyClasses() }}">
        {{--
            Background e Overlay são renderizados via CSS (body::before e body::after)
            Definidos em theme-head.blade.php
            Nenhum markup extra necessário aqui
        --}}

        <div class="theme-login-layer">
            {{-- Logo --}}
            <div class="theme-login-logo">
                @php
                    $logoUrl = $themeContext->logo('main')
                            ?? $themeContext->logo('light')
                            ?? (core()->getConfigData('general.design.admin_logo.logo_image')
                                ? Storage::url(core()->getConfigData('general.design.admin_logo.logo_image'))
                                : null)
                            ?? vite()->asset('images/logo.svg');
                @endphp
                <img src="{{ $logoUrl }}" alt="{{ config('app.name') }}">
            </div>

            {{-- Card de Login --}}
            <div class="theme-login-card">
                {{-- Sparkles (se habilitado) --}}
                @if($themeContext->login('card_sparkles'))
                    <div class="theme-login-sparkles">
                        <div class="theme-login-sparkle"></div>
                        <div class="theme-login-sparkle"></div>
                        <div class="theme-login-sparkle"></div>
                        <div class="theme-login-sparkle"></div>
                        <div class="theme-login-sparkle"></div>
                        <div class="theme-login-sparkle"></div>
                        <div class="theme-login-sparkle"></div>
                        <div class="theme-login-sparkle"></div>
                    </div>
                @endif

                <div class="theme-login-card-content">
                    {{-- Header do Card (se card customizado habilitado) --}}
                    @if($themeContext->hasCustomCard())
                        <div class="theme-login-card-header">
                            <h1 class="theme-login-card-title">
                                {{ $themeContext->login('card_title') ?: __('admin::app.users.login.title') }}
                            </h1>
                            @if($subtitle = $themeContext->login('card_subtitle'))
                                <p class="theme-login-card-subtitle">{{ $subtitle }}</p>
                            @endif
                        </div>
                    @endif

                    {{-- Corpo do Card (Formulário) --}}
                    <div class="theme-login-card-body">
                        {!! view_render_event('admin.sessions.login.form_controls.before') !!}

                        {{-- Título (se card NÃO customizado) --}}
                        @if(!$themeContext->hasCustomCard())
                            <h2 class="theme-login-card-title" style="margin-bottom: 1.5rem;">
                                @lang('admin::app.users.login.title')
                            </h2>
                        @endif

                        {{-- Flash Messages --}}
                        @if(session('error'))
                            <div class="theme-login-alert theme-login-alert-error">
                                {{ session('error') }}
                            </div>
                        @endif

                        @if(session('success'))
                            <div class="theme-login-alert theme-login-alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        {{-- Validation Errors --}}
                        @if($errors->any())
                            <div class="theme-login-alert theme-login-alert-error">
                                @foreach($errors->all() as $error)
                                    <p style="margin: 0;">{{ $error }}</p>
                                @endforeach
                            </div>
                        @endif

                        {{-- Formulário de Login --}}
                        <form method="POST" action="{{ route('admin.session.store') }}">
                            @csrf

                            {{-- Email --}}
                            <div class="theme-login-form-group">
                                <label for="email" class="theme-login-label">
                                    @lang('admin::app.users.login.email') <span style="color: var(--theme-danger);">*</span>
                                </label>
                                <input
                                    type="email"
                                    id="email"
                                    name="email"
                                    class="theme-login-input"
                                    placeholder="@lang('admin::app.users.login.email')"
                                    value="{{ old('email') }}"
                                    required
                                    autofocus
                                >
                                @error('email')
                                    <span class="theme-login-error">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Password --}}
                            <div class="theme-login-form-group">
                                <label for="password" class="theme-login-label">
                                    @lang('admin::app.users.login.password') <span style="color: var(--theme-danger);">*</span>
                                </label>
                                <div class="theme-login-input-wrapper">
                                    <input
                                        type="password"
                                        id="password"
                                        name="password"
                                        class="theme-login-input"
                                        placeholder="@lang('admin::app.users.login.password')"
                                        required
                                        style="padding-right: 3rem;"
                                    >
                                    <button
                                        type="button"
                                        class="theme-login-password-toggle icon-eye-hide"
                                        id="visibilityIcon"
                                        onclick="switchVisibility()"
                                        aria-label="Toggle password visibility"
                                    ></button>
                                </div>
                                @error('password')
                                    <span class="theme-login-error">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Actions --}}
                            <div class="theme-login-actions">
                                <a href="{{ route('admin.forgot_password.create') }}" class="theme-login-forgot-link">
                                    @lang('admin::app.users.login.forget-password-link')
                                </a>

                                <button type="submit" class="theme-login-submit">
                                    @lang('admin::app.users.login.submit-btn')
                                </button>
                            </div>
                        </form>

                        {!! view_render_event('admin.sessions.login.form_controls.after') !!}
                    </div>

                    {{-- Help Link (se habilitado) --}}
                    @if($themeContext->hasCustomCard() && $themeContext->login('card_help_link'))
                        <div class="theme-login-help">
                            <a href="mailto:{{ $themeContext->login('card_support_email', 'suporte@empresa.com.br') }}">
                                @lang('Precisa de ajuda? Contate o suporte')
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Powered By --}}
            @if($themeContext->showPoweredBy())
                <div class="theme-login-powered">
                    @lang('admin::app.components.layouts.powered-by.description', [
                        'krayin' => '<a href="https://krayincrm.com/">Krayin</a>',
                        'webkul' => '<a href="https://webkul.com/">Webkul</a>',
                    ])
                </div>
            @endif
        </div>

        {{-- Password Toggle Script --}}
        <script>
            function switchVisibility() {
                const passwordField = document.getElementById("password");
                const visibilityIcon = document.getElementById("visibilityIcon");

                if (passwordField.type === "password") {
                    passwordField.type = "text";
                    visibilityIcon.classList.remove("icon-eye-hide");
                    visibilityIcon.classList.add("icon-eye");
                } else {
                    passwordField.type = "password";
                    visibilityIcon.classList.remove("icon-eye");
                    visibilityIcon.classList.add("icon-eye-hide");
                }
            }
        </script>
    </body>
    </html>
@endif
