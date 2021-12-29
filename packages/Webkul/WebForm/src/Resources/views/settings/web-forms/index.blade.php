@extends('admin::layouts.master')

@section('page_title')
    {{ __('web_form::app.title') }}
@stop

@section('content-wrapper')
    <div class="content full-page">
        <table-component data-src="{{ route('admin.settings.web_forms.index') }}">
            <template v-slot:table-header>
                <h1>
                    {!! view_render_event('admin.settings.web_forms.index.header.before') !!}

                    {{ Breadcrumbs::render('settings.web_forms') }}

                    {{ __('web_form::app.title') }}

                    {!! view_render_event('admin.settings.web_forms.index.header.after') !!}
                </h1>
            </template>

            @if (bouncer()->hasPermission('settings.automation.web_forms.create'))
                <template v-slot:table-action>
                    <a href="{{ route('admin.settings.web_forms.create') }}" class="btn btn-md btn-primary">{{ __('web_form::app.create-title') }}</a>
                </template>
            @endif
        <table-component>
    </div>
@stop
