@extends('admin::layouts.master')

@section('page_title')
    {{ __('admin::app.dashboard.title') }}
@stop

@section('content-wrapper')
    <h1>
        @yield('table-header', "Table Default Header")
    </h1>

    <table-component result="{{ json_encode($result) }}"><table-component>
@stop
