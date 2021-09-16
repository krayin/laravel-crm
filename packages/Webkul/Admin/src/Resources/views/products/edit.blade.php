@extends('admin::layouts.master')

@section('page_title')
    {{ __('admin::app.products.edit-title') }}
@stop

@section('content-wrapper')
    <div class="content full-page adjacent-center">
        {!! view_render_event('admin.products.edit.header.before', ['product' => $product]) !!}

        <div class="page-header">

            {{ Breadcrumbs::render('products.edit', $product) }}

            <div class="page-title">
                <h1>{{ __('admin::app.products.edit-title') }}</h1>
            </div>
        </div>

        {!! view_render_event('admin.products.edit.header.after', ['product' => $product]) !!}

        <form method="POST" action="{{ route('admin.products.update', $product->id) }}" @submit.prevent="onSubmit" enctype="multipart/form-data">

            <div class="page-content">
                <div class="form-container">

                    <div class="panel">
                        <div class="panel-header">
                            {!! view_render_event('admin.products.edit.form_buttons.before', ['product' => $product]) !!}

                            <button type="submit" class="btn btn-md btn-primary">
                                {{ __('admin::app.products.save-btn-title') }}
                            </button>

                            <a href="{{ route('admin.products.index') }}">{{ __('admin::app.products.back') }}</a>

                            {!! view_render_event('admin.products.edit.form_buttons.after', ['product' => $product]) !!}
                        </div>
        
                        <div class="panel-body">
                            {!! view_render_event('admin.products.edit.form_controls.before', ['product' => $product]) !!}

                            @csrf()

                            <input name="_method" type="hidden" value="PUT">
                
                            @include('admin::common.custom-attributes.edit', [
                                'customAttributes' => app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                                    'entity_type' => 'products',
                                ]),
                                'entity'           => $product,
                            ])

                            {!! view_render_event('admin.products.edit.form_controls.after', ['product' => $product]) !!}

                        </div>
                    </div>

                </div>

            </div>

        </form>

    </div>
@stop