<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
    <head>
        <title>@yield('page_title')</title>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <link rel="icon" sizes="16x16" href="{{ asset('vendor/webkul/ui/assets/images/favicon.ico') }}" />

        <link rel="stylesheet" href="{{ asset('vendor/webkul/ui/assets/css/ui.css') }}">
        <link rel="stylesheet" href="{{ asset('vendor/webkul/admin/assets/css/admin.css') }}">

        @yield('head')

        @yield('css')

        {!! view_render_event('admin.layout.head') !!}

    </head>

    <body style="scroll-behavior: smooth;">
        {!! view_render_event('admin.layout.body.before') !!}

        <div id="app">

            <flash-wrapper ref='flashes'></flash-wrapper>

            {!! view_render_event('admin.layout.nav-top.before') !!}

            @include ('admin::layouts.nav-top')

            {!! view_render_event('admin.layout.nav-top.after') !!}


            {!! view_render_event('admin.layout.nav-left.before') !!}

            @include ('admin::layouts.nav-left')

            {!! view_render_event('admin.layout.nav-left.after') !!}


            <div class="content-container" :style="{ paddingLeft: isMenuOpen ? '150px' : ''}">

                {!! view_render_event('admin.layout.content.before') !!}

                @yield('content-wrapper')

                {!! view_render_event('admin.layout.content.after') !!}

            </div>

        </div>

        <script type="text/javascript">
            window.flashMessages = [];

            @foreach (['success', 'warning', 'error', 'info'] as $key)
                @if ($value = session($key))
                    window.flashMessages.push({'type': '{{ $key }}', 'message': "{{ $value }}" });
                @endif
            @endforeach

            window.serverErrors = [];
            
            @if (isset($errors) && count($errors))
                window.serverErrors = @json($errors->getMessages());
            @endif
        </script>

        <script type="text/javascript" src="{{ asset('vendor/webkul/admin/assets/js/admin.js') }}"></script>
        <script type="text/javascript" src="{{ asset('vendor/webkul/ui/assets/js/ui.js') }}"></script>

        @stack('scripts')

        {!! view_render_event('admin.layout.body.after') !!}

        <div class="modal-overlay"></div>
    </body>
</html>