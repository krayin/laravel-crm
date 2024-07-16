<div 
    class="fixed top-14 z-[1000] h-full w-[270px] bg-white pt-4 shadow-[0px_8px_10px_0px_rgba(0,_0,_0,_0.2)] group-[.sidebar-collapsed]/container:w-[70px] dark:bg-gray-900 max-lg:hidden"
    @mouseover="handleMouseOver"
    @mouseleave="handleMouseLeave"
>
    <div class="journal-scroll h-[calc(100vh-100px)] overflow-auto group-[.sidebar-collapsed]/container:overflow-visible">
        <nav class="grid w-full gap-2">
            <!-- Navigation Menu -->
            @foreach (menu()->getItems('admin') as $menuItem)
                <div class="px-4 group/item {{ $menuItem->isActive() ? 'active' : 'inactive' }}">
                    <a
                        href="{{ $menuItem->getUrl() }}"
                        class="flex gap-2.5 p-1.5 items-center cursor-pointer hover:rounded-lg {{ $menuItem->isActive() == 'active' ? 'bg-blue-600 rounded-lg' : ' hover:bg-gray-100 hover:dark:bg-gray-950' }} peer"
                    >
                        <span class="{{ $menuItem->getIcon() }} text-2xl {{ $menuItem->isActive() ? 'text-white' : ''}}"></span>
                        
                        <p class="text-gray-600 dark:text-gray-300 font-semibold whitespace-nowrap group-[.sidebar-collapsed]/container:hidden {{ $menuItem->isActive() ? 'text-white' : ''}}">
                            {{ $menuItem->getName() }}
                        </p>
                    </a>

                    @if ($menuItem->haveChildren())
                        <div class="fixed z-[100] hidden h-[calc(100vh-60px)] min-w-[180px] overflow-hidden rounded-none rounded-b-lg border border-gray-300 bg-white !p-0 pb-2 group-hover/item:!flex group-hover/item:!flex-col group-hover/item:!gap-2 dark:border-gray-800 dark:bg-gray-900 ltr:left-[270px] ltr:rounded-r-lg ltr:pl-10 ltr:shadow-[34px_10px_14px_rgba(0,0,0,0.01),19px_6px_12px_rgba(0,0,0,0.03),9px_3px_9px_rgba(0,0,0,0.04),2px_1px_5px_rgba(0,0,0,0.05),0px_0px_0px_rgba(0,0,0,0.05)] rtl:right-[270px] rtl:rounded-l-lg rtl:pr-10 rtl:shadow-[-34px_10px_14px_rgba(0,0,0,0.01),-19px_6px_12px_rgba(0,0,0,0.03),-9px_3px_9px_rgba(0,0,0,0.04),-2px_1px_5px_rgba(0,0,0,0.05),-0px_0px_0px_rgba(0,0,0,0.05)]">

                            <div 
                                class="fixed top-14 z-[1000] h-full w-[200px] bg-white pt-4 shadow-[0px_8px_10px_0px_rgba(0,_0,_0,_0.2)] dark:bg-gray-900 max-lg:hidden"
                            >
                                <div class="journal-scroll h-[calc(100vh-100px)] overflow-auto">
                                    <nav class="grid w-full gap-2">
                                        @foreach ($menuItem->getChildren() as $subMenuItem)
                                            <div class="px-4 group/item {{ $menuItem->isActive() ? 'active' : 'inactive' }}">
                                                <a
                                                    href="{{ $subMenuItem->getUrl() }}"
                                                    class="flex gap-2.5 p-1.5 items-center cursor-pointer hover:rounded-lg {{ $subMenuItem->isActive() == 'active' ? 'bg-blue-600 rounded-lg' : ' hover:bg-gray-100 hover:dark:bg-gray-950' }} peer"
                                                >
                                                    <p class="text-gray-600 dark:text-gray-300 font-semibold whitespace-nowrap {{ $subMenuItem->isActive() ? 'text-white' : ''}}">
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