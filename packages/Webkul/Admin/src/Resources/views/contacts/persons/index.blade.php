@extends('ui::datagrid.table')

@section('page_title')
    {{ __('admin::app.contacts.persons.title') }}
@stop

@section('table-header')
    {!! view_render_event('admin.contacts.persons.index.persons.before') !!}

    {{ Breadcrumbs::render('contacts.persons') }}

    {{ __('admin::app.contacts.persons.title') }}

    {!! view_render_event('admin.contacts.persons.index.persons.after') !!}
@stop

@php
    $tableClass = "\Webkul\Admin\DataGrids\Contact\PersonDataGrid";
@endphp

@section('table-action')
    <a href="{{ route('admin.contacts.persons.create') }}" class="btn btn-md btn-primary">{{ __('admin::app.contacts.persons.add-title') }}</a>
@stop