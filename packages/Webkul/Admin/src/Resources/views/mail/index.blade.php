@extends('ui::datagrid.table')

@section('table-header')
    {{ __('admin::app.mail.' . request('route')) }}
@stop

@php
    $tableClass = "\Webkul\Admin\DataGrids\Mail\EmailDataGrid";
@endphp