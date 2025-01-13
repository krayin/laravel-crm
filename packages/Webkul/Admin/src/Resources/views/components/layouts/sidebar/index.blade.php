<div
    ref="sidebar"
    class="duration-80 fixed top-[60px] z-[10002] h-full w-[200px] border-gray-200 bg-white pt-4 transition-all group-[.sidebar-collapsed]/container:w-[70px] dark:border-gray-800 dark:bg-gray-900 max-lg:hidden ltr:border-r rtl:border-l"
    @mouseover="handleMouseOver"
    @mouseleave="handleMouseLeave"
>
    <div class="journal-scroll h-[calc(100vh-100px)] overflow-hidden group-[.sidebar-collapsed]/container:overflow-visible">
        <nav class="sidebar-rounded grid w-full gap-2">
            <!-- Navigation Menu -->
            @foreach (menu()->getItems('admin') as $menuItem)
                <div class="px-4 group/item {{ $menuItem->isActive() ? 'active' : 'inactive' }}">
                    <a
                        class="flex gap-2 p-1.5 items-center cursor-pointer hover:rounded-lg {{ $menuItem->isActive() == 'active' ? 'bg-brandColor rounded-lg' : ' hover:bg-gray-100 hover:dark:bg-gray-950' }} peer"
                        href="{{ ! in_array($menuItem->getKey(), ['settings', 'configuration']) && $menuItem->haveChildren() ? 'javascript:void(0)' : $menuItem->getUrl() }}"
                        @mouseleave="!isMenuActive ? hoveringMenu = '' : {}"
                        @mouseover="hoveringMenu='{{$menuItem->getKey()}}'"
                        @click="isMenuActive = !isMenuActive"
                    >
                        <span class="{{ $menuItem->getIcon() }} text-2xl {{ $menuItem->isActive() ? 'text-white' : ''}}"></span>
                        
                        <div class="flex-1 flex justify-between items-center text-gray-600 dark:text-gray-300 font-medium whitespace-nowrap group-[.sidebar-collapsed]/container:hidden {{ $menuItem->isActive() ? 'text-white' : ''}} group">
                            <p>{{ $menuItem->getName() }}</p>
                        
                            @if ( ! in_array($menuItem->getKey(), ['settings', 'configuration']) && $menuItem->haveChildren())
                                <i class="icon-right-arrow rtl:icon-left-arrow invisible text-2xl group-hover/item:visible {{ $menuItem->isActive() ? 'text-white' : ''}}"></i>
                            @endif
                        </div>
                    </a>

                    <!-- Submenu -->
                    @if (
                        ! in_array($menuItem->getKey(), ['settings', 'configuration'])
                        && $menuItem->haveChildren()
                    )
                        <div
                            class="absolute top-0 hidden flex-col bg-gray-100 ltr:left-[200px] rtl:right-[199px]"
                            :class="[isMenuActive && (hoveringMenu == '{{$menuItem->getKey()}}') ? '!flex' : 'hidden']"
                        >
                            <div class="sidebar-rounded fixed z-[1000] h-full min-w-[140px] max-w-max bg-white pt-4 after:-right-[30px] dark:border-gray-800 dark:bg-gray-900 max-lg:hidden ltr:border-r rtl:border-x">
                                <div class="journal-scroll h-[calc(100vh-100px)] overflow-hidden">
                                    <nav class="grid w-full gap-2">
                                        @foreach ($menuItem->getChildren() as $subMenuItem)
                                            <div class="px-4 group/item {{ $menuItem->isActive() ? 'active' : 'inactive' }}">
                                                <a
                                                    href="{{ $subMenuItem->getUrl() }}"
                                                    class="flex gap-2.5 p-2 items-center cursor-pointer hover:rounded-lg {{ $subMenuItem->isActive() == 'active' ? 'bg-brandColor rounded-lg' : ' hover:bg-gray-100 hover:dark:bg-gray-950' }} peer"
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
