<div 
    class="duration-80 fixed top-14 z-[1000] h-full w-[190px] bg-white pt-4 transition-all group-[.sidebar-collapsed]/container:w-[70px] dark:bg-gray-900 max-lg:hidden"
    @mouseover="handleMouseOver"
    @mouseleave="handleMouseLeave"
>
    <div class="journal-scroll h-[calc(100vh-100px)] overflow-auto group-[.sidebar-collapsed]/container:overflow-visible">
        <nav class="sidebar-rounded grid w-full gap-2">
            <!-- Navigation Menu -->
            @foreach (menu()->getItems('admin') as $menuItem)
                <div class="px-4 group/item {{ $menuItem->isActive() ? 'active' : 'inactive' }}">
                    <a
                        href="{{ $menuItem->getUrl() }}"
                        class="flex gap-2.5 p-1.5 items-center cursor-pointer hover:rounded-lg {{ $menuItem->isActive() == 'active' ? 'bg-brandColor rounded-lg' : ' hover:bg-gray-100 hover:dark:bg-gray-950' }} peer"
                    >
                        <span class="{{ $menuItem->getIcon() }} text-2xl {{ $menuItem->isActive() ? 'text-white' : ''}}"></span>
                        
                        <div class="flex-1 flex justify-between items-center text-gray-600 dark:text-gray-300 font-medium whitespace-nowrap group-[.sidebar-collapsed]/container:hidden {{ $menuItem->isActive() ? 'text-white' : ''}} group">
                            <p>{{ $menuItem->getName() }}</p>
                        
                            @if ($menuItem->haveChildren())
                                <i class="icon-arrow-left invisible text-2xl group-hover:visible {{ $menuItem->isActive() ? 'text-white' : ''}}"></i>
                            @endif
                        </div>
                    </a>

                    <!-- Submenu -->
                    @if ($menuItem->haveChildren())
                        <div class="fixed z-[100] hidden h-[calc(100vh-60px)] min-w-[150px] overflow-hidden rounded-none rounded-b-lg bg-white !p-0 pb-2 group-hover/item:!flex group-hover/item:!flex-col group-hover/item:!gap-2 dark:bg-gray-900 ltr:left-[190px] ltr:rounded-r-lg ltr:pl-10 rtl:right-[190px] rtl:rounded-l-lg rtl:pr-10">
                            <div class="sidebar-rounded fixed top-14 z-[1000] h-full w-[150px] bg-white pt-4 dark:bg-gray-900 max-lg:hidden">
                                <div class="journal-scroll h-[calc(100vh-100px)] overflow-auto">
                                    <nav class="grid w-full gap-2">
                                        @foreach ($menuItem->getChildren() as $subMenuItem)
                                            <div class="px-4 group/item {{ $menuItem->isActive() ? 'active' : 'inactive' }}">
                                                <a
                                                    href="{{ $subMenuItem->getUrl() }}"
                                                    class="flex gap-2.5 p-1.5 items-center cursor-pointer hover:rounded-lg {{ $subMenuItem->isActive() == 'active' ? 'bg-brandColor rounded-lg' : ' hover:bg-gray-100 hover:dark:bg-gray-950' }} peer"
                                                >
                                                    <p class="text-gray-600 dark:text-gray-300 font-medium whitespace-nowrap {{ $subMenuItem->isActive() ? 'text-white' : ''}}">
                                                        {{ $subMenuItem->getName() }}
                                                    </p>
                                                </a>
                                            </div>
                                        @endforeach
                                    </nav>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        </nav>
    </div>
</div>