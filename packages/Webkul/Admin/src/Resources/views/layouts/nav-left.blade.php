<?php $menu = Menu::prepare(); ?>

<div class="navbar-left" v-bind:class="{'open': isMenuOpen}">
    <ul class="menubar">
        @foreach ($menu->items as $menuItem)
            <li class="menu-item {{ Menu::getActive($menuItem) }}">
                <a href="{{ count($menuItem['children']) ? current($menuItem['children'])['url'] : $menuItem['url'] }}">
                    <span class="icon {{ $menuItem['icon-class'] }}"></span>
                    
                    <span class="menu-label">{{ $menuItem['name'] }}</span>
                </a>

                @if (count($menuItem['children']))
                    <ul class="sub-menubar">
                        @foreach ($menuItem['children'] as $subMenuItem)
                            <li class="sub-menu-item {{ Menu::getActive($subMenuItem) }}">
                                <a href="{{ count($subMenuItem['children']) ? current($subMenuItem['children'])['url'] : $subMenuItem['url'] }}">
                                    <span class="menu-label">{{ $subMenuItem['name'] }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </li>
        @endforeach
    </ul>

    <div class="menubar-bottom" @click="toggleMenu">
        <span class="icon" v-bind:class="[isMenuOpen ? 'menu-fold-icon' : 'menu-unfold-icon']"></span>
    </div>
</div>