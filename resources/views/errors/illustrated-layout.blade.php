<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <title>@yield('title')</title>

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

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;500&display=swap" rel="stylesheet">


        <!-- Styles -->
        <style>
            * {
                box-sizing: border-box;
                -webkit-font-smoothing: antialiased;
            }

            body {
                overflow: unset;
                margin: 0;
                position: static;
                height: 100%;
                width: 100%;
                background: #F7F8F9;
                color: #263238;
                font-family: "Roboto";
                font-size: 18px;
                font-weight: 400;
                letter-spacing: -0.26px;
                line-height: 22px;
            }

            .anonymous-layout-container {
                text-align: center;
                position: absolute;
                width: 100%;
                height: 100%;
                display: table;
                z-index: 1;
                background: #F7F8F9;
            }

            .center-box {
                display: table-cell;
                vertical-align: middle;
            }

            .adjacent-center {
                width: 476px;
                display: inline-block;
            }

            .brand-logo img {
                width: 200px;
                margin-bottom: 20px;
            }

            label {
                font-size: 65px;
                font-weight: 300;
                margin-bottom: 25px;
                line-height: 65px;
                display: inline-block;
            }

            h1 {
                font-size: 23px;
                font-weight: 500;
                margin: 0;
                margin-bottom: 17px;
            }

            p {
                font-size: 14px;
                font-weight: 400;
            }

            .error-illustraton {
                margin-bottom: 20px;
            }

            ul {
                padding: 0;
                margin-bottom: 40px;
            }

            ul li {
                list-style: none;
                display: inline;
            }

            ul li:after {
                content: " \00b7";
            }

            ul li:last-child:after {
                content: none;
            }

            a {
                font-size: 14px;
                text-decoration: none;
                color: #0E90D9;
            }

            .copyright .separator {
                color: #000000;
            }

            .copyright p {
                color: #000000;
                font-size: 12px;
            }
        </style>
    </head>

    <body>
        <div class="anonymous-layout-container">
            <div class="center-box">

                <div class="adjacent-center">

                    <div class="brand-logo">
                        <img src="{{ asset('vendor/webkul/admin/assets/images/logo.svg') }}" alt="{{ config('app.name') }}"/>
                    </div>

                    <label>{{ __('Oops!') }}</label>

                    <h1>@yield('code') - @yield('title')</h1>

                    <p>@yield('message')</p>

                    <img class="error-illustraton" src="{{ asset('vendor/webkul/admin/assets/images/error-illustraton.svg') }}">

                    <p>{{ __('Few of the links which may help you to get back on the track -') }}</p>

                    <div class="quick-links">
                        <ul>
                            <li><a href="{{ route('admin.dashboard.index') }}">{{ __('Dashboard') }}</a></li>
                            <li><a href="{{ route('admin.session.create') }}">{{ __('Login') }}</a></li>
                            <li><a href="https://webkul.uvdesk.com">{{ __('Support') }}</a></li>
                            <li><a href="mailto: support@krayincrm.com">{{ __('Contact Krayin Team') }}</a></li>
                        </ul>
                    </div>

                    <div class="copyright">
                        <span class="separator">. . .</span>

                        <p>© Copyright 2021 <a href="https:krayincrm.com">Krayin</a>, All right reserved.</p>
                    </div>

                </div>

            </div>
        </div>
    </body>
</html>
