@php
    $isThemeActive = app('theme')->isActive();
    $loginConfig = $isThemeActive ? app('theme')->getLoginConfig() : null;
@endphp

@if(!$isThemeActive)
    {{-- Tema inativo: usar view original do Admin --}}
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
    {{-- Tema ativo: usar login customizado --}}
    <!DOCTYPE html>
    <html lang="{{ app()->getLocale() }}" dir="{{ core()->getCurrentLocale()->direction }}">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@lang('admin::app.users.login.title')</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            :root {
                --login-bg-zoom: {{ $loginConfig['bg_zoom'] / 100 }};
                --login-bg-opacity: {{ $loginConfig['bg_opacity'] / 100 }};
                @if($loginConfig['card_enabled'])
                    --card-bg-opacity: {{ $loginConfig['card_bg_opacity'] / 100 }};
                @endif
            }

            body, html {
                margin: 0;
                padding: 0;
                height: 100%;
                overflow: hidden;
            }

            .login-wrapper {
                position: relative;
                width: 100vw;
                height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                overflow: hidden;
            }

            /* Background com imagem e zoom */
            .login-background {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                @if($loginConfig['bg_image'])
                    background-image: url('{{ $loginConfig['bg_image'] }}');
                    background-size: cover;
                    background-position: center;
                    transform: scale(var(--login-bg-zoom, 1));
                @else
                    background: #f3f4f6; /* Cor neutra - sem imagem de fundo */
                @endif
            }

            /* Overlay escuro sobre o background */
            .login-overlay {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, var(--login-bg-opacity, 0.5));
            }

            /* Container do card */
            .login-card-container {
                position: relative;
                z-index: 10;
                width: 90%;
                max-width: 450px;
            }

            /* Logo */
            .login-logo {
                text-align: center;
                margin-bottom: 2rem;
            }

            .login-logo img {
                max-height: 60px;
                max-width: 200px;
                filter: drop-shadow(0 4px 6px rgba(0, 0, 0, 0.3));
            }

            /* Card padrão */
            .login-card {
                background: white;
                border-radius: 16px;
                box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
                overflow: hidden;
            }

            /* Card customizado com imagem de fundo */
            @if($loginConfig['card_enabled'])
                .login-card-custom {
                    position: relative;
                    @if($loginConfig['card_bg_image'])
                        background-image: url('{{ $loginConfig['card_bg_image'] }}');
                        background-size: cover;
                        background-position: center;
                    @else
                        background: white; /* Sem imagem quando NULL */
                    @endif
                }

                .login-card-custom::before {
                    content: '';
                    position: absolute;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    @if($loginConfig['card_bg_image'])
                        background-color: rgba(255, 255, 255, calc(1 - var(--card-bg-opacity, 0.62)));
                        @if($loginConfig['card_overlay_color'])
                            background: {{ $loginConfig['card_overlay_color'] }};
                        @endif
                    @else
                        display: none; /* Sem overlay quando não há imagem */
                    @endif
                }

                .login-card-content {
                    position: relative;
                    z-index: 1;
                }

                .login-card-header {
                    padding: 3rem 2rem 1.5rem;
                    text-align: center;
                    color: white;
                    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
                }

                .login-card-title {
                    font-size: 2rem;
                    font-weight: bold;
                    margin: 0 0 0.5rem;
                }

                .login-card-subtitle {
                    font-size: 1rem;
                    opacity: 0.9;
                    margin: 0;
                }
            @endif

            /* Formulário */
            .login-form {
                padding: 2rem;
                @if(!$loginConfig['card_enabled'])
                    background: white;
                @else
                    background: rgba(255, 255, 255, 0.95);
                    backdrop-filter: blur(10px);
                @endif
            }

            .form-group {
                margin-bottom: 1.5rem;
            }

            .form-label {
                display: block;
                font-size: 0.875rem;
                font-weight: 600;
                color: #374151;
                margin-bottom: 0.5rem;
            }

            .form-input {
                width: 100%;
                padding: 0.75rem 1rem;
                border: 1px solid #d1d5db;
                border-radius: 8px;
                font-size: 1rem;
                transition: all 0.2s;
            }

            .form-input:focus {
                outline: none;
                border-color: var(--primary-color, #667eea);
                box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            }

            .password-wrapper {
                position: relative;
            }

            .password-toggle {
                position: absolute;
                right: 1rem;
                top: 50%;
                transform: translateY(-50%);
                cursor: pointer;
                font-size: 1.25rem;
                color: #6b7280;
            }

            .form-actions {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-top: 2rem;
            }

            .forgot-link {
                font-size: 0.875rem;
                color: var(--primary-color, #667eea);
                text-decoration: none;
                font-weight: 600;
            }

            .forgot-link:hover {
                text-decoration: underline;
            }

            .submit-btn {
                padding: 0.75rem 2rem;
                background: var(--primary-color, #667eea);
                color: white;
                border: none;
                border-radius: 8px;
                font-size: 1rem;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.2s;
            }

            .submit-btn:hover {
                background: var(--primary-dark-color, #5568d3);
                transform: translateY(-1px);
                box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
            }

            /* Help link */
            @if($loginConfig['card_enabled'] && $loginConfig['card_help_link'])
                .help-link {
                    text-align: center;
                    padding: 1rem 2rem;
                    background: rgba(255, 255, 255, 0.9);
                    border-top: 1px solid rgba(0, 0, 0, 0.1);
                }

                .help-link a {
                    color: var(--primary-color, #667eea);
                    text-decoration: none;
                    font-size: 0.875rem;
                    font-weight: 600;
                }

                .help-link a:hover {
                    text-decoration: underline;
                }
            @endif

            /* Powered by */
            .powered-by {
                position: absolute;
                bottom: 2rem;
                left: 50%;
                transform: translateX(-50%);
                z-index: 10;
                color: white;
                font-size: 0.875rem;
                text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
            }

            .powered-by a {
                color: white;
                text-decoration: none;
                font-weight: 600;
            }

            .powered-by a:hover {
                text-decoration: underline;
            }

            /* Sparkles animation */
            @if($loginConfig['card_enabled'] && $loginConfig['card_sparkles'])
                @keyframes sparkle {
                    0%, 100% { opacity: 0; transform: scale(0) rotate(0deg); }
                    50% { opacity: 1; transform: scale(1) rotate(180deg); }
                }

                .sparkles-container {
                    position: absolute;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    overflow: hidden;
                    pointer-events: none;
                }

                .sparkle {
                    position: absolute;
                    width: 6px;
                    height: 6px;
                    background: radial-gradient(circle, #ffd700 0%, #ffed4e 50%, transparent 70%);
                    border-radius: 50%;
                    animation: sparkle 3s infinite ease-in-out;
                    box-shadow: 0 0 10px #ffd700;
                }

                .sparkle:nth-child(1) { top: 15%; left: 20%; animation-delay: 0s; animation-duration: 2.5s; }
                .sparkle:nth-child(2) { top: 25%; left: 75%; animation-delay: 0.5s; animation-duration: 3s; }
                .sparkle:nth-child(3) { top: 60%; left: 15%; animation-delay: 1s; animation-duration: 2.8s; }
                .sparkle:nth-child(4) { top: 45%; left: 85%; animation-delay: 1.5s; animation-duration: 3.2s; }
                .sparkle:nth-child(5) { top: 75%; left: 50%; animation-delay: 2s; animation-duration: 2.7s; }
                .sparkle:nth-child(6) { top: 35%; left: 45%; animation-delay: 0.3s; animation-duration: 3.5s; }
                .sparkle:nth-child(7) { top: 80%; left: 30%; animation-delay: 1.8s; animation-duration: 2.9s; }
                .sparkle:nth-child(8) { top: 10%; left: 60%; animation-delay: 0.8s; animation-duration: 3.1s; }
            @endif

            /* Error messages */
            .error-message {
                color: #ef4444;
                font-size: 0.875rem;
                margin-top: 0.25rem;
            }

            .alert {
                padding: 1rem;
                margin-bottom: 1rem;
                border-radius: 8px;
            }

            .alert-error {
                background-color: #fee2e2;
                color: #991b1b;
                border: 1px solid #fecaca;
            }
        </style>
    </head>
    <body>
        <div class="login-wrapper">
            <!-- Background -->
            <div class="login-background"></div>
            <div class="login-overlay"></div>

            <!-- Login Card -->
            <div class="login-card-container">
                <!-- Logo -->
                <div class="login-logo">
                    @if ($logo = app('theme')->getLogo('light'))
                        <img src="{{ $logo }}" alt="{{ config('app.name') }}">
                    @elseif ($logo = app('theme')->getLogo('main'))
                        <img src="{{ $logo }}" alt="{{ config('app.name') }}">
                    @elseif ($logo = core()->getConfigData('general.design.admin_logo.logo_image'))
                        <img src="{{ Storage::url($logo) }}" alt="{{ config('app.name') }}">
                    @else
                        <img src="{{ vite()->asset('images/logo.svg') }}" alt="{{ config('app.name') }}">
                    @endif
                </div>

                <!-- Card -->
                <div class="login-card @if($loginConfig['card_enabled']) login-card-custom @endif">
                    @if($loginConfig['card_enabled'] && $loginConfig['card_sparkles'])
                        <div class="sparkles-container">
                            <div class="sparkle"></div>
                            <div class="sparkle"></div>
                            <div class="sparkle"></div>
                            <div class="sparkle"></div>
                            <div class="sparkle"></div>
                            <div class="sparkle"></div>
                            <div class="sparkle"></div>
                            <div class="sparkle"></div>
                        </div>
                    @endif

                    <div class="login-card-content">
                        @if($loginConfig['card_enabled'])
                            <div class="login-card-header">
                                <h1 class="login-card-title">{{ $loginConfig['card_title'] }}</h1>
                                <p class="login-card-subtitle">{{ $loginConfig['card_subtitle'] }}</p>
                            </div>
                        @endif

                        {!! view_render_event('admin.sessions.login.form_controls.before') !!}

                        <form class="login-form" method="POST" action="{{ route('admin.session.store') }}">
                            @csrf

                            @if(!$loginConfig['card_enabled'])
                                <h2 style="margin: 0 0 1.5rem; font-size: 1.5rem; font-weight: bold; color: #1f2937;">
                                    @lang('admin::app.users.login.title')
                                </h2>
                            @endif

                            @if ($errors->any())
                                <div class="alert alert-error">
                                    @foreach ($errors->all() as $error)
                                        <p style="margin: 0;">{{ $error }}</p>
                                    @endforeach
                                </div>
                            @endif

                            <!-- Email -->
                            <div class="form-group">
                                <label for="email" class="form-label">
                                    @lang('admin::app.users.login.email') <span style="color: #ef4444;">*</span>
                                </label>
                                <input
                                    type="email"
                                    id="email"
                                    name="email"
                                    class="form-input"
                                    placeholder="@lang('admin::app.users.login.email')"
                                    value="{{ old('email') }}"
                                    required
                                    autofocus
                                >
                                @error('email')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Password -->
                            <div class="form-group">
                                <label for="password" class="form-label">
                                    @lang('admin::app.users.login.password') <span style="color: #ef4444;">*</span>
                                </label>
                                <div class="password-wrapper">
                                    <input
                                        type="password"
                                        id="password"
                                        name="password"
                                        class="form-input"
                                        placeholder="@lang('admin::app.users.login.password')"
                                        required
                                        style="padding-right: 3rem;"
                                    >
                                    <span
                                        class="password-toggle icon-eye-hide"
                                        id="visibilityIcon"
                                        onclick="switchVisibility()"
                                    ></span>
                                </div>
                                @error('password')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Actions -->
                            <div class="form-actions">
                                <a href="{{ route('admin.forgot_password.create') }}" class="forgot-link">
                                    @lang('admin::app.users.login.forget-password-link')
                                </a>

                                <button type="submit" class="submit-btn">
                                    @lang('admin::app.users.login.submit-btn')
                                </button>
                            </div>
                        </form>

                        {!! view_render_event('admin.sessions.login.form_controls.after') !!}

                        @if($loginConfig['card_enabled'] && $loginConfig['card_help_link'])
                            <div class="help-link">
                                <a href="mailto:{{ $loginConfig['card_support_email'] }}">
                                    Precisa de ajuda? Contate o suporte
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Powered By -->
            @if($loginConfig['show_powered_by'])
                <div class="powered-by">
                    @lang('admin::app.components.layouts.powered-by.description', [
                        'krayin' => '<a href="https://krayincrm.com/">Krayin</a>',
                        'webkul' => '<a href="https://webkul.com/">Webkul</a>',
                    ])
                </div>
            @endif
        </div>

        <script>
            function switchVisibility() {
                let passwordField = document.getElementById("password");
                let visibilityIcon = document.getElementById("visibilityIcon");

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
