@extends('admin::layouts.master')

@section('page_title')
    {{ __('admin::app.settings.roles.title') }}
@stop

@section('content-wrapper')
    <div class="content full-page">
        <table-component data-src="{{ route('admin.settings.roles.index') }}">
            <template v-slot:table-header>
                <h1>
                    {!! view_render_event('admin.settings.roles.index.header.before') !!}

                    {{ Breadcrumbs::render('settings.roles') }}

                    {{ __('admin::app.settings.roles.title') }}

                    {!! view_render_event('admin.settings.roles.index.header.after') !!}
                </h1>
            </template>

            @if (bouncer()->hasPermission('settings.user.roles.create'))
                <template v-slot:table-action>
                    <a href="{{ route('admin.settings.roles.create') }}" class="btn btn-md btn-primary">
                        {{ __('admin::app.settings.roles.create-title') }}
                    </a>
                </template>
            @endif
        <table-component>
    </div>
@stop
