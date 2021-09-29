@extends('admin::layouts.master')

@section('page_title')
    {{ __('admin::app.settings.workflows.title') }}
@stop

@section('content-wrapper')
    <div class="content full-page">
        <table-component data-src="{{ route('admin.settings.workflows.index') }}">
            <template v-slot:table-header>
                <h1>
                    {!! view_render_event('admin.settings.workflows.index.header.before') !!}

                    {{ Breadcrumbs::render('settings.workflows') }}

                    {{ __('admin::app.settings.workflows.title') }}

                    {!! view_render_event('admin.settings.workflows.index.header.after') !!}
                </h1>
            </template>

            @if (bouncer()->hasPermission('settings.automation.workflows.create'))
                <template v-slot:table-action>
                    <a href="{{ route('admin.settings.workflows.create') }}" class="btn btn-md btn-primary">{{ __('admin::app.settings.workflows.create-title') }}</a>
                </template>
            @endif
        <table-component>
    </div>
@stop
