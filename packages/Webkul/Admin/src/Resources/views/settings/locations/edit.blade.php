@extends('admin::layouts.master')

@section('page_title')
    {{ __('admin::app.settings.locations.edit-title') }}
@stop

@section('content-wrapper')
    <div class="content full-page adjacent-center">

        {!! view_render_event('admin.settings.locations.edit.header.before', ['location' => $location]) !!}

        <div class="page-header">
            {{ Breadcrumbs::render('settings.locations.edit', $location) }}

            <div class="page-title">
                <h1>{{ __('admin::app.settings.locations.edit-title') }}</h1>
            </div>
        </div>

        {!! view_render_event('admin.settings.locations.edit.header.after', ['location' => $location]) !!}

        <form method="POST" action="{{ route('admin.settings.locations.update', $location->id) }}" @submit.prevent="onSubmit" enctype="multipart/form-data">
            <div class="page-content">
                <div class="form-container">
                    <div class="panel">
                        <div class="panel-header">
                            {!! view_render_event('admin.settings.locations.edit.form_buttons.before', ['location' => $location]) !!}

                            <button type="submit" class="btn btn-md btn-primary">
                                {{ __('admin::app.settings.locations.save-btn-title') }}
                            </button>

                            <a href="{{ route('admin.settings.locations.index') }}">{{ __('admin::app.settings.locations.back') }}</a>

                            {!! view_render_event('admin.settings.locations.edit.form_buttons.after', ['location' => $location]) !!}
                        </div>
        
                        <div class="panel-body">
                            {!! view_render_event('admin.settings.locations.edit.form_controls.before', ['location' => $location]) !!}

                            @csrf()
                            
                            <input name="_method" type="hidden" value="PUT">
                
                            @include('admin::common.custom-attributes.edit', [
                                'customAttributes' => app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                                    'entity_type' => 'locations',
                                ]),
                                'entity'           => $location,
                            ])

                            {!! view_render_event('admin.settings.locations.edit.form_controls.after', ['location' => $location]) !!}
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@stop