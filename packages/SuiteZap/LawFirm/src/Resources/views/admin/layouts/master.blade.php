@extends('admin::layouts.master')

@section('page_title')
{{ __('lawfirm::app.admin.title') }}
@stop

@section('content-wrapper')
@yield('content-wrapper')
@stop