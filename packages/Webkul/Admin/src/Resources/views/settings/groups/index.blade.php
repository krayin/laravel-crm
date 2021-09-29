@extends('admin::layouts.master')

@section('page_title')
    {{ __('admin::app.settings.groups.title') }}
@stop

@section('content-wrapper')
    <div class="content full-page">
        <table-component data-src="{{ route('admin.settings.groups.index') }}">
            <template v-slot:table-header>
                <h1>
                    {!! view_render_event('admin.settings.groups.index.header.before') !!}

                    {{ Breadcrumbs::render('settings.groups') }}

                    {{ __('admin::app.settings.groups.title') }}

                    {!! view_render_event('admin.settings.groups.index.header.after') !!}
                </h1>
            </template>

            @if (bouncer()->hasPermission('settings.user.groups.create'))
                <template v-slot:table-action>
                    <a href="{{ route('admin.settings.groups.create') }}" class="btn btn-md btn-primary">
                        {{ __('admin::app.settings.groups.create-title') }}
                    </a>
                </template>
            @endif
        <table-component>
    </div>
@stop
