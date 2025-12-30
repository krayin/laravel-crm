@extends('admin::layouts.master')

@section('page_title')
    {{ __('lawfirm::app.processos.index') }}
@endsection

@section('content-wrapper')
    <div class="content full-page">
        <div class="page-header">
            <div class="page-title">
                <h1>{{ __('lawfirm::app.processos.index') }}</h1>
            </div>

            <div class="page-action">
                <a href="{{ route('admin.processos.create') }}" class="btn btn-primary">
                    {{ __('lawfirm::app.processos.create') }}
                </a>
            </div>
        </div>

        <div class="page-content">
            <datagrid-plus src="{{ route('admin.processos.index') }}"></datagrid-plus>
        </div>
    </div>
@endsection