<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
    <head>
        <title>@yield('page_title')</title>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <link rel="apple-touch-icon" sizes="57x57" href="{{ asset('vendor/webkul/admin/assets/images/favicon/apple-icon-57x57.png') }}">
        <link rel="apple-touch-icon" sizes="60x60" href="{{ asset('vendor/webkul/admin/assets/images/favicon/apple-icon-60x60.png') }}">
        <link rel="apple-touch-icon" sizes="72x72" href="{{ asset('vendor/webkul/admin/assets/images/favicon/apple-icon-72x72.png') }}">
        <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('vendor/webkul/admin/assets/images/favicon/apple-icon-76x76.png') }}">
        <link rel="apple-touch-icon" sizes="114x114" href="{{ asset('vendor/webkul/admin/assets/images/favicon/apple-icon-114x114.png') }}">
        <link rel="apple-touch-icon" sizes="120x120" href="{{ asset('vendor/webkul/admin/assets/images/favicon/apple-icon-120x120.png') }}">
        <link rel="apple-touch-icon" sizes="144x144" href="{{ asset('vendor/webkul/admin/assets/images/favicon/apple-icon-144x144.png') }}">
        <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('vendor/webkul/admin/assets/images/favicon/apple-icon-152x152.png') }}">
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('vendor/webkul/admin/assets/images/favicon/apple-icon-180x180.png') }}">
        <link rel="icon" type="image/png" sizes="192x192"  href="{{ asset('vendor/webkul/admin/assets/images/favicon/android-icon-192x192.png') }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('vendor/webkul/admin/assets/images/favicon/favicon-32x32.png') }}">
        <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('vendor/webkul/admin/assets/images/favicon/favicon-96x96.png') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('vendor/webkul/admin/assets/images/favicon/favicon-16x16.png') }}">
        <link rel="manifest" href="{{ asset('vendor/webkul/admin/assets/images/favicon/manifest.json') }}">

        <link rel="stylesheet" href="{{ asset('vendor/webkul/ui/assets/css/ui.css') }}">
        <link rel="stylesheet" href="{{ asset('vendor/webkul/admin/assets/css/admin.css') }}">

        @yield('head')

        @yield('css')
        @stack('css')

        {!! view_render_event('admin.anonymous-layout.head') !!}
    </head>
    
    <body>        
        <div id="app" class="anonymous-layout-container">
            <spinner-meter :full-page="true" v-if="! pageLoaded"></spinner-meter>

            <flash-wrapper ref='flashes'></flash-wrapper>

            <div class="center-box">

                <div class="adjacent-center">

                    <div class="brand-logo">
                        <img src="{{ asset('vendor/webkul/admin/assets/images/logo.svg') }}" alt="{{ config('app.name') }}"/>

                        <p>Consumers interaction goes smoothly and efficiently</p>
                    </div>

                    {!! view_render_event('admin.anonymous-layout.content.before') !!}

                    @yield('content')

                    {!! view_render_event('admin.layout.content.after') !!}

                </div>

            </div>

        </div>

        <script type="text/javascript">
            window.flashMessages = [];

            @if ($success = session('success'))
                window.flashMessages = [{'type': 'success', 'message': "{{ $success }}" }];
            @elseif ($warning = session('warning'))
                window.flashMessages = [{'type': 'warning', 'message': "{{ $warning }}" }];
            @elseif ($error = session('error'))
                window.flashMessages = [{'type': 'error', 'message': "{{ $error }}" }];
            @endif


            window.serverErrors = [];
            
            @if (isset($errors) && count($errors))
                window.serverErrors = @json($errors->getMessages());
            @endif
        </script>

        <script type="text/javascript" src="{{ asset('vendor/webkul/admin/assets/js/admin.js') }}"></script>
        <script type="text/javascript" src="{{ asset('vendor/webkul/ui/assets/js/ui.js') }}"></script>

        @stack('scripts')

        {!! view_render_event('admin.anonymous-layout.body.after') !!}
    </body>
</html>
