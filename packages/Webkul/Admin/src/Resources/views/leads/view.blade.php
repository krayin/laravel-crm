@extends('admin::layouts.master')

@section('page_title')
    {{ $lead->title }}
@stop

@section('content-wrapper')

    <div class="content full-page">
        <div class="page-header">
            <div class="page-title">
                <h1>{{ $lead->title }}</h1>
            </div>

            <div class="page-action">
                
            </div>
        </div>

        <div class="page-content lead-view">
            
            <div class="lead-content-left">
                <div class="panel">
                    <div class="panel-header">
                        Details
                    </div>
    
                    <div class="panel-body">
                    </div>
                </div>

                <div class="panel">
                    <div class="panel-header">
                        Contact Person
                    </div>
    
                    <div class="panel-body">
                    </div>
                </div>

                <div class="panel">
                    <div class="panel-header">
                        Products
                    </div>
    
                    <div class="panel-body">
                    </div>
                </div>
            </div>

            <div class="lead-content-right">
                Right
            </div>

        </div>
    </div>

@stop