@extends('admin::layouts.master')

@section('page_title')
    {{ __('admin::app.contacts.persons.title') }}
@stop

@section('content-wrapper')
    <div class="content full-page table">
        <div class="table-header">
            <h1>
                {!! view_render_event('admin.contacts.persons.index.persons.before') !!}

                {{ Breadcrumbs::render('contacts.persons') }}

                {{ __('admin::app.contacts.persons.title') }}

                {!! view_render_event('admin.contacts.persons.index.persons.after') !!}
            </h1>

            <div class="table-action">
                <a href="{{ route('admin.contacts.persons.create') }}" class="btn btn-md btn-primary">{{ __('admin::app.contacts.persons.create-title') }}</a>
            </div>
        </div>

        <sidebar-filter></sidebar-filter>

        <table-component table-class="{{ '\Webkul\Admin\DataGrids\Contact\PersonDataGrid' }}"><table-component>
    </div>
@stop

@push('scripts')
    <script>
        window.baseURL = "{{ route('krayin.home') }}";

        window.params = @json(request()->route()->parameters());
    </script>
@endpush
