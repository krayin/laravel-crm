<div class="navbar-top">
    <div class="navbar-top-left">
        <div class="brand-logo">
            <a href="{{ route('admin.dashboard.index') }}">
                <img src="{{ asset('vendor/webkul/admin/assets/images/logo.svg') }}" alt="{{ config('app.name') }}"/>
            </a>
        </div>
    </div>

    <div class="navbar-top-right">
        <div class="profile-info">
            <div class="avatar dropdown-toggle">
                <span class="icon avatar-icon"></span>
            </div>

            <div class="dropdown-list bottom-right">
                <span class="app-version">{{ __('admin::app.layouts.app-version', ['version' => 'v' . config('app.version')]) }}</span>

                <div class="dropdown-container">
                    <ul>
                        <li>
                            <a href="{{ route('admin.user.account.edit') }}">{{ __('admin::app.layouts.my-account') }}</a>
                        </li>
                        <li>
                            <a href="{{ route('admin.session.destroy') }}">{{ __('admin::app.layouts.sign-out') }}</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>