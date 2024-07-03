<div
    class="navbar-left"
    :class="{'open': isMenuOpen}"
>
    <ul class="menubar">
        @foreach (
            menu()->getItems('admin') 
            as $menuItem
        )
            <li
                class="menu-item {{ $menuItem->isActive() ? 'active' : 'inactive' }}"
                title="{{ $name = $menuItem->getName() }}"
                @if (
                    ! $menuItem->haveChildren()
                    || $menuItem->getKey() == 'settings'
                )
                    v-tooltip.right="{
                        content: '{{ $name }}',
                        classes: [isMenuOpen ? 'hide' : 'show']
                    }"
                @endif
            >

                <a href="{{ $menuItem->getUrl() }}">
                    <i class="icon sprite {{ $menuItem->getIcon() }}"></i>
                    
                    <span class="menu-label">{{ $menuItem->getName() }}</span>
                </a>

                @if (
                    ! in_array($menuItem->getKey(), ['settings', 'configuration'])
                    && $menuItem->haveChildren()
                )
                    <ul class="sub-menubar">
                        @foreach ($menuItem->getChildren() as $subMenuItem)
                            <li class="sub-menu-item {{ $subMenuItem->isActive() ? 'active' : 'inactive' }}">
                                <a href="{{ $subMenuItem->getUrl() }}">
                                    <span class="menu-label">{{ $subMenuItem->getName() }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </li>
        @endforeach
    </ul>

    <div
        class="menubar-bottom"
        @click="toggleMenu"
    >
        <span
            class="icon"
            v-bind:class="[isMenuOpen ? 'menu-fold-icon' : 'menu-unfold-icon']"
        ></span>
    </div>
</div>