<v-sidebar-drawer>
    <i class="icon-menu hidden cursor-pointer rounded-md p-1.5 text-2xl hover:bg-gray-100 dark:hover:bg-gray-950 max-lg:block"></i>
</v-sidebar-drawer>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-sidebar-drawer-template"
    >
        <!-- Menu Sidebar Drawer -->
        <x-admin::drawer
            position="left"
            width="280px"
            ref="sidebarMenuDrawer"
        >
            <!-- Drawer Toggler -->
            <x-slot:toggle>
                <i class="icon-menu hidden cursor-pointer rounded-md p-1.5 text-2xl hover:bg-gray-100 dark:hover:bg-gray-950 max-lg:block"></i>
            </x-slot>
                    
            <!-- Drawer Header -->
            <x-slot:header>
                @if ($logo = core()->getConfigData('general.design.admin_logo.logo_image'))
                    <img
                        class="h-10"
                        src="{{ Storage::url($logo) }}"
                        alt="{{ config('app.name') }}"
                    />
                @else
                    <img
                        class="h-10"
                        src="{{ request()->cookie('dark_mode') ? vite()->asset('images/dark-logo.svg') : vite()->asset('images/logo.svg') }}"
                        id="logo-image"
                        alt="{{ config('app.name') }}"
                    />
                @endif
            </x-slot>

            <!-- Drawer Content -->
            <x-slot:content class="p-4">
                <div class="journal-scroll h-[calc(100vh-100px)] overflow-auto">
                    <nav class="grid w-full gap-2">
                        <!-- Navigation Menu -->
                        @foreach (menu()->getItems('admin') as $menuItem)
                            <div class="group/item relative">
                                <a
                                    href="{{ $menuItem->getUrl() }}"
                                    class="flex gap-2.5 p-1.5 items-center cursor-pointer hover:rounded-lg {{ $menuItem->isActive() == 'active' ? 'bg-brandColor rounded-lg' : ' hover:bg-gray-100 hover:dark:bg-gray-950' }} peer"
                                >
                                    <span class="{{ $menuItem->getIcon() }} text-2xl {{ $menuItem->isActive() ? 'text-white' : ''}}"></span>
                                    
                                    <p class="text-gray-600 dark:text-gray-300 font-semibold whitespace-nowrap group-[.sidebar-collapsed]/container:hidden {{ $menuItem->isActive() ? 'text-white' : ''}}">
                                        {{ $menuItem->getName() }}
                                    </p>
                                </a>


                                @if ($menuItem->haveChildren() && ! in_array($menuItem->getKey(), ['settings', 'configuration']))
                                    <div class="{{ $menuItem->isActive() ? ' !grid bg-gray-100 dark:bg-gray-950' : '' }} hidden min-w-[180px] ltr:pl-10 rtl:pr-10 pb-2 rounded-b-lg z-[100]">
                                        @foreach ($menuItem->getChildren() as $subMenuItem)
                                            <a
                                                href="{{ $subMenuItem->getUrl() }}"
                                                class="text-sm text-{{ $subMenuItem->isActive() ? 'blue':'gray' }}-600 dark:text-{{ $subMenuItem->isActive() ? 'blue':'gray' }}-300 whitespace-nowrap py-1 group-[.sidebar-collapsed]/container:px-5 group-[.sidebar-collapsed]/container:py-2.5 group-[.inactive]/item:px-5 group-[.inactive]/item:py-2.5 hover:text-bg-brandColor dark:hover:bg-gray-950"
                                            >
                                                {{ $subMenuItem->getName() }}
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </nav>
                </div>
            </x-slot>
        </x-admin::drawer>
    </script>

    <script type="module">
        app.component('v-sidebar-drawer', {
            template: '#v-sidebar-drawer-template',
        });
    </script>
@endPushOnce