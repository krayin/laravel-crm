@extends('admin::layouts.master')

@section('page_title')
    {{ __('admin::app.settings.warehouses.title') }}
@stop

@section('content-wrapper')
    <div class="content full-page">
        <table-component data-src="{{ route('admin.settings.warehouses.index') }}">
            <template v-slot:table-header>
                <h1>
                    {!! view_render_event('admin.settings.warehouses.index.header.before') !!}

                    {{ Breadcrumbs::render('settings.warehouses') }}

                    {{ __('admin::app.settings.warehouses.title') }}

                    {!! view_render_event('admin.settings.warehouses.index.header.after') !!}
                </h1>
            </template>

            @if (bouncer()->hasPermission('settings.automation.warehouses.create'))
                <template v-slot:table-action>
                    <a href="{{ route('admin.settings.warehouses.create') }}" class="btn btn-md btn-primary">
                        {{ __('admin::app.settings.warehouses.create-title') }}
                    </a>
                </template>
            @endif
        <table-component>
    </div>
@stop
