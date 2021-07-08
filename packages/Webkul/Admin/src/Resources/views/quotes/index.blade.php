@extends('ui::datagrid.table')

@section('page_title')
    {{ __('admin::app.quotes.title') }}
@stop

@section('table-header')
    {{ Breadcrumbs::render('quotes') }}

    {{ __('admin::app.quotes.title') }}
@stop

@php
    $tableClass = "\Webkul\Admin\DataGrids\Quote\QuoteDataGrid";
@endphp