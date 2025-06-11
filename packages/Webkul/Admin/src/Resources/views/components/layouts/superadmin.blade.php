<!DOCTYPE html>
<html
    class="h-full {{ request()->cookie('dark_mode') ? 'dark' : '' }}"
    lang="{{ app()->getLocale() }}"
    dir="{{ in_array(app()->getLocale(), ['fa','ar']) ? 'rtl' : 'ltr' }}"
>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>@yield('page_title')</title>

    {{-- Importa a fonte Inter via Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap"
      rel="stylesheet"
    >

    {{-- Carrega CSS/JS via Vite --}}
    {{ vite()->set([
        'src/Resources/assets/css/app.css',
        'src/Resources/assets/js/app.js'
    ]) }}

    @stack('styles')
</head>

<body class="h-full bg-gray-50 dark:bg-gray-900 font-inter antialiased">
    <div class="flex h-full">
        {{-- Sidebar --}}
        <aside class="w-64 bg-white dark:bg-gray-800 shadow-lg border-r border-gray-200 dark:border-gray-700 flex flex-col">
            {{-- Header da sidebar --}}
            <div class="px-6 py-8 border-b border-gray-200 dark:border-gray-700 mb-6">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-indigo-700 dark:from-blue-400 dark:to-indigo-500 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-gray-900 dark:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">
                            Super Admin
                        </h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Painel de Controle</p>
                    </div>
                </div>
            </div>

            {{-- Navigation --}}
            <nav class="flex-1 px-6 py-0">
                <ul class="space-y-2">
                    <li>
                        <a
                            href="{{ route('superAdmin.tenants.index') }}"
                            class="group flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('superAdmin.tenants.index') ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 border-r-2 border-blue-500' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white' }}"
                        >
                            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('superAdmin.tenants.index') ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            Tenants
                            @if(request()->routeIs('superAdmin.tenants.index'))
                                <div class="ml-auto w-2 h-2 bg-blue-500 rounded-full"></div>
                            @endif
                        </a>
                    </li>
                   <li>
                        <a
                            href="{{ route('superAdmin.users.index') }}"
                            class="group flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('superAdmin.users.index') ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 border-r-2 border-blue-500' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white' }}"
                        >
                            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('superAdmin.users.index') ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M 12 5 a 4 4 0 1 1 0 6 M 15 21 H 3 v -1 a 6 6 0 0 1 12 0 v 1 z m 0 0 h 6 v -1 a 6 6 0 0 0 -9 -5.197 m 1 -6.803 a 2.5 2.5 0 1 1 -9 0 a 2.5 2.5 0 0 1 9 0 z"/>
                            </svg>
                            Usuários
                            @if(request()->routeIs('superAdmin.users.index'))
                                <div class="ml-auto w-2 h-2 bg-blue-500 rounded-full"></div>
                            @endif
                        </a>
                    </li>
                </ul>
            </nav>

            {{-- Footer da sidebar --}}
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                <button 
                    onclick="toggleDarkMode()" 
                    class="w-full flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 rounded-lg transition-colors duration-200 cursor-pointer"
                >
                    <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                    </svg>
                    <span>Modo Escuro</span>
                    <div class="ml-4 w-8 h-4 bg-gray-200 dark:bg-gray-600 rounded-full relative">
                        <div id="toggle-switch" class="w-4 h-4 bg-gray-200 dark:bg-gray-700 rounded-full shadow absolute top-0 {{ request()->cookie('dark_mode') ? 'right-0' : 'left-0' }} transition-all duration-200"></div>
                    </div>
                </button>
            </div>
        </aside>

        {{-- Main content area --}}
        <main class="flex-1 flex flex-col overflow-hidden">
            {{-- Top header --}}
            <header class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                            @yield('page_title', 'Dashboard')
                        </h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            Gerencie seus tenants e configurações do sistema
                        </p>
                    </div>
                   
                </div>
            </header>

            {{-- Content area --}}
            <div class="flex-1 overflow-auto bg-gray-50 dark:bg-gray-900">
                @yield('content')
            </div>
        </main>
    </div>

    <script>
        function getDarkModeCookie() {
            const cookies = document.cookie.split(';');
            for (let cookie of cookies) {
                const [name, value] = cookie.trim().split('=');
                if (name === 'dark_mode') {
                    return parseInt(value) || 0;
                }
            }
            return 0;
        }

        function toggleDarkMode() {
            const isDarkMode = getDarkModeCookie() ? 0 : 1;
            
            // Set cookie with 1 month expiry
            const expiryDate = new Date();
            expiryDate.setMonth(expiryDate.getMonth() + 1);
            document.cookie = 'dark_mode=' + isDarkMode + '; path=/; expires=' + expiryDate.toGMTString();
            
            // Toggle dark class on html element
            document.documentElement.classList.toggle('dark', isDarkMode === 1);
            
            // Update toggle switch position
            const toggleSwitch = document.getElementById('toggle-switch');
            if (isDarkMode === 1) {
                toggleSwitch.classList.remove('left-0');
                toggleSwitch.classList.add('right-0');
            } else {
                toggleSwitch.classList.remove('right-0');
                toggleSwitch.classList.add('left-0');
            }
            
            // Emit events if you have an event emitter system
            if (window.$emitter) {
                if (isDarkMode) {
                    window.$emitter.emit('change-theme', 'dark');
                    // Update logo if you have one
                    const logoImage = document.getElementById('logo-image');
                    if (logoImage && window.dark_logo) {
                        logoImage.src = window.dark_logo;
                    }
                } else {
                    window.$emitter.emit('change-theme', 'light');
                    // Update logo if you have one
                    const logoImage = document.getElementById('logo-image');
                    if (logoImage && window.logo) {
                        logoImage.src = window.logo;
                    }
                }
            }
        }
    </script>

    @stack('scripts')
</body>
</html>