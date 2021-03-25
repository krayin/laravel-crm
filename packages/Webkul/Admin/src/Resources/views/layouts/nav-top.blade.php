<div class="navbar-top">
    <div class="navbar-top-left">
        <div class="brand-logo">
            <a href="{{ route('admin.dashboard.index') }}">
                <img src="{{ asset('vendor/webkul/admin/assets/images/logo.svg') }}" alt="{{ config('app.name') }}"/>
            </a>
        </div>
    </div>

    <div class="navbar-top-right">
        <div class="profile">
            <span class="avatar">
            </span>

            <div class="profile-info">
                <div class="dropdown-toggle">
                    <div style="display: inline-block; vertical-align: middle;">
                        <span class="name">
                            Jitendra Singh
                            {{-- {{ auth()->guard('admin')->user()->name }} --}}
                        </span>

                        <span class="role">
                            Admin
                            {{-- {{ auth()->guard('admin')->user()->role['name'] }} --}}
                        </span>
                    </div>
                    <i class="icon arrow-down-icon active"></i>
                </div>

                <div class="dropdown-list bottom-right">
                    <span class="app-version">{{ __('admin::app.layouts.app-version', ['version' => 'v' . config('app.version')]) }}</span>

                    <div class="dropdown-container">
                        <label>Account</label>
                        <ul>
                            <li>
                                Menu 1
                                {{-- <a href="{{ route('admin.account.edit') }}">{{ __('admin::app.layouts.my-account') }}</a> --}}
                            </li>
                            <li>
                                Menu 2
                                {{-- <a href="{{ route('admin.session.destroy') }}">{{ __('admin::app.layouts.logout') }}</a> --}}
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>