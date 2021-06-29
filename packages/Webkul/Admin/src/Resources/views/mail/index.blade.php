@extends('ui::datagrid.table')

@section('page_title')
    {{ __('admin::app.mail.' . request('route')) }}
@stop

@section('table-header')
    {{ Breadcrumbs::render('mail.route', request('route')) }}

    {{ __('admin::app.mail.' . request('route')) }}
@stop

@php
    $tableClass = "\Webkul\Admin\DataGrids\Mail\EmailDataGrid";
@endphp