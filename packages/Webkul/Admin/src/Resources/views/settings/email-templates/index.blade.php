@extends('admin::layouts.master')

@section('page_title')
    {{ __('admin::app.settings.email-templates.title') }}
@stop

@section('content-wrapper')
    <div class="content full-page">
        <table-component data-src="{{ route('admin.settings.email_templates.index') }}">
            <template v-slot:table-header>
                <h1>
                    {!! view_render_event('admin.settings.email_templates.index.header.before') !!}

                    {{ Breadcrumbs::render('settings.email_templates') }}

                    {{ __('admin::app.settings.email-templates.title') }}

                    {!! view_render_event('admin.settings.email_templates.index.header.after') !!}
                </h1>
            </template>

            @if (bouncer()->hasPermission('settings.automation.email_templates.create'))
                <template v-slot:table-action>
                    <a href="{{ route('admin.settings.email_templates.create') }}" class="btn btn-md btn-primary">{{ __('admin::app.settings.email-templates.create-title') }}</a>
                </template>
            @endif
        <table-component>
    </div>
@stop
