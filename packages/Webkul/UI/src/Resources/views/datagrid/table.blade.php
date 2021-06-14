@extends('admin::layouts.master')

@section('page_title')
    @yield('table-header', "Table Default Header")
@stop

@section('content-wrapper')
    <div class="content full-page table">
        <div class="table-header">
            <h1>
                @yield('table-header', "Table Default Header")

                @yield('post-heading')
            </h1>

            <div class="table-action">
                @yield('table-action')
            </div>
        </div>

        @if ($showDefaultTable ?? true)
            <sidebar-filter></sidebar-filter>

            @section('table-section')
                <table-component table-class="{{ $tableClass }}"><table-component>
            @show
        @else
            @section('table-section')
            @show
        @endif
    </div>

    @yield('meta-content')
@stop

@push('scripts')
    <script>
        window.baseURL = "{{ config('app.url') }}";
        window.params = @json(request()->route()->parameters());
    </script>
@endpush
