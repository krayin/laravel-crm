@extends('ui::datagrid.table')

@section('page_title')
    {{ __('admin::app.products.title') }}
@stop

@section('table-header')
    {!! view_render_event('admin.products.index.header.before') !!}

    {{ Breadcrumbs::render('products') }}

    {{ __('admin::app.products.title') }}

    {!! view_render_event('admin.products.index.header.after') !!}
@stop

@php
    $tableClass = "\Webkul\Admin\DataGrids\Product\ProductDataGrid";
@endphp

@section('table-action')
    <a href="{{ route('admin.products.create') }}" class="btn btn-md btn-primary">{{ __('admin::app.products.create-title') }}</a>
@stop