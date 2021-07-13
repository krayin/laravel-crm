@extends('ui::datagrid.table')

@section('page_title')
    {{ __('admin::app.mail.' . request('route')) }}
@stop

@section('table-header')
    {!! view_render_event('admin.mail.index.header.before') !!}

    {{ Breadcrumbs::render('mail.route', request('route')) }}

    {{ __('admin::app.mail.' . request('route')) }}

    {!! view_render_event('admin.mail.index.header.after') !!}
@stop

@php
    $tableClass = "\Webkul\Admin\DataGrids\Mail\EmailDataGrid";
@endphp