@extends('admin::layouts.master')

@section('page_title')
    {{ __('admin::app.settings.attributes.title') }}
@stop

@section('content-wrapper')
    <div class="content full-page">
        <table-component data-src="{{ route('admin.settings.attributes.index') }}">
            <template v-slot:table-header>
                <h1>
                    {!! view_render_event('admin.settings.attributes.index.header.before') !!}

                    {{ Breadcrumbs::render('settings.attributes') }}

                    {{ __('admin::app.settings.attributes.title') }}

                    {!! view_render_event('admin.settings.attributes.index.header.after') !!}
                </h1>
            </template>

            @if (bouncer()->hasPermission('settings.automation.attributes.create'))
                <template v-slot:table-action>
                    <a href="{{ route('admin.settings.attributes.create') }}" class="btn btn-md btn-primary">
                        {{ __('admin::app.settings.attributes.create-title') }}
                    </a>
                </template>
            @endif
        <table-component>
    </div>
@stop
