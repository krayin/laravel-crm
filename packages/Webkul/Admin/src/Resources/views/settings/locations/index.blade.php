@extends('admin::layouts.master')

@section('page_title')
    {{ __('admin::app.settings.locations.title') }}
@stop

@section('content-wrapper')
    <div class="content full-page">
        <table-component data-src="{{ route('admin.settings.locations.index') }}">
            <template v-slot:table-header>
                <h1>
                    {!! view_render_event('admin.settings.locations.index.header.before') !!}

                    {{ Breadcrumbs::render('settings.locations') }}

                    {{ __('admin::app.settings.locations.title') }}

                    {!! view_render_event('admin.settings.locations.index.header.after') !!}
                </h1>
            </template>

            @if (bouncer()->hasPermission('settings.automation.locations.create'))
                <template v-slot:table-action>
                    <a href="{{ route('admin.settings.locations.create') }}" class="btn btn-md btn-primary">
                        {{ __('admin::app.settings.locations.create-title') }}
                    </a>
                </template>
            @endif
        <table-component>
    </div>
@stop
