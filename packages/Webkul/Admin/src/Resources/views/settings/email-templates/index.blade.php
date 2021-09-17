@extends('ui::datagrid.table')

@section('page_title')
    {{ __('admin::app.settings.email-templates.title') }}
@stop

@section('table-header')
    {!! view_render_event('admin.settings.email_templates.index.header.before') !!}

    {{ Breadcrumbs::render('settings.email_templates') }}

    {{ __('admin::app.settings.email-templates.title') }}

    {!! view_render_event('admin.settings.email_templates.index.header.after') !!}
@stop

@section('table-action')
    <a href="{{ route('admin.settings.email_templates.create') }}" class="btn btn-md btn-primary">{{ __('admin::app.settings.email-templates.create-title') }}</a>
@stop