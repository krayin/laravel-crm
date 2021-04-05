@extends('admin::layouts.master')

@section('page_title')
    @yield('table-header', "Table Default Header")
@stop

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
@stop

@section('content-wrapper')
    <div class="content full-page dashboard table">
        <div class="table-header">
            <h1>
                @yield('table-header', "Table Default Header")
            </h1>

            <div class="table-action">
                @yield('table-action')
            </div>
        </div>

        <sidebar-filter></sidebar-filter>

        <table-component table-class="{{ $tableClass }}"><table-component>
    </div>
@stop
