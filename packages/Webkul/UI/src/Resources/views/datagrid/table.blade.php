@extends('admin::layouts.master')

@section('page_title')
    @yield('table-header', "Table Default Header")
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

        @section('table-section')
            <table-component table-class="{{ $tableClass }}"><table-component>
        @show
    </div>
@stop

@push('scripts')
    <script>
        window.baseURL = "{{ config('app.url') }}";
    </script>
@endpush
