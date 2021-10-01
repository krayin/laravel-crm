@extends('admin::layouts.master')

@section('page_title')
    {{ __('admin::app.settings.pipelines.title') }}
@stop

@section('content-wrapper')
    <div class="content full-page">
        <table-component data-src="{{ route('admin.settings.pipelines.index') }}">
            <template v-slot:table-header>
                <h1>
                    {!! view_render_event('admin.settings.pipelines.index.header.before') !!}

                    {{ Breadcrumbs::render('settings.pipelines') }}

                    {{ __('admin::app.settings.pipelines.title') }}

                    {!! view_render_event('admin.settings.pipelines.index.header.after') !!}
                </h1>
            </template>

            @if (bouncer()->hasPermission('settings.lead.pipelines.create'))
                <template v-slot:table-action>
                    <a href="{{ route('admin.settings.pipelines.create') }}" class="btn btn-md btn-primary">
                        {{ __('admin::app.settings.pipelines.create-title') }}
                    </button>
                </template>
            @endif
        <table-component>
    </div>
@stop
