<div class="phone">
    <span class="button dropdown-toggle">
        <i class="icon phone-white-icon"></i>
    </span>

    <div class="dropdown-list">
        <div class="quick-link-container">
            @if (bouncer()->hasPermission('leads.create'))
                <div class="quick-link-item">
                    <a href="{{ route('admin.leads.create') }}">
                        <i class="icon lead-icon"></i>
    
                        <span>{{ __('admin::app.layouts.lead') }}</span>
                    </a>
                </div>
            @endif
    
            @if (bouncer()->hasPermission('quotes.create'))
                <div class="quick-link-item">
                    <a href="{{ route('admin.quotes.create') }}">
                        <i class="icon quotation-icon"></i>
    
                        <span>{{ __('admin::app.layouts.quote') }}</span>
                    </a>
                </div>
            @endif
    
            @if (bouncer()->hasPermission('mail.create'))
                <div class="quick-link-item">
                    <a href="{{ route('admin.mail.index', ['route' => 'compose']) }}">
                        <i class="icon mail-icon"></i>
    
                        <span>{{ __('admin::app.layouts.email') }}</span>
                    </a>
                </div>
            @endif
    
            @if (bouncer()->hasPermission('contacts.persons.create'))
                <div class="quick-link-item">
                    <a href="{{ route('admin.contacts.persons.create') }}">
                        <i class="icon person-icon"></i>
    
                        <span>{{ __('admin::app.layouts.person') }}</span>
                    </a>
                </div>
            @endif
    
            @if (bouncer()->hasPermission('contacts.organizations.create'))
                <div class="quick-link-item">
                    <a href="{{ route('admin.contacts.organizations.create') }}">
                        <i class="icon organization-icon"></i>
    
                        <span>{{ __('admin::app.layouts.organization') }}</span>
                    </a>
                </div>
            @endif
    
            @if (bouncer()->hasPermission('products.create'))
                <div class="quick-link-item">
                    <a href="{{ route('admin.products.create') }}">
                        <i class="icon product-icon"></i>
    
                        <span>{{ __('admin::app.layouts.product') }}</span>
                    </a>
                </div>
            @endif
    
            @if (bouncer()->hasPermission('settings.automation.attributes.create'))
                <div class="quick-link-item">
                    <a href="{{ route('admin.settings.attributes.create') }}">
                        <i class="icon attribute-icon"></i>
    
                        <span>{{ __('admin::app.layouts.attribute') }}</span>
                    </a>
                </div>
            @endif
    
            @if (bouncer()->hasPermission('settings.user.roles.create'))
                <div class="quick-link-item">
                    <a href="{{ route('admin.settings.roles.create') }}">
                        <i class="icon role-icon"></i>
    
                        <span>{{ __('admin::app.layouts.role') }}</span>
                    </a>
                </div>
            @endif
    
            @if (bouncer()->hasPermission('settings.user.users.create'))
                <div class="quick-link-item">
                    <a href="{{ route('admin.settings.users.create') }}">
                        <i class="icon user-icon"></i>
    
                        <span>{{ __('admin::app.layouts.user') }}</span>
                    </a>
                </div>
            @endif
        </div>
    
    </div>
</div>