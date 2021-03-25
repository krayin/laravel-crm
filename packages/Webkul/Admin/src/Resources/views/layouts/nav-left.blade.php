<div class="navbar-left" v-bind:class="{'open': isMenuOpen}">
    <ul class="menubar">
        <li class="menu-item active" v-tooltip.right="{ content: 'Dashboard', classes: isMenuOpen ? 'hide' : 'show' }">
            <a href="">
                <span class="icon dashboard-icon"></span>

                <span class="menu-label">Dashboard</span>
            </a>
        </li>

        <li class="menu-item" v-tooltip.right="{ content: 'Leads', classes: isMenuOpen ? 'hide' : 'show' }">
            <a href="">
                <span class="icon leads-icon"></span>

                <span class="menu-label">Leads</span>
            </a>
        </li>
        {{-- @foreach ($menu->items as $menuItem)
            <li class="menu-item {{ $menu->getActive($menuItem) }}">
                <a href="{{ count($menuItem['children']) ? current($menuItem['children'])['url'] : $menuItem['url'] }}">
                    <span class="icon {{ $menuItem['icon-class'] }}"></span>
                    
                    <span>{{ trans($menuItem['name']) }}</span>
                </a>
            </li>
        @endforeach --}}
    </ul>

    <div class="menubar-bottom" @click="toggleMenu">
        <span class="icon" v-bind:class="[isMenuOpen ? 'menu-fold-icon' : 'menu-unfold-icon']"></span>
    </div>
</div>