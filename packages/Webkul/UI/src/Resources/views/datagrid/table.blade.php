@extends('admin::layouts.master')

@section('page_title')
    @yield('table-header', "Table Default Header")
@stop

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <style>
        .content-container {
            padding: 25px 25px 25px 160px !important;
        }
    </style>
@stop

@section('content-wrapper')
    <h1>
        @yield('table-header', "Table Default Header")
    </h1>

    <sidebar-filter></sidebar-filter>

    <table-component table-class="{{ $tableClass }}"><table-component>
@stop
