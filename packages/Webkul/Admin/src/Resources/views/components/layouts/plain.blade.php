{{-- resources/views/vendor/webkul/admin/components/layouts/plain.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_','-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>@yield('page_title')</title>
    <!-- estilos do painel -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body class="bg-gray-100 dark:bg-gray-900">
    <main class="min-h-screen p-6">
        <header class="mb-6">
            <h1 class="text-2xl font-semibold text-gray-800 dark:text-white">
                @yield('page_title')
            </h1>
        </header>

        <section>
            @yield('content')
        </section>
    </main>
</body>
</html>
