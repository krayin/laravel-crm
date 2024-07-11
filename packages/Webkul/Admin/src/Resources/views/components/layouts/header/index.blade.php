<header class="sticky top-0 z-[10001] flex items-center justify-between border-b bg-white px-4 py-2.5 dark:border-gray-800 dark:bg-gray-900">
    <div class="flex items-center gap-1.5">
        <i class="icon-menu hidden cursor-pointer rounded-md p-1.5 text-2xl hover:bg-gray-100 dark:hover:bg-gray-950 max-lg:block"></i>

        <a href="{{ route('admin.dashboard.index') }}">
            <img
                class="h-10" 
                src="{{ asset('vendor/webkul/admin/assets/images/logo.svg') }}" 
                alt="{{ config('app.name') }}"
            />
        </a>

        <div class="relative flex w-[525px] max-w-[525px] items-center max-lg:w-[400px] ltr:ml-2.5 rtl:mr-2.5">
            <i class="icon-search absolute top-1.5 flex items-center text-2xl ltr:left-3 rtl:right-3"></i>
            <input
                type="text"
                class="peer block w-full rounded-lg border bg-white px-10 py-1.5 leading-6 text-gray-600 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400"
                placeholder="Mega Search">
        </div>
    </div>

    <div class="flex items-center gap-2.5">
        <!-- Quick create section -->
        @if (bouncer()->hasPermission('leads.create')
            || bouncer()->hasPermission('quotes.create')
            || bouncer()->hasPermission('mail.create')
            || bouncer()->hasPermission('contacts.persons.create')
            || bouncer()->hasPermission('contacts.organizations.create')
            || bouncer()->hasPermission('products.create')
            || bouncer()->hasPermission('settings.automation.attributes.create')
            || bouncer()->hasPermission('settings.user.roles.create')
            || bouncer()->hasPermission('settings.user.users.create')
        )
            <x-admin::dropdown position="bottom-right">
                <x-slot:toggle>
                    <button class="flex h-9 w-9 cursor-pointer items-center justify-center rounded-full bg-blue-400 text-sm font-semibold leading-6 text-white transition-all hover:bg-blue-500 focus:bg-blue-500">
                        {{ substr(auth()->guard('user')->user()->name, 0, 1) }}
                    </button>
                </x-slot>

                <!-- Admin Dropdown -->
                <x-slot:content class="!p-0">
                    <div class="relative">
                        <div class="absolute right-0 mt-2 w-64 shadow-lg bg-white ring-1 ring-black ring-opacity-5 dropdown-list">
                            <div class="grid grid-cols-3 gap-2 p-4 text-center quick-link-container text-xs font-medium">
                                @if (bouncer()->hasPermission('leads.create'))
                                    <div class="bg-white w-20 hover:bg-gray-100 rounded-lg quick-link-item">
                                        <a href="{{ route('admin.leads.create') }}" class="block text-gray-700 p-2">
                                            <i class="icon icon-settings w-10 h-10 mb-1"></i>
                                            <span>{{ __('admin::app.layouts.lead') }}</span>
                                        </a>
                                    </div>
                                @endif
                    
                                @if (bouncer()->hasPermission('quotes.create'))
                                    <div class="bg-white w-20 hover:bg-gray-100 rounded-lg quick-link-item">
                                        <a href="{{ route('admin.quotes.create') }}" class="block text-gray-700 p-2">
                                            <i class="icon quotation-icon w-10 h-10 mb-1"></i>
                                            <span>{{ __('admin::app.layouts.quote') }}</span>
                                        </a>
                                    </div>
                                @endif
                    
                                @if (bouncer()->hasPermission('mail.create'))
                                    <div class="bg-white w-20 hover:bg-gray-100 rounded-lg quick-link-item">
                                        <a href="{{ route('admin.mail.index', ['route' => 'compose']) }}" class="block text-gray-700 p-2">
                                            <i class="icon mail-icon w-10 h-10 mb-1"></i>
                                            <span>{{ __('admin::app.layouts.email') }}</span>
                                        </a>
                                    </div>
                                @endif
                    
                                @if (bouncer()->hasPermission('contacts.persons.create'))
                                    <div class="bg-white w-20 hover:bg-gray-100 rounded-lg quick-link-item">
                                        <a href="{{ route('admin.contacts.persons.create') }}" class="block text-gray-700 p-2">
                                            <i class="icon person-icon w-10 h-10 mb-1"></i>
                                            <span>{{ __('admin::app.layouts.person') }}</span>
                                        </a>
                                    </div>
                                @endif
                    
                                @if (bouncer()->hasPermission('contacts.organizations.create'))
                                    <div class="bg-white w-20 hover:bg-gray-100 rounded-lg quick-link-item">
                                        <a href="{{ route('admin.contacts.organizations.create') }}" class="block text-gray-700 p-2">
                                            <i class="icon organization-icon w-10 h-10 mb-1"></i>
                                            <span>{{ __('admin::app.layouts.organization') }}</span>
                                        </a>
                                    </div>
                                @endif
                    
                                @if (bouncer()->hasPermission('products.create'))
                                    <div class="bg-white w-20 hover:bg-gray-100 rounded-lg quick-link-item">
                                        <a href="{{ route('admin.products.create') }}" class="block text-gray-700 p-2">
                                            <i class="icon product-icon w-10 h-10 mb-1"></i>
                                            <span>{{ __('admin::app.layouts.product') }}</span>
                                        </a>
                                    </div>
                                @endif
                    
                                @if (bouncer()->hasPermission('settings.automation.attributes.create'))
                                    <div class="bg-white w-20 hover:bg-gray-100 rounded-lg quick-link-item">
                                        <a href="{{ route('admin.settings.attributes.create') }}" class="block text-gray-700 p-2">
                                            <i class="icon attribute-icon w-10 h-10 mb-1"></i>
                                            <span>{{ __('admin::app.layouts.attribute') }}</span>
                                        </a>
                                    </div>
                                @endif
                    
                                @if (bouncer()->hasPermission('settings.user.roles.create'))
                                    <div class="bg-white w-20 hover:bg-gray-100 rounded-lg quick-link-item">
                                        <a href="{{ route('admin.settings.roles.create') }}" class="block text-gray-700 p-2">
                                            <i class="icon role-icon w-10 h-10 mb-1"></i>
                                            <span>{{ __('admin::app.layouts.role') }}</span>
                                        </a>
                                    </div>
                                @endif
                    
                                @if (bouncer()->hasPermission('settings.user.users.create'))
                                    <div class="bg-white w-20 hover:bg-gray-100 rounded-lg quick-link-item">
                                        <a href="{{ route('admin.settings.users.create') }}" class="block text-gray-700 p-2">
                                            <i class="icon user-icon w-10 h-10 mb-1"></i>
                                            <span>{{ __('admin::app.layouts.user') }}</span>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </x-slot>
            </x-admin::dropdown>
        @endif
            
        <!-- Admin profile -->
        <x-admin::dropdown position="bottom-right">
            <x-slot:toggle>
                @if (auth()->guard('user')->user()->image)
                    <button class="flex h-9 w-9 cursor-pointer overflow-hidden rounded-full hover:opacity-80 focus:opacity-80">
                        <img
                            src="{{ auth()->guard('user')->user()->image_url }}"
                            class="w-full"
                        />
                    </button>
                @else
                    <button class="flex h-9 w-9 cursor-pointer items-center justify-center rounded-full bg-blue-400 text-sm font-semibold leading-6 text-white transition-all hover:bg-blue-500 focus:bg-blue-500">
                        {{ substr(auth()->guard('user')->user()->name, 0, 1) }}
                    </button>
                @endif
            </x-slot>

            <!-- Admin Dropdown -->
            <x-slot:content class="!p-0">
                <div class="flex items-center gap-1.5 border border-b-gray-300 px-5 py-2.5 dark:border-gray-800">
                    <img
                        src="{{ url('cache/logo.png') }}"
                        width="24"
                        height="24"
                    />

                    <!-- Version -->
                    <p class="text-gray-400">
                        Version: v{{ config('app.version') }}
                    </p>
                </div>

                <div class="grid gap-1 pb-2.5">
                    <a
                        class="cursor-pointer px-5 py-2 text-base text-gray-800 hover:bg-gray-100 dark:text-white dark:hover:bg-gray-950"
                        href="{{ route('admin.user.account.edit') }}"
                    >
                        My Account
                    </a>

                    <!--Admin logout-->
                    {{-- <x-admin::form
                        method="DELETE"
                        action="{{ route('admin.session.destroy') }}"
                        id="adminLogout"
                    >
                    </x-admin::form> --}}

                    <a
                        class="cursor-pointer px-5 py-2 text-base text-gray-800 hover:bg-gray-100 dark:text-white dark:hover:bg-gray-950"
                        href="{{ route('admin.session.destroy') }}"
                        onclick="event.preventDefault(); document.getElementById('adminLogout').submit();"
                    >
                        Logout
                    </a>
                </div>
            </x-slot>
        </x-admin::dropdown>
    </div>
</header>
