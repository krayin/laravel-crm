@extends('ui::datagrid.table')

@section('page_title')
    {{ __('admin::app.quotes.title') }}
@stop

@section('table-header')
    {!! view_render_event('admin.quotes.index.header.before') !!}

    {{ Breadcrumbs::render('quotes') }}

    {{ __('admin::app.quotes.title') }}

    {!! view_render_event('admin.quotes.index.header.after') !!}
@stop

@php
    $tableClass = "\Webkul\Admin\DataGrids\Quote\QuoteDataGrid";
@endphp