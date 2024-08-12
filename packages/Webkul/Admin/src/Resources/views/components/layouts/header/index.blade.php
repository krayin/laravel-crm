<header class="sticky top-0 z-[10001] flex items-center justify-between bg-white px-4 py-2.5 dark:border-gray-800 dark:bg-gray-900">
    <!-- logo -->
    <div class="flex items-center gap-1.5">
        <i class="icon-menu hidden cursor-pointer rounded-md p-1.5 text-2xl hover:bg-gray-100 dark:hover:bg-gray-950 max-lg:block"></i>

        <a href="{{ route('admin.dashboard.index') }}">
            <img
                class="h-10" 
                src="{{ admin_vite()->asset('images/logo.svg') }}" 
                alt="{{ config('app.name') }}"
            />
        </a>
    </div>

    <div class="flex items-center gap-1.5">
        <!-- Mega Search Bar Vue Component -->
        <div class="relative flex w-[525px] max-w-[525px] items-center max-lg:w-[400px] ltr:ml-2.5 rtl:mr-2.5">
            <i class="icon-search absolute top-1.5 flex items-center text-2xl ltr:left-3 rtl:right-3"></i>

            <input 
                type="text" 
                class="block w-full rounded-3xl border bg-white px-10 py-1.5 leading-6 text-gray-600 transition-all hover:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300"
                placeholder="@lang('Search')" 
            >
        </div>
        
        <!-- Quick create section -->
        <div>
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
                        <!-- Toggle Button -->
                        <button class="flex h-9 w-9 cursor-pointer items-center justify-center rounded-full bg-brandColor text-white">
                            <i class="icon-add text-2xl"></i>
                        </button>
                    </x-slot>

                    <!-- Dropdown Content -->
                    <x-slot:content class="mt-2 !p-0">
                        <div class="relative px-2 py-4">
                            <div class="grid grid-cols-3 gap-2 text-center">
                                <!-- Link to create new Lead -->
                                @if (bouncer()->hasPermission('leads.create'))
                                    <div class="rounded-lg bg-white p-2 hover:bg-gray-100">
                                        <a href="{{ route('admin.leads.create') }}">
                                            <div class="flex flex-col gap-1">
                                                <i class="icon-leads text-2xl text-gray-600"></i>
                                                
                                                <span class="font-medium">@lang('admin::app.layouts.lead')</span>
                                            </div>
                                        </a>
                                    </div>
                                @endif
                    
                                <!-- Link to create new Quotes -->
                                @if (bouncer()->hasPermission('quotes.create'))
                                    <div class="rounded-lg bg-white p-2 hover:bg-gray-100">
                                        <a href="{{ route('admin.quotes.create') }}">
                                            <div class="flex flex-col gap-1">
                                                <i class="icon-quote text-2xl text-gray-600"></i>
                                                
                                                <span class="font-medium">@lang('admin::app.layouts.quote')</span>
                                            </div>
                                        </a>
                                    </div>
                                @endif
                    
                                <!-- Link to send new Mail-->
                                @if (bouncer()->hasPermission('mail.create'))
                                    <div class="rounded-lg bg-white p-2 hover:bg-gray-100">
                                        <a href="{{ route('admin.mail.index', ['route' => 'compose']) }}">
                                            <div class="flex flex-col gap-1">
                                                <i class="icon-mail text-2xl text-gray-600"></i>
                                                
                                                <span class="font-medium">@lang('admin::app.layouts.email')</span>
                                            </div>
                                        </a>
                                    </div>
                                @endif
                    
                                <!-- Link to create new Person-->
                                @if (bouncer()->hasPermission('contacts.persons.create'))
                                    <div class="rounded-lg bg-white p-2 hover:bg-gray-100">
                                        <a href="{{ route('admin.contacts.persons.create') }}">
                                            <div class="flex flex-col gap-1">
                                                <i class="icon-settings-user text-2xl text-gray-600"></i>
                                                
                                                <span class="font-medium">@lang('admin::app.layouts.person')</span>
                                            </div>
                                        </a>
                                    </div>
                                @endif
                    
                                <!-- Link to create new Organizations -->
                                @if (bouncer()->hasPermission('contacts.organizations.create'))
                                    <div class="rounded-lg bg-white p-2 hover:bg-gray-100">
                                        <a href="{{ route('admin.contacts.organizations.create') }}">
                                            <div class="flex flex-col gap-1">
                                                <i class="icon-organization text-2xl text-gray-600"></i>
                                                
                                                <span class="font-medium">@lang('admin::app.layouts.organization')</span>
                                            </div>
                                        </a>
                                    </div>
                                @endif
                    
                                <!-- Link to create new Products -->
                                @if (bouncer()->hasPermission('products.create'))
                                    <div class="rounded-lg bg-white p-2 hover:bg-gray-100">
                                        <a href="{{ route('admin.products.create') }}">
                                            <div class="flex flex-col gap-1">
                                                <i class="icon-product text-2xl text-gray-600"></i>
                                                
                                                <span class="font-medium">@lang('admin::app.layouts.product')</span>
                                            </div>
                                        </a>
                                    </div>
                                @endif
                                
                                <!-- Link to create new Attributes -->
                                @if (bouncer()->hasPermission('settings.automation.attributes.create'))
                                    <div class="rounded-lg bg-white p-2 hover:bg-gray-100">
                                        <a href="{{ route('admin.settings.attributes.create') }}">
                                            <div class="flex flex-col gap-1">
                                                <i class="icon-attribute text-2xl text-gray-600"></i>
                                                
                                                <span class="font-medium">@lang('admin::app.layouts.attribute')</span>
                                            </div>
                                        </a>
                                    </div>
                                @endif
                    
                                <!-- Link to create new Role -->
                                @if (bouncer()->hasPermission('settings.user.roles.create'))
                                    <div class="rounded-lg bg-white p-2 hover:bg-gray-100">
                                        <a href="{{ route('admin.settings.roles.create') }}">
                                            <div class="flex flex-col gap-1">
                                                <i class="icon-role text-2xl text-gray-600"></i>
                                                
                                                <span class="font-medium">@lang('admin::app.layouts.role')</span>
                                            </div>
                                        </a>
                                    </div>
                                @endif
                    
                                <!-- Link to create new User-->
                                @if (bouncer()->hasPermission('settings.user.users.create'))
                                    <div class="rounded-lg bg-white p-2 hover:bg-gray-100">
                                        <a href="{{ route('admin.settings.users.create') }}">
                                            <div class="flex flex-col gap-1">
                                                <i class="icon-user text-2xl text-gray-600"></i>
                                                
                                                <span class="font-medium">@lang('admin::app.layouts.user')</span>
                                            </div>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </x-slot>
                </x-admin::dropdown>
            @endif
        </div>
    </div>

    <div class="flex items-center gap-2.5">
        <!-- Dark mode -->
        <v-dark>
            <div class="flex">
                <span
                    class="{{ request()->cookie('dark_mode') ? 'icon-light' : 'icon-dark' }} p-1.5 rounded-md text-2xl cursor-pointer transition-all hover:bg-gray-100 dark:hover:bg-gray-950"
                ></span>
            </div>
        </v-dark>
        
        <!-- Notification Component -->
        <span class="relative flex">
            <span 
                class="icon-notification cursor-pointer rounded-md p-1.5 text-2xl transition-all hover:bg-gray-100 dark:hover:bg-gray-950" 
                title="@lang('admin::app.components.layouts.header.notifications')"
            >
            </span>
        </span>
            
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
                    <button class="flex h-9 w-9 cursor-pointer items-center justify-center rounded-full bg-pink-400 font-semibold leading-6 text-white">
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
                        Logout
                    </a>
                </div>
            </x-slot>
        </x-admin::dropdown>
    </div>
</header>

@pushOnce('scripts')
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

                    logo: "{{ asset('images/logo.svg') }}",

                    dark_logo: "{{ asset('images/dark-logo.svg') }}",
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
