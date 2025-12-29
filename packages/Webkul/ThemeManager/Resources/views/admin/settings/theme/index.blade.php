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

                    <!-- SEÇÃO 4 - PÁGINA DE LOGIN (BACKGROUND) -->
                    <div class="box-shadow rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                        <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">
                            @lang('theme-manager::app.settings.login.title')
                        </p>

                        <p class="mb-4 text-sm text-gray-600 dark:text-gray-300">
                            @lang('theme-manager::app.settings.login.description')
                        </p>

                        <div class="grid grid-cols-1 gap-6">
                            <!-- Background Image -->
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

                            <!-- Background Zoom (Slider) -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>
                                    @lang('theme-manager::app.settings.login.bg-zoom')
                                    <span id="login_bg_zoom_value" class="ml-2 text-blue-600 dark:text-blue-400">{{ old('login_bg_zoom', $config->login_bg_zoom ?? 100) }}%</span>
                                </x-admin::form.control-group.label>

                                <input
                                    type="range"
                                    name="login_bg_zoom"
                                    id="login_bg_zoom"
                                    min="50"
                                    max="150"
                                    step="5"
                                    value="{{ old('login_bg_zoom', $config->login_bg_zoom ?? 100) }}"
                                    class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer dark:bg-gray-700"
                                    oninput="document.getElementById('login_bg_zoom_value').textContent = this.value + '%'"
                                />

                                <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    <span>50%</span>
                                    <span>100%</span>
                                    <span>150%</span>
                                </div>

                                <x-admin::form.control-group.error control-name="login_bg_zoom" />
                            </x-admin::form.control-group>

                            <!-- Background Opacity (Slider) -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>
                                    @lang('theme-manager::app.settings.login.bg-opacity')
                                    <span id="login_bg_opacity_value" class="ml-2 text-blue-600 dark:text-blue-400">{{ old('login_bg_opacity', $config->login_bg_opacity ?? 50) }}%</span>
                                </x-admin::form.control-group.label>

                                <input
                                    type="range"
                                    name="login_bg_opacity"
                                    id="login_bg_opacity"
                                    min="0"
                                    max="100"
                                    step="5"
                                    value="{{ old('login_bg_opacity', $config->login_bg_opacity ?? 50) }}"
                                    class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer dark:bg-gray-700"
                                    oninput="document.getElementById('login_bg_opacity_value').textContent = this.value + '%'"
                                />

                                <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    <span>0% (oculto)</span>
                                    <span>50%</span>
                                    <span>100% (visível)</span>
                                </div>

                                <x-admin::form.control-group.error control-name="login_bg_opacity" />
                            </x-admin::form.control-group>

                            <!-- Show Powered By -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>
                                    @lang('theme-manager::app.settings.login.show-powered-by')
                                </x-admin::form.control-group.label>

                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input
                                        type="hidden"
                                        name="login_show_powered_by"
                                        value="0"
                                    />
                                    <input
                                        type="checkbox"
                                        name="login_show_powered_by"
                                        value="1"
                                        class="sr-only peer"
                                        {{ old('login_show_powered_by', $config->login_show_powered_by ?? true) ? 'checked' : '' }}
                                    />
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                    <span class="ml-3 text-sm font-medium text-gray-600 dark:text-gray-300">
                                        Exibir "Powered by Krayin"
                                    </span>
                                </label>

                                <x-admin::form.control-group.error control-name="login_show_powered_by" />
                            </x-admin::form.control-group>
                        </div>
                    </div>

                    <!-- SEÇÃO 5 - CAIXA DE LOGIN CUSTOMIZADA -->
                    <div class="box-shadow rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                        <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">
                            @lang('theme-manager::app.settings.login-card.section-title')
                        </p>

                        <p class="mb-4 text-sm text-gray-600 dark:text-gray-300">
                            @lang('theme-manager::app.settings.login-card.description')
                        </p>

                        <!-- Toggle Card Enabled -->
                        <x-admin::form.control-group class="mb-6">
                            <x-admin::form.control-group.label>
                                @lang('theme-manager::app.settings.login-card.enabled')
                            </x-admin::form.control-group.label>

                            <label class="relative inline-flex items-center cursor-pointer">
                                <input
                                    type="hidden"
                                    name="login_card_enabled"
                                    value="0"
                                />
                                <input
                                    type="checkbox"
                                    name="login_card_enabled"
                                    id="login_card_enabled"
                                    value="1"
                                    class="sr-only peer"
                                    {{ old('login_card_enabled', $config->login_card_enabled ?? false) ? 'checked' : '' }}
                                    onchange="toggleLoginCardOptions()"
                                />
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                <span class="ml-3 text-sm font-medium text-gray-600 dark:text-gray-300">
                                    Ativar card customizado no login
                                </span>
                            </label>

                            <x-admin::form.control-group.error control-name="login_card_enabled" />
                        </x-admin::form.control-group>

                        <div id="login-card-options" class="grid grid-cols-1 gap-6" style="{{ old('login_card_enabled', $config->login_card_enabled ?? false) ? '' : 'display: none;' }}">
                            <!-- Card Background Image -->
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

                            <!-- Card Background Opacity (Slider) -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>
                                    @lang('theme-manager::app.settings.login-card.bg-opacity')
                                    <span id="login_card_bg_opacity_value" class="ml-2 text-blue-600 dark:text-blue-400">{{ old('login_card_bg_opacity', $config->login_card_bg_opacity ?? 62) }}%</span>
                                </x-admin::form.control-group.label>

                                <input
                                    type="range"
                                    name="login_card_bg_opacity"
                                    id="login_card_bg_opacity"
                                    min="0"
                                    max="100"
                                    step="5"
                                    value="{{ old('login_card_bg_opacity', $config->login_card_bg_opacity ?? 62) }}"
                                    class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer dark:bg-gray-700"
                                    oninput="document.getElementById('login_card_bg_opacity_value').textContent = this.value + '%'"
                                />

                                <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    <span>0%</span>
                                    <span>50%</span>
                                    <span>100%</span>
                                </div>

                                <x-admin::form.control-group.error control-name="login_card_bg_opacity" />
                            </x-admin::form.control-group>

                            <!-- Overlay Color -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>
                                    @lang('theme-manager::app.settings.login-card.overlay-color')
                                </x-admin::form.control-group.label>

                                <div class="flex items-center gap-2">
                                    <x-admin::form.control-group.control
                                        type="text"
                                        name="login_card_overlay_color"
                                        id="login_card_overlay_color"
                                        :value="old('login_card_overlay_color', $config->login_card_overlay_color ?? 'rgba(10, 45, 15, 0.78)')"
                                        placeholder="rgba(10, 45, 15, 0.78)"
                                        class="flex-1"
                                    />
                                    <div
                                        id="overlay_color_preview"
                                        class="w-10 h-10 rounded border border-gray-300 dark:border-gray-600"
                                        style="background-color: {{ old('login_card_overlay_color', $config->login_card_overlay_color ?? 'rgba(10, 45, 15, 0.78)') }};"
                                    ></div>
                                </div>

                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    @lang('theme-manager::app.settings.login-card.overlay-color-help')
                                </p>

                                <x-admin::form.control-group.error control-name="login_card_overlay_color" />
                            </x-admin::form.control-group>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Title -->
                                <x-admin::form.control-group>
                                    <x-admin::form.control-group.label>
                                        @lang('theme-manager::app.settings.login-card.welcome-title')
                                    </x-admin::form.control-group.label>

                                    <x-admin::form.control-group.control
                                        type="text"
                                        name="login_card_title"
                                        :value="old('login_card_title', $config->login_card_title ?? 'Bem-vindo')"
                                        placeholder="Bem-vindo"
                                    />

                                    <x-admin::form.control-group.error control-name="login_card_title" />
                                </x-admin::form.control-group>

                                <!-- Subtitle -->
                                <x-admin::form.control-group>
                                    <x-admin::form.control-group.label>
                                        @lang('theme-manager::app.settings.login-card.subtitle')
                                    </x-admin::form.control-group.label>

                                    <x-admin::form.control-group.control
                                        type="text"
                                        name="login_card_subtitle"
                                        :value="old('login_card_subtitle', $config->login_card_subtitle ?? 'Acesse sua conta para continuar')"
                                        placeholder="Acesse sua conta para continuar"
                                    />

                                    <x-admin::form.control-group.error control-name="login_card_subtitle" />
                                </x-admin::form.control-group>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Sparkles Toggle -->
                                <x-admin::form.control-group>
                                    <x-admin::form.control-group.label>
                                        @lang('theme-manager::app.settings.login-card.sparkles')
                                    </x-admin::form.control-group.label>

                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="hidden" name="login_card_sparkles" value="0" />
                                        <input
                                            type="checkbox"
                                            name="login_card_sparkles"
                                            value="1"
                                            class="sr-only peer"
                                            {{ old('login_card_sparkles', $config->login_card_sparkles ?? false) ? 'checked' : '' }}
                                        />
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                        <span class="ml-3 text-sm font-medium text-gray-600 dark:text-gray-300">
                                            Efeito de partículas
                                        </span>
                                    </label>

                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        @lang('theme-manager::app.settings.login-card.sparkles-help')
                                    </p>

                                    <x-admin::form.control-group.error control-name="login_card_sparkles" />
                                </x-admin::form.control-group>

                                <!-- Help Link Toggle -->
                                <x-admin::form.control-group>
                                    <x-admin::form.control-group.label>
                                        @lang('theme-manager::app.settings.login-card.help-link')
                                    </x-admin::form.control-group.label>

                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="hidden" name="login_card_help_link" value="0" />
                                        <input
                                            type="checkbox"
                                            name="login_card_help_link"
                                            value="1"
                                            class="sr-only peer"
                                            {{ old('login_card_help_link', $config->login_card_help_link ?? true) ? 'checked' : '' }}
                                        />
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                        <span class="ml-3 text-sm font-medium text-gray-600 dark:text-gray-300">
                                            Exibir link de ajuda
                                        </span>
                                    </label>

                                    <x-admin::form.control-group.error control-name="login_card_help_link" />
                                </x-admin::form.control-group>
                            </div>

                            <!-- Support Email -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>
                                    @lang('theme-manager::app.settings.login-card.support-email')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="email"
                                    name="login_card_support_email"
                                    :value="old('login_card_support_email', $config->login_card_support_email ?? 'suporte@empresa.com.br')"
                                    placeholder="suporte@empresa.com.br"
                                />

                                <x-admin::form.control-group.error control-name="login_card_support_email" />
                            </x-admin::form.control-group>
                        </div>
                    </div>

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

    @pushOnce('scripts')
        <script>
            // Toggle login card options based on enabled status
            function toggleLoginCardOptions() {
                const checkbox = document.getElementById('login_card_enabled');
                const options = document.getElementById('login-card-options');

                if (checkbox && options) {
                    if (checkbox.checked) {
                        options.style.display = 'grid';
                    } else {
                        options.style.display = 'none';
                    }
                }
            }

            // Update overlay color preview
            function updateOverlayColorPreview() {
                const input = document.getElementById('login_card_overlay_color');
                const preview = document.getElementById('overlay_color_preview');

                if (input && preview) {
                    preview.style.backgroundColor = input.value;
                }
            }

            // Execute on page load
            document.addEventListener('DOMContentLoaded', function() {
                // Initialize login card options visibility
                toggleLoginCardOptions();

                // Listen for overlay color changes
                const overlayInput = document.getElementById('login_card_overlay_color');
                if (overlayInput) {
                    overlayInput.addEventListener('input', updateOverlayColorPreview);
                    overlayInput.addEventListener('change', updateOverlayColorPreview);
                }

                // Sync color inputs (picker and text)
                document.querySelectorAll('input[type="color"]').forEach(function(colorInput) {
                    const name = colorInput.getAttribute('name');
                    const textInputs = document.querySelectorAll('input[type="text"][name="' + name + '"]');

                    colorInput.addEventListener('input', function() {
                        textInputs.forEach(function(textInput) {
                            textInput.value = colorInput.value.toUpperCase();
                        });
                    });

                    textInputs.forEach(function(textInput) {
                        textInput.addEventListener('input', function() {
                            // Validate hex color format
                            let value = textInput.value.trim();
                            if (value.match(/^#[0-9A-Fa-f]{6}$/)) {
                                colorInput.value = value;
                            }
                        });
                    });
                });
            });
        </script>
    @endPushOnce
</x-admin::layouts>
