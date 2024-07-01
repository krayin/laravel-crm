<div
    class="navbar-left"
    :class="{'open': isMenuOpen}"
>
    <ul class="menubar">
        @foreach (menu()->getItems('admin') as $menuItem)
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