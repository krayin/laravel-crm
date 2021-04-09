@extends('admin::layouts.master')

@section('page_title')
    {{ __('admin::app.settings.attributes.title') }}
@stop

@section('content-wrapper')
    <div class="content full-page dashboard">
        <div class="page-header">
            <div class="page-title">
                <h1>{{ __('admin::app.settings.attributes.title') }}</h1>
            </div>

            <div class="page-action">
                <a href="{{ route('admin.settings.attributes.create') }}" class="btn btn-md btn-primary">{{ __('admin::app.settings.attributes.add-title') }}</a>
            </div>
        </div>

        <div class="page-content">
        </div>
    </div>
@stop