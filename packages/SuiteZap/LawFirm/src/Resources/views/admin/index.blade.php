@extends('admin::layouts.master')

@section('page_title')
{{ __('lawfirm::app.admin.title') }}
@stop

@section('content-wrapper')
<div class="content full-page">
    <div class="page-header">
        <div class="page-title">
            <h1>{{ __('lawfirm::app.admin.title') }}</h1>
        </div>
    </div>

    <div class="page-content">
        <div class="dashboard-stats">
            <div class="row">
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <h5>{{ __('lawfirm::app.admin.stats.total_cases') }}</h5>
                            <h2>0</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <h5>{{ __('lawfirm::app.admin.stats.active_cases') }}</h5>
                            <h2>0</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <h5>{{ __('lawfirm::app.admin.stats.upcoming_hearings') }}</h5>
                            <h2>0</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <h5>{{ __('lawfirm::app.admin.stats.total_clients') }}</h5>
                            <h2>0</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop