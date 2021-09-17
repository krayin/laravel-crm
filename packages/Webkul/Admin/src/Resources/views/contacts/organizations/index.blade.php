@extends('ui::datagrid.table')

@section('page_title')
    {{ __('admin::app.contacts.organizations.title') }}
@stop

@section('table-header')
    {!! view_render_event('admin.contacts.organizations.index.header.before') !!}

    {{ Breadcrumbs::render('contacts.organizations') }}

    {{ __('admin::app.contacts.organizations.title') }}

    {!! view_render_event('admin.contacts.organizations.index.header.after') !!}
@stop

@php
    $tableClass = "\Webkul\Admin\DataGrids\Contact\OrganizationDataGrid";
@endphp

@section('table-action')
    <a href="{{ route('admin.contacts.organizations.create') }}" class="btn btn-md btn-primary">{{ __('admin::app.contacts.organizations.create-title') }}</a>
@stop