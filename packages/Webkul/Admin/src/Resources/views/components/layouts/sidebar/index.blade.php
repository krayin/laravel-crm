<div 
    class="fixed top-14 z-[1000] h-full w-[270px] bg-white pt-4 shadow-[0px_8px_10px_0px_rgba(0,_0,_0,_0.2)] transition-all duration-300 group-[.sidebar-collapsed]/container:w-[70px] dark:bg-gray-900 max-lg:hidden"
    @mouseover="handleMouseOver"
    @mouseleave="handleMouseLeave"
>
    <div class="journal-scroll h-[calc(100vh-100px)] overflow-auto group-[.sidebar-collapsed]/container:overflow-visible">
        <nav class="grid w-full gap-2">
            <!-- Navigation Menu -->
            @foreach (menu()->getItems('admin') as $menuItem)
                <div
                    class="px-4 group/item {{ $menuItem->isActive() ? 'active' : 'inactive' }}"
                    onmouseenter="adjustSubMenuPosition(event)"
                >
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
                        <div class="fixed z-[100] hidden min-w-[180px] overflow-hidden rounded-none rounded-b-lg border border-gray-300 bg-white !p-0 pb-2 group-hover/item:!grid group-[.sidebar-collapsed]/container:fixed group-[.sidebar-collapsed]/container:!hidden group-[.sidebar-collapsed]/container:rounded-none group-[.sidebar-collapsed]/container:border group-[.sidebar-collapsed]/container:border-gray-300 group-[.sidebar-collapsed]/container:bg-white group-[.sidebar-collapsed]/container:p-[0] dark:border-gray-800 dark:bg-gray-900 group-[.sidebar-collapsed]/container:dark:border-gray-800 dark:group-[.sidebar-collapsed]/container:bg-gray-900 ltr:left-[270px] ltr:rounded-r-lg ltr:pl-10 ltr:shadow-[34px_10px_14px_rgba(0,0,0,0.01),19px_6px_12px_rgba(0,0,0,0.03),9px_3px_9px_rgba(0,0,0,0.04),2px_1px_5px_rgba(0,0,0,0.05),0px_0px_0px_rgba(0,0,0,0.05)] group-[.sidebar-collapsed]/container:ltr:!left-[70px] group-[.sidebar-collapsed]/container:ltr:rounded-r-lg group-[.sidebar-collapsed]/container:ltr:shadow-[34px_10px_14px_rgba(0,0,0,0.01),19px_6px_12px_rgba(0,0,0,0.03),9px_3px_9px_rgba(0,0,0,0.04),2px_1px_5px_rgba(0,0,0,0.05),0px_0px_0px_rgba(0,0,0,0.05)] rtl:right-[270px] rtl:rounded-l-lg rtl:pr-10 rtl:shadow-[-34px_10px_14px_rgba(0,0,0,0.01),-19px_6px_12px_rgba(0,0,0,0.03),-9px_3px_9px_rgba(0,0,0,0.04),-2px_1px_5px_rgba(0,0,0,0.05),-0px_0px_0px_rgba(0,0,0,0.05)] group-[.sidebar-collapsed]/container:rtl:!right-[70px] group-[.sidebar-collapsed]/container:rtl:rounded-l-lg group-[.sidebar-collapsed]/container:rtl:shadow-[-34px_10px_14px_rgba(0,0,0,0.01),-19px_6px_12px_rgba(0,0,0,0.03),-9px_3px_9px_rgba(0,0,0,0.04),-2px_1px_5px_rgba(0,0,0,0.05),-0px_0px_0px_rgba(0,0,0,0.05)]">
                            @foreach ($menuItem->getChildren() as $subMenuItem)
                                <a
                                    href="{{ $subMenuItem->getUrl() }}"
                                    class="text-sm text-{{ $subMenuItem->isActive() ? 'blue':'gray' }}-600 dark:text-{{ $subMenuItem->isActive() ? 'blue':'gray' }}-300 whitespace-nowrap py-1 group-[.sidebar-collapsed]/container:px-5 group-[.sidebar-collapsed]/container:py-2.5 px-5 py-2.5 hover:text-blue-600 dark:hover:bg-gray-950"
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
</div>

@pushOnce('scripts')
    <script>
        const adjustSubMenuPosition = (event) => {
            let menuContainer = event.currentTarget;

            let subMenuContainer = menuContainer.lastElementChild;

            if (subMenuContainer) {
                const menuTopOffset = menuContainer.getBoundingClientRect().top;

                const subMenuHeight = subMenuContainer.offsetHeight;

                const availableHeight = window.innerHeight - menuTopOffset;

                let subMenuTopOffset = menuTopOffset;

                if (subMenuHeight > availableHeight) {
                    subMenuTopOffset = menuTopOffset - (subMenuHeight - availableHeight);
                }

                subMenuContainer.style.top = `${subMenuTopOffset}px`;
            }
        };
    </script>
@endpushOnce