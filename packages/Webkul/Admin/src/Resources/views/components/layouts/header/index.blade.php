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
