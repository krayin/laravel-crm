<x-admin::layouts>
    <!-- Page Title -->
    <x-slot:title>
        @lang('theme-manager::app.settings.title')
    </x-slot>

    <x-admin::form
        method="POST"
        enctype="multipart/form-data"
        :action="route('admin.settings.theme.update')"
    >
        <div class="flex flex-col gap-4">
            <!-- Page Header -->
            <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                <div class="flex flex-col gap-2">
                    <!-- Breadcrumbs -->
                    <x-admin::breadcrumbs name="settings" />

                    <div class="text-xl font-bold dark:text-white">
                        @lang('theme-manager::app.settings.title')
                    </div>
                </div>

                <div class="flex items-center gap-x-2.5">
                    <!-- Save Button -->
                    <button
                        type="submit"
                        class="primary-button"
                    >
                        @lang('theme-manager::app.settings.save-btn')
                    </button>
                </div>
            </div>

            <!-- Main Content -->
            <div class="flex gap-2.5 max-xl:flex-wrap">
                <!-- Left Column - Main Settings -->
                <div class="flex flex-1 flex-col gap-2.5 max-xl:flex-auto">

                    <!-- SEÇÃO 1 - ATIVAÇÃO DO TEMA -->
                    <div class="box-shadow rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                        <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">
                            @lang('theme-manager::app.settings.activation.title')
                        </p>

                        <p class="mb-4 text-sm text-gray-600 dark:text-gray-300">
                            @lang('theme-manager::app.settings.activation.description')
                        </p>

                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label class="required">
                                @lang('theme-manager::app.settings.activation.is-active')
                            </x-admin::form.control-group.label>

                            <select
                                name="is_active"
                                class="block w-full rounded-md border border-gray-200 px-3 py-2.5 text-sm text-gray-600 transition-all hover:border-gray-400 focus:border-blue-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-blue-400"
                            >
                                <option value="0" {{ old('is_active', $config->is_active) == 0 ? 'selected' : '' }}>
                                    @lang('theme-manager::app.settings.activation.no')
                                </option>
                                <option value="1" {{ old('is_active', $config->is_active) == 1 ? 'selected' : '' }}>
                                    @lang('theme-manager::app.settings.activation.yes')
                                </option>
                            </select>

                            <x-admin::form.control-group.error control-name="is_active" />
                        </x-admin::form.control-group>

                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                            @lang('theme-manager::app.settings.activation.info')
                        </p>
                    </div>

                    <!-- SEÇÃO 2 - CORES DO TEMA -->
                    <div class="box-shadow rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                        <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">
                            @lang('theme-manager::app.settings.colors.title')
                        </p>

                        <p class="mb-4 text-sm text-gray-600 dark:text-gray-300">
                            @lang('theme-manager::app.settings.colors.description')
                        </p>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <!-- Cor Primária -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>
                                    @lang('theme-manager::app.settings.colors.primary')
                                </x-admin::form.control-group.label>

                                <div class="flex items-center gap-2">
                                    <x-admin::form.control-group.control
                                        type="color"
                                        name="color_primary"
                                        :value="old('color_primary', $config->color_primary)"
                                        class="h-10 w-20"
                                    />
                                    <x-admin::form.control-group.control
                                        type="text"
                                        name="color_primary"
                                        :value="old('color_primary', $config->color_primary)"
                                        class="flex-1"
                                    />
                                </div>

                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    @lang('theme-manager::app.settings.colors.primary-help')
                                </p>

                                <x-admin::form.control-group.error control-name="color_primary" />
                            </x-admin::form.control-group>

                            <!-- Cor Primária Escura -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>
                                    @lang('theme-manager::app.settings.colors.primary-dark')
                                </x-admin::form.control-group.label>

                                <div class="flex items-center gap-2">
                                    <x-admin::form.control-group.control
                                        type="color"
                                        name="color_primary_dark"
                                        :value="old('color_primary_dark', $config->color_primary_dark)"
                                        class="h-10 w-20"
                                    />
                                    <x-admin::form.control-group.control
                                        type="text"
                                        name="color_primary_dark"
                                        :value="old('color_primary_dark', $config->color_primary_dark)"
                                        class="flex-1"
                                    />
                                </div>

                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    @lang('theme-manager::app.settings.colors.primary-dark-help')
                                </p>

                                <x-admin::form.control-group.error control-name="color_primary_dark" />
                            </x-admin::form.control-group>

                            <!-- Cor Primária Clara -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>
                                    @lang('theme-manager::app.settings.colors.primary-light')
                                </x-admin::form.control-group.label>

                                <div class="flex items-center gap-2">
                                    <x-admin::form.control-group.control
                                        type="color"
                                        name="color_primary_light"
                                        :value="old('color_primary_light', $config->color_primary_light)"
                                        class="h-10 w-20"
                                    />
                                    <x-admin::form.control-group.control
                                        type="text"
                                        name="color_primary_light"
                                        :value="old('color_primary_light', $config->color_primary_light)"
                                        class="flex-1"
                                    />
                                </div>

                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    @lang('theme-manager::app.settings.colors.primary-light-help')
                                </p>

                                <x-admin::form.control-group.error control-name="color_primary_light" />
                            </x-admin::form.control-group>

                            <!-- Cor de Sucesso -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>
                                    @lang('theme-manager::app.settings.colors.success')
                                </x-admin::form.control-group.label>

                                <div class="flex items-center gap-2">
                                    <x-admin::form.control-group.control
                                        type="color"
                                        name="color_success"
                                        :value="old('color_success', $config->color_success)"
                                        class="h-10 w-20"
                                    />
                                    <x-admin::form.control-group.control
                                        type="text"
                                        name="color_success"
                                        :value="old('color_success', $config->color_success)"
                                        class="flex-1"
                                    />
                                </div>

                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    @lang('theme-manager::app.settings.colors.success-help')
                                </p>

                                <x-admin::form.control-group.error control-name="color_success" />
                            </x-admin::form.control-group>

                            <!-- Cor de Alerta -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>
                                    @lang('theme-manager::app.settings.colors.warning')
                                </x-admin::form.control-group.label>

                                <div class="flex items-center gap-2">
                                    <x-admin::form.control-group.control
                                        type="color"
                                        name="color_warning"
                                        :value="old('color_warning', $config->color_warning)"
                                        class="h-10 w-20"
                                    />
                                    <x-admin::form.control-group.control
                                        type="text"
                                        name="color_warning"
                                        :value="old('color_warning', $config->color_warning)"
                                        class="flex-1"
                                    />
                                </div>

                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    @lang('theme-manager::app.settings.colors.warning-help')
                                </p>

                                <x-admin::form.control-group.error control-name="color_warning" />
                            </x-admin::form.control-group>

                            <!-- Cor de Perigo -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>
                                    @lang('theme-manager::app.settings.colors.danger')
                                </x-admin::form.control-group.label>

                                <div class="flex items-center gap-2">
                                    <x-admin::form.control-group.control
                                        type="color"
                                        name="color_danger"
                                        :value="old('color_danger', $config->color_danger)"
                                        class="h-10 w-20"
                                    />
                                    <x-admin::form.control-group.control
                                        type="text"
                                        name="color_danger"
                                        :value="old('color_danger', $config->color_danger)"
                                        class="flex-1"
                                    />
                                </div>

                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    @lang('theme-manager::app.settings.colors.danger-help')
                                </p>

                                <x-admin::form.control-group.error control-name="color_danger" />
                            </x-admin::form.control-group>
                        </div>
                    </div>

                    <!-- SEÇÃO 3 - LOGOS E FAVICON -->
                    <div class="box-shadow rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                        <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">
                            @lang('theme-manager::app.settings.logos.title')
                        </p>

                        <p class="mb-4 text-sm text-gray-600 dark:text-gray-300">
                            @lang('theme-manager::app.settings.logos.description')
                        </p>

                        <div class="grid grid-cols-1 gap-6">
                            <!-- Logo Principal -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>
                                    @lang('theme-manager::app.settings.logos.main')
                                </x-admin::form.control-group.label>

                                @if($config->logo_main)
                                    <div class="mb-2">
                                        <img src="{{ asset('storage/theme-manager/' . $config->logo_main) }}"
                                             alt="Logo Principal"
                                             class="h-16 rounded border border-gray-200 bg-gray-50 p-2 dark:border-gray-700 dark:bg-gray-800">

                                        <label class="mt-2 flex items-center gap-2 text-sm">
                                            <input type="checkbox" name="logo_main_delete" value="1" class="rounded">
                                            <span class="text-gray-600 dark:text-gray-400">
                                                @lang('theme-manager::app.settings.logos.delete-current')
                                            </span>
                                        </label>
                                    </div>
                                @endif

                                <x-admin::form.control-group.control
                                    type="file"
                                    name="logo_main"
                                    accept="image/*"
                                />

                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    @lang('theme-manager::app.settings.logos.main-help')
                                </p>

                                <x-admin::form.control-group.error control-name="logo_main" />
                            </x-admin::form.control-group>

                            <!-- Logo Claro -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>
                                    @lang('theme-manager::app.settings.logos.light')
                                </x-admin::form.control-group.label>

                                @if($config->logo_light)
                                    <div class="mb-2">
                                        <img src="{{ asset('storage/theme-manager/' . $config->logo_light) }}"
                                             alt="Logo Claro"
                                             class="h-16 rounded border border-gray-200 bg-gray-800 p-2">

                                        <label class="mt-2 flex items-center gap-2 text-sm">
                                            <input type="checkbox" name="logo_light_delete" value="1" class="rounded">
                                            <span class="text-gray-600 dark:text-gray-400">
                                                @lang('theme-manager::app.settings.logos.delete-current')
                                            </span>
                                        </label>
                                    </div>
                                @endif

                                <x-admin::form.control-group.control
                                    type="file"
                                    name="logo_light"
                                    accept="image/*"
                                />

                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    @lang('theme-manager::app.settings.logos.light-help')
                                </p>

                                <x-admin::form.control-group.error control-name="logo_light" />
                            </x-admin::form.control-group>

                            <!-- Ícone do Logo -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>
                                    @lang('theme-manager::app.settings.logos.icon')
                                </x-admin::form.control-group.label>

                                @if($config->logo_icon)
                                    <div class="mb-2">
                                        <img src="{{ asset('storage/theme-manager/' . $config->logo_icon) }}"
                                             alt="Ícone do Logo"
                                             class="h-16 w-16 rounded border border-gray-200 bg-gray-50 p-2 dark:border-gray-700 dark:bg-gray-800">

                                        <label class="mt-2 flex items-center gap-2 text-sm">
                                            <input type="checkbox" name="logo_icon_delete" value="1" class="rounded">
                                            <span class="text-gray-600 dark:text-gray-400">
                                                @lang('theme-manager::app.settings.logos.delete-current')
                                            </span>
                                        </label>
                                    </div>
                                @endif

                                <x-admin::form.control-group.control
                                    type="file"
                                    name="logo_icon"
                                    accept="image/*"
                                />

                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    @lang('theme-manager::app.settings.logos.icon-help')
                                </p>

                                <x-admin::form.control-group.error control-name="logo_icon" />
                            </x-admin::form.control-group>

                            <!-- Favicon -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>
                                    @lang('theme-manager::app.settings.logos.favicon')
                                </x-admin::form.control-group.label>

                                @if($config->favicon)
                                    <div class="mb-2">
                                        <img src="{{ asset('storage/theme-manager/' . $config->favicon) }}"
                                             alt="Favicon"
                                             class="h-8 w-8 rounded border border-gray-200 bg-gray-50 p-1 dark:border-gray-700 dark:bg-gray-800">

                                        <label class="mt-2 flex items-center gap-2 text-sm">
                                            <input type="checkbox" name="favicon_delete" value="1" class="rounded">
                                            <span class="text-gray-600 dark:text-gray-400">
                                                @lang('theme-manager::app.settings.logos.delete-current')
                                            </span>
                                        </label>
                                    </div>
                                @endif

                                <x-admin::form.control-group.control
                                    type="file"
                                    name="favicon"
                                    accept="image/*,.ico"
                                />

                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    @lang('theme-manager::app.settings.logos.favicon-help')
                                </p>

                                <x-admin::form.control-group.error control-name="favicon" />
                            </x-admin::form.control-group>
                        </div>
                    </div>

                    {{--
                    ============================================================================
                    SEÇÕES DE LOGIN PAGE TEMPORARIAMENTE DESABILITADAS
                    Backend mantido intacto para facilitar reativação futura.
                    Para reativar: remova os comentários {{-- e --}} das seções 4 e 5 abaixo.
                    ============================================================================
                    --}}

                    {{-- SEÇÃO 4 - PÁGINA DE LOGIN (BACKGROUND) - DESABILITADA
                    <div class="box-shadow rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                        <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">
                            @lang('theme-manager::app.settings.login.title')
                        </p>

                        <p class="mb-4 text-sm text-gray-600 dark:text-gray-300">
                            @lang('theme-manager::app.settings.login.description')
                        </p>

                        <div class="grid grid-cols-1 gap-6">
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>
                                    @lang('theme-manager::app.settings.login.bg-image')
                                </x-admin::form.control-group.label>

                                @if($config->login_bg_image)
                                    <div class="mb-2">
                                        <img src="{{ asset('storage/theme-manager/' . $config->login_bg_image) }}"
                                             alt="Login Background"
                                             class="h-32 w-full rounded border border-gray-200 object-cover dark:border-gray-700">

                                        <label class="mt-2 flex items-center gap-2 text-sm">
                                            <input type="checkbox" name="login_bg_image_delete" value="1" class="rounded">
                                            <span class="text-gray-600 dark:text-gray-400">
                                                @lang('theme-manager::app.settings.logos.delete-current')
                                            </span>
                                        </label>
                                    </div>
                                @endif

                                <x-admin::form.control-group.control
                                    type="file"
                                    name="login_bg_image"
                                    accept="image/*"
                                />

                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    @lang('theme-manager::app.settings.login.bg-image-help')
                                </p>

                                <x-admin::form.control-group.error control-name="login_bg_image" />
                            </x-admin::form.control-group>

                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>
                                    @lang('theme-manager::app.settings.login.bg-zoom')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="select"
                                    name="login_bg_zoom"
                                    :value="old('login_bg_zoom', $config->login_bg_zoom)"
                                >
                                    <option value="50">50%</option>
                                    <option value="75">75%</option>
                                    <option value="100">100%</option>
                                    <option value="125">125%</option>
                                    <option value="150">150%</option>
                                    <option value="200">200%</option>
                                </x-admin::form.control-group.control>

                                <x-admin::form.control-group.error control-name="login_bg_zoom" />
                            </x-admin::form.control-group>

                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>
                                    @lang('theme-manager::app.settings.login.bg-opacity')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="select"
                                    name="login_bg_opacity"
                                    :value="old('login_bg_opacity', $config->login_bg_opacity)"
                                >
                                    @for($i = 0; $i <= 100; $i += 10)
                                        <option value="{{ $i }}">{{ $i }}%</option>
                                    @endfor
                                </x-admin::form.control-group.control>

                                <x-admin::form.control-group.error control-name="login_bg_opacity" />
                            </x-admin::form.control-group>

                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>
                                    @lang('theme-manager::app.settings.login.show-powered-by')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="select"
                                    name="login_show_powered_by"
                                    :value="old('login_show_powered_by', $config->login_show_powered_by)"
                                >
                                    <option value="0">@lang('theme-manager::app.settings.activation.no')</option>
                                    <option value="1">@lang('theme-manager::app.settings.activation.yes')</option>
                                </x-admin::form.control-group.control>

                                <x-admin::form.control-group.error control-name="login_show_powered_by" />
                            </x-admin::form.control-group>
                        </div>
                    </div>
                    FIM SEÇÃO 4 --}}

                    {{-- SEÇÃO 5 - CAIXA DE LOGIN CUSTOMIZADA - DESABILITADA
                    <div class="box-shadow rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                        <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">
                            @lang('theme-manager::app.settings.login-card.section-title')
                        </p>

                        <p class="mb-4 text-sm text-gray-600 dark:text-gray-300">
                            @lang('theme-manager::app.settings.login-card.description')
                        </p>

                        <x-admin::form.control-group class="mb-6">
                            <x-admin::form.control-group.label>
                                @lang('theme-manager::app.settings.login-card.enabled')
                            </x-admin::form.control-group.label>

                            <select
                                name="login_card_enabled"
                                id="login_card_enabled"
                                class="block w-full rounded-md border border-gray-200 px-3 py-2.5 text-sm text-gray-600 transition-all hover:border-gray-400 focus:border-blue-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-blue-400"
                            >
                                <option value="0" {{ old('login_card_enabled', $config->login_card_enabled) == 0 ? 'selected' : '' }}>
                                    @lang('theme-manager::app.settings.activation.no')
                                </option>
                                <option value="1" {{ old('login_card_enabled', $config->login_card_enabled) == 1 ? 'selected' : '' }}>
                                    @lang('theme-manager::app.settings.activation.yes')
                                </option>
                            </select>

                            <x-admin::form.control-group.error control-name="login_card_enabled" />
                        </x-admin::form.control-group>

                        <div id="login-card-options" class="grid grid-cols-1 gap-6">
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>
                                    @lang('theme-manager::app.settings.login-card.bg-image')
                                </x-admin::form.control-group.label>

                                @if($config->login_card_bg_image)
                                    <div class="mb-2">
                                        <img src="{{ asset('storage/theme-manager/' . $config->login_card_bg_image) }}"
                                             alt="Login Card Background"
                                             class="h-32 w-full rounded border border-gray-200 object-cover dark:border-gray-700">

                                        <label class="mt-2 flex items-center gap-2 text-sm">
                                            <input type="checkbox" name="login_card_bg_image_delete" value="1" class="rounded">
                                            <span class="text-gray-600 dark:text-gray-400">
                                                @lang('theme-manager::app.settings.logos.delete-current')
                                            </span>
                                        </label>
                                    </div>
                                @endif

                                <x-admin::form.control-group.control
                                    type="file"
                                    name="login_card_bg_image"
                                    accept="image/*"
                                />

                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    @lang('theme-manager::app.settings.login-card.bg-image-help')
                                </p>

                                <x-admin::form.control-group.error control-name="login_card_bg_image" />
                            </x-admin::form.control-group>

                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>
                                    @lang('theme-manager::app.settings.login-card.bg-opacity')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="select"
                                    name="login_card_bg_opacity"
                                    :value="old('login_card_bg_opacity', $config->login_card_bg_opacity)"
                                >
                                    @for($i = 0; $i <= 100; $i += 10)
                                        <option value="{{ $i }}">{{ $i }}%</option>
                                    @endfor
                                </x-admin::form.control-group.control>

                                <x-admin::form.control-group.error control-name="login_card_bg_opacity" />
                            </x-admin::form.control-group>

                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>
                                    @lang('theme-manager::app.settings.login-card.overlay-color')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="text"
                                    name="login_card_overlay_color"
                                    :value="old('login_card_overlay_color', $config->login_card_overlay_color)"
                                    placeholder="rgba(10, 45, 15, 0.78)"
                                />

                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    @lang('theme-manager::app.settings.login-card.overlay-color-help')
                                </p>

                                <x-admin::form.control-group.error control-name="login_card_overlay_color" />
                            </x-admin::form.control-group>

                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>
                                    @lang('theme-manager::app.settings.login-card.welcome-title')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="text"
                                    name="login_card_title"
                                    :value="old('login_card_title', $config->login_card_title)"
                                />

                                <x-admin::form.control-group.error control-name="login_card_title" />
                            </x-admin::form.control-group>

                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>
                                    @lang('theme-manager::app.settings.login-card.subtitle')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="text"
                                    name="login_card_subtitle"
                                    :value="old('login_card_subtitle', $config->login_card_subtitle)"
                                />

                                <x-admin::form.control-group.error control-name="login_card_subtitle" />
                            </x-admin::form.control-group>

                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>
                                    @lang('theme-manager::app.settings.login-card.sparkles')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="select"
                                    name="login_card_sparkles"
                                    :value="old('login_card_sparkles', $config->login_card_sparkles)"
                                >
                                    <option value="0">@lang('theme-manager::app.settings.activation.no')</option>
                                    <option value="1">@lang('theme-manager::app.settings.activation.yes')</option>
                                </x-admin::form.control-group.control>

                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    @lang('theme-manager::app.settings.login-card.sparkles-help')
                                </p>

                                <x-admin::form.control-group.error control-name="login_card_sparkles" />
                            </x-admin::form.control-group>

                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>
                                    @lang('theme-manager::app.settings.login-card.help-link')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="select"
                                    name="login_card_help_link"
                                    :value="old('login_card_help_link', $config->login_card_help_link)"
                                >
                                    <option value="0">@lang('theme-manager::app.settings.activation.no')</option>
                                    <option value="1">@lang('theme-manager::app.settings.activation.yes')</option>
                                </x-admin::form.control-group.control>

                                <x-admin::form.control-group.error control-name="login_card_help_link" />
                            </x-admin::form.control-group>

                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>
                                    @lang('theme-manager::app.settings.login-card.support-email')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="email"
                                    name="login_card_support_email"
                                    :value="old('login_card_support_email', $config->login_card_support_email)"
                                />

                                <x-admin::form.control-group.error control-name="login_card_support_email" />
                            </x-admin::form.control-group>

                            <x-admin::form.control-group class="mb-6">
                                <x-admin::form.control-group.label>
                                    @lang('theme-manager::app.settings.login-card.custom-code')
                                </x-admin::form.control-group.label>

                                <textarea
                                    name="login_card_custom_code"
                                    rows="10"
                                    class="block w-full rounded-md border border-gray-200 px-3 py-2.5 text-sm text-gray-600 transition-all hover:border-gray-400 focus:border-blue-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-blue-400 font-mono"
                                    placeholder="<!-- Cole seu código HTML/CSS/JavaScript aqui -->"
                                >{{ old('login_card_custom_code', $config->login_card_custom_code) }}</textarea>

                                <p class="mt-2 text-xs text-gray-600 dark:text-gray-300">
                                    @lang('theme-manager::app.settings.login-card.custom-code-hint')
                                </p>

                                <x-admin::form.control-group.error control-name="login_card_custom_code" />
                            </x-admin::form.control-group>
                        </div>
                    </div>
                    FIM SEÇÃO 5 --}}

                    <!-- SEÇÃO 6 - EMPTY STATES -->
                    <div class="box-shadow rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                        <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">
                            @lang('theme-manager::app.settings.empty-states.title')
                        </p>

                        <p class="mb-4 text-sm text-gray-600 dark:text-gray-300">
                            @lang('theme-manager::app.settings.empty-states.description')
                        </p>

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                            @foreach(['activities', 'calls', 'emails', 'meetings', 'notes', 'organizations', 'persons', 'leads', 'products'] as $emptyState)
                                <x-admin::form.control-group>
                                    <x-admin::form.control-group.label>
                                        @lang('theme-manager::app.settings.empty-states.' . $emptyState)
                                    </x-admin::form.control-group.label>

                                    @if($config->{'empty_state_' . $emptyState})
                                        <div class="mb-2">
                                            <img src="{{ asset('storage/theme-manager/' . $config->{'empty_state_' . $emptyState}) }}"
                                                 alt="{{ ucfirst($emptyState) }} Empty State"
                                                 class="h-24 w-24 rounded border border-gray-200 bg-gray-50 p-2 dark:border-gray-700 dark:bg-gray-800">

                                            <label class="mt-2 flex items-center gap-2 text-sm">
                                                <input type="checkbox" name="empty_state_{{ $emptyState }}_delete" value="1" class="rounded">
                                                <span class="text-gray-600 dark:text-gray-400">
                                                    @lang('theme-manager::app.settings.logos.delete-current')
                                                </span>
                                            </label>
                                        </div>
                                    @endif

                                    <x-admin::form.control-group.control
                                        type="file"
                                        name="empty_state_{{ $emptyState }}"
                                        accept=".svg,image/svg+xml"
                                    />

                                    <x-admin::form.control-group.error control-name="empty_state_{{ $emptyState }}" />
                                </x-admin::form.control-group>
                            @endforeach
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </x-admin::form>

    {{-- JavaScript desabilitado junto com as seções de Login Page
    @pushOnce('scripts')
        <script>
            // Toggle login card options based on enabled status
            function toggleLoginCardOptions() {
                const enabled = document.getElementById('login_card_enabled').value;
                const options = document.getElementById('login-card-options');

                if (enabled === '1' || enabled === 1 || enabled === true) {
                    options.style.display = 'grid';
                } else {
                    options.style.display = 'none';
                }
            }

            // Execute on page load
            document.addEventListener('DOMContentLoaded', function() {
                toggleLoginCardOptions();

                // Listen for changes
                document.getElementById('login_card_enabled').addEventListener('change', toggleLoginCardOptions);
            });
        </script>
    @endPushOnce
    --}}
</x-admin::layouts>
