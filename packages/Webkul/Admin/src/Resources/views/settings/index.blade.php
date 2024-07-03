@extends('admin::layouts.master')

@section('page_title')
    {{ __('admin::app.settings.title') }}
@stop

@section('content-wrapper')
    <div class="content full-page">
        <div class="page-header">
            {{ Breadcrumbs::render('settings') }}

            <div class="page-title">
                <h1>
                    {{ __('admin::app.settings.title') }}
                </h1>
            </div>
        </div>

        <div class="page-content settings-container">
            @php
                $settings = menu()->getAdminMenuByKey('settings')->getChildren();
            @endphp 

            @foreach ($settings as $setting)
                <div class="panel">
                    <div class="panel-header">
                        <h3>{{ $setting->getName() }}</h3>

                        <p>{{ __($setting->getInfo()) }}</p>
                    </div>
                    
                    <div class="panel-body">
                        <div class="setting-link-container">
                            @foreach ($setting->getChildren() as $child)
                                <div class="setting-link-item">
                                    <a href="{{ $child->getUrl() }}">
                                        <div class="icon-container">
                                            <i class="icon {{ $child->getIcon() }}"></i>
                                        </div>
                                        
                                        <div class="setting-info">
                                            <label>{{ $child->getName() }}</label>

                                            <p>{{ $child->getInfo() }}</p>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@stop
