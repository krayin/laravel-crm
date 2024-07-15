@extends('admin::layouts.master')

@section('page_title')
    {{ __('admin::app.settings.warehouses.edit-title') }}
@stop

@section('content-wrapper')
    <div class="content full-page adjacent-center">

        {!! view_render_event('admin.settings.warehouses.edit.header.before', ['warehouse' => $warehouse]) !!}

        <div class="page-header">
            {{ Breadcrumbs::render('settings.warehouses.edit', $warehouse) }}

            <div class="page-title">
                <h1>{{ __('admin::app.settings.warehouses.edit-title') }}</h1>
            </div>
        </div>

        {!! view_render_event('admin.settings.warehouses.edit.header.after', ['warehouse' => $warehouse]) !!}

        <form method="POST" action="{{ route('admin.settings.warehouses.update', $warehouse->id) }}" @submit.prevent="onSubmit" enctype="multipart/form-data">
            <div class="page-content">
                <div class="form-container">
                    <div class="panel">
                        <div class="panel-header">
                            {!! view_render_event('admin.settings.warehouses.edit.form_buttons.before', ['warehouse' => $warehouse]) !!}

                            <button type="submit" class="btn btn-md btn-primary">
                                {{ __('admin::app.settings.warehouses.save-btn-title') }}
                            </button>

                            <a href="{{ route('admin.settings.warehouses.index') }}">{{ __('admin::app.settings.warehouses.back') }}</a>

                            {!! view_render_event('admin.settings.warehouses.edit.form_buttons.after', ['warehouse' => $warehouse]) !!}
                        </div>
        
                        <div class="panel-body">
                            {!! view_render_event('admin.settings.warehouses.edit.form_controls.before', ['warehouse' => $warehouse]) !!}

                            @csrf()
                            
                            <input name="_method" type="hidden" value="PUT">
                
                            @include('admin::common.custom-attributes.edit', [
                                'customAttributes' => app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                                    'entity_type' => 'warehouses',
                                ]),
                                'entity'           => $warehouse,
                            ])

                            {!! view_render_event('admin.settings.warehouses.edit.form_controls.after', ['warehouse' => $warehouse]) !!}
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@stop