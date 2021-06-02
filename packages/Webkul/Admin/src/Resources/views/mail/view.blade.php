@extends('admin::layouts.master')

@section('page_title')
    {{ $email->subject }}
@stop

@section('content-wrapper')
    <div class="content full-page">
        <div class="page-header">
            <div class="page-title">
                <h1>{{ $email->subject }}</h1>
            </div>
        </div>

        <div class="page-content lead-view">
        </div>
    </div>
@stop