@extends('admin::layouts.master')

@section('page_title')
    {{ __('admin::app.settings.locations.create-title') }}
@stop

@section('content-wrapper')
    <div class="content full-page adjacent-center">
        {!! view_render_event('admin.settings.locations.create.header.before') !!}

        <div class="page-header">

            {{ Breadcrumbs::render('settings.locations.create') }}

            <div class="page-title">
                <h1>{{ __('admin::app.settings.locations.create-title') }}</h1>
            </div>
        </div>

        {!! view_render_event('admin.settings.locations.create.header.after') !!}

        <form method="POST" action="{{ route('admin.settings.locations.store') }}" @submit.prevent="onSubmit" enctype="multipart/form-data">
            <div class="page-content">
                <div class="form-container">
                    <div class="panel">
                        <div class="panel-header">
                            {!! view_render_event('admin.settings.locations.create.form_buttons.before') !!}

                            <button type="submit" class="btn btn-md btn-primary">
                                {{ __('admin::app.settings.locations.save-btn-title') }}
                            </button>

                            <a href="{{ route('admin.settings.locations.index') }}">{{ __('admin::app.settings.locations.back') }}</a>

                            {!! view_render_event('admin.settings.locations.create.form_buttons.after') !!}
                        </div>
        
                        <div class="panel-body">
                            {!! view_render_event('admin.settings.locations.create.form_controls.before') !!}

                            @csrf()
                            
                            @include('admin::common.custom-attributes.edit', [
                                'customAttributes' => app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                                    'entity_type' => 'locations',
                                ]),
                            ])

                            {!! view_render_event('admin.settings.locations.create.form_controls.after') !!}
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@stop