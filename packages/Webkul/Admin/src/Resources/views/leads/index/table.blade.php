<div class="content full-page">
    <table-component data-src="{{ route('admin.leads.index') }}" switch-page-url="{{ route('admin.leads.index') }}">
        <template v-slot:table-header>
            <h1>
                {!! view_render_event('admin.leads.index.header.before') !!}

                {{ Breadcrumbs::render('leads') }}

                {{ __('admin::app.leads.title') }}

                {!! view_render_event('admin.leads.index.header.after') !!}
            </h1>
        </template>

        @if (bouncer()->hasPermission('leads.create'))
            <template v-slot:table-action>
                <a href="{{ route('admin.leads.create') }}" class="btn btn-md btn-primary">{{ __('admin::app.leads.create-title') }}</a>
            </template>
        @endif
    <table-component>
</div>
