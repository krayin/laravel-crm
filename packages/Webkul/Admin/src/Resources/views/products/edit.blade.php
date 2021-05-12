@extends('admin::layouts.master')

@section('page_title')
    {{ __('admin::app.products.edit-title') }}
@stop

@section('content-wrapper')
    <div class="content full-page adjacent-center">
        <div class="page-header">
            <div class="page-title">
                <h1>{{ __('admin::app.products.edit-title') }}</h1>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.products.update', $product->id) }}" @submit.prevent="onSubmit" enctype="multipart/form-data">

            <div class="page-content">
                <div class="form-container">

                    <div class="panel">
                        <div class="panel-header">
                            <button type="submit" class="btn btn-md btn-primary">
                                {{ __('admin::app.products.save-btn-title') }}
                            </button>

                            <a href="{{ route('admin.products.index') }}">{{ __('admin::app.products.back') }}</a>
                        </div>
        
                        <div class="panel-body">
                            @csrf()
                            <input name="_method" type="hidden" value="PUT">
                
                            @include('admin::common.custom-attributes.edit', [
                                'customAttributes' => app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                                    'entity_type' => 'products',
                                ]),
                                'entity'           => $product,
                            ])

                        </div>
                    </div>

                </div>

            </div>

        </form>

    </div>
@stop