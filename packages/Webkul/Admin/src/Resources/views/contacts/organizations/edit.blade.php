@extends('admin::layouts.master')

@section('page_title')
    {{ __('admin::app.contacts.organizations.edit-title') }}
@stop

@section('content-wrapper')
    <div class="content full-page adjacent-center">
        <div class="page-header">
            <div class="page-title">
                <h1>{{ __('admin::app.contacts.organizations.edit-title') }}</h1>
            </div>
        </div>

        {{-- <form method="POST" action="{{ route('admin.contacts.organizations.update', $organization->id) }}" @submit.prevent="onSubmit" enctype="multipart/form-data"> --}}
        <form method="POST" action="{{ route('admin.contacts.organizations.update', $organization->id) }}" enctype="multipart/form-data">

            <div class="page-content">
                <div class="form-container">

                    <div class="panel">
                        <div class="panel-header">
                            <button type="submit" class="btn btn-md btn-primary">
                                {{ __('admin::app.contacts.organizations.save-btn-title') }}
                            </button>

                            <a href="">{{ __('admin::app.contacts.organizations.back') }}</a>
                        </div>
        
                        <div class="panel-body">
                            @csrf()
                            <input name="_method" type="hidden" value="PUT">
                
                            @include('admin::common.custom-attribute-controls', [
                                'customAttributes' => app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                                    'entity_type' => 'organizations',
                                ]),
                                'entity'           => $organization,
                            ])

                        </div>
                    </div>
                </div>
            </div>

        </form>
    </div>
@stop