<header class="sticky top-0 z-[10001] flex items-center justify-between gap-1 border-b border-gray-200 bg-white px-4 py-2.5 transition-all dark:border-gray-800 dark:bg-gray-900">  
    <!-- logo -->
    <div class="flex items-center gap-1.5">
        <!-- Sidebar Menu -->
        <x-admin::layouts.sidebar.mobile />
        
        <a href="{{ route('admin.dashboard.index') }}">
            @if ($logo = core()->getConfigData('general.general.admin_logo.logo_image'))
                <img
                    class="h-10"
                    src="{{ Storage::url($logo) }}"
                    alt="{{ config('app.name') }}"
                />
            @else
                <img
                    class="h-10 max-sm:hidden"
                    src="{{ request()->cookie('dark_mode') ? vite()->asset('images/dark-logo.svg') : vite()->asset('images/logo.svg') }}"
                    id="logo-image"
                    alt="{{ config('app.name') }}"
                />

                <img
                    class="h-10 sm:hidden"
                    src="{{ request()->cookie('dark_mode') ? vite()->asset('images/mobile-dark-logo.svg') : vite()->asset('images/mobile-light-logo.svg') }}"
                    id="logo-image"
                    alt="{{ config('app.name') }}"
                />
            @endif
        </a>
    </div>

    <div class="flex items-center gap-1.5 max-md:hidden">
        <!-- Mega Search Bar -->
        @include('admin::components.layouts.header.desktop.mega-search')

        <!-- Quick Creation Bar -->
        @include('admin::components.layouts.header.quick-creation')
    </div>

    <div class="flex items-center gap-2.5">
        <div class="md:hidden">
            <!-- Mega Search Bar -->
            @include('admin::components.layouts.header.mobile.mega-search')
        </div>
        
        <!-- Switch Tenants -->
        @php
            $user = auth()->guard('user')->user();
            $tenants = bouncer()->getUserTenants();
            $currentTenantId = tenant('id');
            $currentTenant = collect($tenants)->firstWhere('id', $currentTenantId);
            $otherTenants = collect($tenants)->filter(fn ($tenant) => $tenant['id'] !== $currentTenantId);
            $roles = bouncer()->fetchRoles();
        @endphp

        @if ($otherTenants->isNotEmpty())
            <x-admin::dropdown position="bottom-{{ in_array(app()->getLocale(), ['fa', 'ar']) ? 'left' : 'right' }}">
                <x-slot:toggle>
                    <button
                        class="min-w-[20rem] py-1.5 px-4 rounded-md cursor-pointer transition-all hover:bg-gray-100 dark:text-white dark:hover:bg-gray-950"
                    >
                        <span class="flex items-center justify-between w-full">
                            <span>{{$currentTenant['name']}}</span>
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </span>
                    </button>
                </x-slot>

                <x-slot:content 
                    class="mt-2 border-t-0 !p-0 w-[25rem] flex flex-col" 
                    x-data="{ search: '', hasResults: true }"
                    style="max-height: calc(100vh - 10rem);"
                >
                    <!-- Search Bar -->
                    <div class="px-4 py-3 border-b dark:border-gray-700 flex-none" x-on:click.stop>
                        <input 
                            type="text" 
                            x-model="search"
                            placeholder="{{ __('admin::app.layouts.search-by-name-id') }}"
                            class="w-full px-3 py-2 text-sm border rounded-md dark:bg-gray-800 dark:border-gray-700 dark:text-white"
                            x-on:click.stop
                            x-on:input="
                                let visibleItems = 0;
                                document.querySelectorAll('.tenant-item').forEach(item => {
                                    const name = item.getAttribute('data-name').toLowerCase();
                                    const id = item.getAttribute('data-id');
                                    const isVisible = search === '' || name.includes(search.toLowerCase()) || id.includes(search);
                                    item.style.display = isVisible ? 'block' : 'none';
                                    if (isVisible) visibleItems++;
                                });
                                hasResults = visibleItems > 0;
                            "
                        >
                    </div>
                    
                    <!-- Tenants List Container -->
                    <div class="flex-1 overflow-y-auto" style="max-height: calc(100vh - 15rem);">
                        <div id="tenants-list">
                            @foreach ($otherTenants as $tenant)
                                <div 
                                    class="tenant-item"
                                    data-name="{{ $tenant['name'] ?? '' }}"
                                    data-id="{{ $tenant['id'] }}"
                                >
                                    <x-admin::form 
                                        method="POST" 
                                        action="{{ route('admin.tenant.switch') }}"
                                        x-on:click.stop
                                    >
                                        <input type="hidden" name="tenant_id" value="{{ $tenant['id'] }}">

                                        <button
                                            type="submit"
                                            class="w-full text-left cursor-pointer px-5 py-2.5 text-sm text-gray-800 hover:bg-gray-100 dark:text-white dark:hover:bg-gray-950 flex justify-between items-center"
                                        >
                                            <div>
                                                <div class="font-medium">{{ $tenant['name'] ?? __('admin::app.layouts.unknown') }}</div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">ID: {{ $tenant['id'] }}</div>
                                            </div>
                                            <span class="{{ 
                                                'px-2 inline-flex text-xs leading-5 font-semibold rounded-full '.
                                                ($tenant['role_id'] == 1 
                                                    ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200' 
                                                    : ($tenant['role_id'] == 2 
                                                        ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' 
                                                        : 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                                                    )
                                                )
                                            }}">
                                                {{ $roles[$tenant['role_id']] ?? __('admin::app.layouts.unknown') }}
                                            </span>                                           
                                        </button>
                                    </x-admin::form>
                                </div>
                            @endforeach
                            
                            <!-- No Results Message -->
                            <div 
                                class="px-4 py-3 text-center text-sm text-gray-500 dark:text-gray-400 tenant-no-results"
                                x-show="!hasResults"
                                style="display: none;"
                            >
                                {{ __('admin::app.layouts.no-other-tenants') }}
                            </div>
                        </div>
                    </div>
                </x-slot:content>
            </x-admin::dropdown>
        @else
            <button
                class="min-w-[20rem] py-1.5 px-4 rounded-md cursor-pointer transition-all hover:bg-gray-100 dark:text-white dark:hover:bg-gray-950 flex items-center justify-start"
            >
                <span>{{ $currentTenant['name'] ?? __('admin::app.layouts.current-account') }}</span>
            </button>
        @endif

        <!-- Dark mode -->
        <v-dark>
            <div class="flex">
                <span
                    class="{{ request()->cookie('dark_mode') ? 'icon-light' : 'icon-dark' }} p-1.5 rounded-md text-2xl cursor-pointer transition-all hover:bg-gray-100 dark:hover:bg-gray-950"
                ></span>
            </div>
        </v-dark>

        <div class="md:hidden">
            <!-- Quick Creation Bar -->
            @include('admin::components.layouts.header.quick-creation')
        </div>
        
        <!-- Admin profile -->
        <x-admin::dropdown position="bottom-{{ in_array(app()->getLocale(), ['fa', 'ar']) ? 'left' : 'right' }}">
            <x-slot:toggle>
                @php($user = auth()->guard('user')->user())

                @if ($user->image)
                    <button class="flex h-9 w-9 cursor-pointer overflow-hidden rounded-full hover:opacity-80 focus:opacity-80">
                        <img
                            src="{{ $user->image_url }}"
                            class="h-full w-full object-cover"
                        />
                    </button>
                @else
                    <button class="flex h-9 w-9 cursor-pointer items-center justify-center rounded-full bg-pink-400 font-semibold leading-6 text-white">
                        {{ substr($user->name, 0, 1) }}
                    </button>
                @endif
            </x-slot>

            <!-- Admin Dropdown -->
            <x-slot:content class="mt-2 border-t-0 !p-0">
                <div class="flex items-center gap-1.5 border border-x-0 border-b-gray-300 px-5 py-2.5 dark:border-gray-800">
                    <img
                        src="{{ url('cache/logo.png') }}"
                        width="24"
                        height="24"
                    />

                    <!-- Version -->
                    <p class="text-gray-400">
                        @lang('admin::app.layouts.app-version', ['version' => core()->version()])
                    </p>
                </div>

                <div class="grid gap-1 pb-2.5">
                    <a
                        class="cursor-pointer px-5 py-2 text-base text-gray-800 hover:bg-gray-100 dark:text-white dark:hover:bg-gray-950"
                        href="{{ route('admin.user.account.edit') }}"
                    >
                        @lang('admin::app.layouts.my-account')
                    </a>
                    
                    @if(auth()->guard('user')->user()->is_super)
                    <a
                        class="cursor-pointer px-5 py-2 text-base text-gray-800 hover:bg-gray-100 dark:text-white dark:hover:bg-gray-950"
                        href="{{ route('superAdmin.tenants.index') }}"
                        target="_blank"
                        rel="noopener noreferrer"
                    >
                        @lang('admin::app.layouts.super-admin')
                    </a>
                    @endif

                    <!--Admin logout-->
                    <x-admin::form
                        method="DELETE"
                        action="{{ route('admin.session.destroy') }}"
                        id="adminLogout"
                    >
                    </x-admin::form>

                    <a
                        class="cursor-pointer px-5 py-2 text-base text-gray-800 hover:bg-gray-100 dark:text-white dark:hover:bg-gray-950"
                        href="{{ route('admin.session.destroy') }}"
                        onclick="event.preventDefault(); document.getElementById('adminLogout').submit();"
                    >
                        @lang('admin::app.layouts.sign-out')
                    </a>
                </div>
            </x-slot>
        </x-admin::dropdown>
    </div>
</header>

@pushOnce('styles')
<style>
    #tenants-list {
        contain: content;
        overscroll-behavior: contain;
    }
    #tenants-list > div {
        min-height: min-content;
    }
</style>
@endPushOnce

@pushOnce('scripts')
    <script src="//unpkg.com/alpinejs" defer></script>

    <script
        type="text/x-template"
        id="v-dark-template"
    >
        <div class="flex">
            <span
                class="cursor-pointer rounded-md p-1.5 text-2xl transition-all hover:bg-gray-100 dark:hover:bg-gray-950"
                :class="[isDarkMode ? 'icon-light' : 'icon-dark']"
                @click="toggle"
            ></span>
        </div>
    </script>

    <script type="module">
        app.component('v-dark', {
            template: '#v-dark-template',

            data() {
                return {
                    isDarkMode: {{ request()->cookie('dark_mode') ?? 0 }},

                    logo: "{{ vite()->asset('images/logo.svg') }}",

                    dark_logo: "{{ vite()->asset('images/dark-logo.svg') }}",
                };
            },

            methods: {
                toggle() {
                    this.isDarkMode = parseInt(this.isDarkModeCookie()) ? 0 : 1;

                    var expiryDate = new Date();

                    expiryDate.setMonth(expiryDate.getMonth() + 1);

                    document.cookie = 'dark_mode=' + this.isDarkMode + '; path=/; expires=' + expiryDate.toGMTString();

                    document.documentElement.classList.toggle('dark', this.isDarkMode === 1);

                    if (this.isDarkMode) {
                        this.$emitter.emit('change-theme', 'dark');

                        document.getElementById('logo-image').src = this.dark_logo;
                    } else {
                        this.$emitter.emit('change-theme', 'light');

                        document.getElementById('logo-image').src = this.logo;
                    }
                },

                isDarkModeCookie() {
                    const cookies = document.cookie.split(';');

                    for (const cookie of cookies) {
                        const [name, value] = cookie.trim().split('=');

                        if (name === 'dark_mode') {
                            return value;
                        }
                    }

                    return 0;
                },
            },
        });
    </script>
@endPushOnce
