{{--
    Anonymous Layout Override (Upgrade-Safe)

    Este arquivo sobrescreve: packages/Webkul/Admin/src/Resources/views/components/layouts/anonymous.blade.php
    Registrado via: App\Providers\ThemeBootProvider (View::prependNamespace)

    Modificações:
    - Adiciona classes de tema no <body> via $themeContext->bodyClasses()
    - Inclui partial theme-head.blade.php para injetar CSS vars
    - Mantém 100% compatibilidade com layout original quando tema desativado
--}}

@php
    // ThemeContext é injetado via ShareThemeContext middleware
    // Fallback seguro caso não exista
    $themeContext = $themeContext ?? \App\Support\ThemeContext::disabled();
@endphp

<!DOCTYPE html>
<html
    lang="{{ app()->getLocale() }}"
    dir="{{ in_array(app()->getLocale(), ['fa', 'ar']) ? 'rtl' : 'ltr' }}"
    class="{{ request()->cookie('dark_mode') ? 'dark' : '' }}"
>
<head>
    <title>{{ $title ?? '' }}</title>

    <meta charset="UTF-8">

    <meta
        http-equiv="X-UA-Compatible"
        content="IE=edge"
    >
    <meta
        http-equiv="content-language"
        content="{{ app()->getLocale() }}"
    >

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >
    <meta
        name="base-url"
        content="{{ url()->to('/') }}"
    >
    <meta
        name="currency-code"
        {{-- content="{{ core()->getCurrentCurrencyCode() }}" --}}
    >

    @stack('meta')

    {{
        vite()->set(['src/Resources/assets/css/app.css', 'src/Resources/assets/js/app.js'])
    }}

    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"
    />

    <link
        href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&display=swap"
        rel="stylesheet"
    />

    {{-- Favicon com suporte a tema --}}
    @if($themeContext->enabled && $themeContext->get('favicon'))
        <link
            type="image/x-icon"
            href="{{ Storage::disk('public')->url('theme-manager/' . $themeContext->get('favicon')) }}"
            rel="shortcut icon"
            sizes="16x16"
        >
    @elseif ($favicon = core()->getConfigData('general.design.admin_logo.favicon'))
        <link
            type="image/x-icon"
            href="{{ Storage::url($favicon) }}"
            rel="shortcut icon"
            sizes="16x16"
        >
    @else
        <link
            type="image/x-icon"
            href="{{ vite()->asset('images/favicon.ico') }}"
            rel="shortcut icon"
            sizes="16x16"
        />
    @endif

    @php
        // Usa cor primária do tema se ativo, senão usa config padrão
        $brandColor = $themeContext->enabled
            ? $themeContext->get('color_primary', '#0E90D9')
            : (core()->getConfigData('general.settings.menu_color.brand_color') ?? '#0E90D9');
    @endphp

    <style>
        :root {
            --brand-color: {{ $brandColor }};
        }

        {!! core()->getConfigData('general.content.custom_scripts.custom_css') !!}
    </style>

    {{-- Estilos do tema (CSS vars + estilos base) --}}
    @include('admin::partials.theme-head')

    @stack('styles')

    {!! view_render_event('admin.layout.head') !!}
</head>

<body class="{{ $themeContext->bodyClasses() }}">
    {!! view_render_event('admin.layout.body.before') !!}

    <div id="app">
        <!-- Flash Message Blade Component -->
        <x-admin::flash-group />

        {!! view_render_event('admin.layout.content.before') !!}

        <!-- Page Content Blade Component -->
        {{ $slot }}

        {!! view_render_event('admin.layout.content.after') !!}
    </div>

    {!! view_render_event('admin.layout.body.after') !!}

    @stack('scripts')

    {!! view_render_event('admin.layout.vue-app-mount.before') !!}

    <script>
        /**
         * Load event, the purpose of using the event is to mount the application
         * after all of our `Vue` components which is present in blade file have
         * been registered in the app. No matter what `app.mount()` should be
         * called in the last.
         */
        window.addEventListener("load", function(event) {
            app.mount("#app");
        });
    </script>

    {!! view_render_event('admin.layout.vue-app-mount.after') !!}

    <script type="text/javascript">
        {!! core()->getConfigData('general.content.custom_scripts.custom_javascript') !!}
    </script>
</body>
</html>
