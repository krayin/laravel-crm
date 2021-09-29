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
            @php($menu = Menu::prepare())

            @foreach ($menu->items['settings']['children'] as $setting)
                <div class="panel">
                    <div class="panel-header">
                        <h3>{{ $setting['name'] }}</h3>

                        <p>{{ __($setting['info']) }}</p>
                    </div>
                    
                    <div class="panel-body">
                        <div class="setting-link-container">

                            @foreach ($setting['children'] as $subSetting)

                                <div class="setting-link-item">
                                    <a href="{{ $subSetting['url'] }}">
                                        <div class="icon-container">
                                            <i class="icon {{ $subSetting['icon-class'] ?? '' }}"></i>
                                        </div>
                                        
                                        <div class="setting-info">
                                            <label>{{ $subSetting['name'] }}</label>
                                            <p>{{ __($subSetting['info'] ?? '') }}</p>
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
