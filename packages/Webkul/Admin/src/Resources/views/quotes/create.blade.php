@extends('admin::layouts.master')

@section('page_title')
    {{ __('admin::app.quotes.add-title') }}
@stop

@section('content-wrapper')
    <div class="content full-page adjacent-center">
        <div class="page-header">

            {{ Breadcrumbs::render('quotes.create') }}

            <div class="page-title">
                <h1>{{ __('admin::app.quotes.add-title') }}</h1>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.quotes.store') }}" @submit.prevent="onSubmit">

            <div class="page-content">
                <div class="form-container">

                    <div class="panel">
                        <div class="panel-header">
                            <button type="submit" class="btn btn-md btn-primary">
                                {{ __('admin::app.quotes.save-btn-title') }}
                            </button>

                            <a href="{{ route('admin.quotes.index') }}">{{ __('admin::app.quotes.back') }}</a>
                        </div>
        
                        <div class="panel-body">
                            @csrf()

                            @include('admin::common.custom-attributes.edit', [
                                'customAttributes' => app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                                    'entity_type' => 'quotes',
                                ]),
                            ])
                        </div>
                    </div>

                </div>

            </div>

        </form>

    </div>
@stop