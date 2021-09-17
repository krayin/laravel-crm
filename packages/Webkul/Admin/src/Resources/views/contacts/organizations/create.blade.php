@extends('admin::layouts.master')

@section('page_title')
    {{ __('admin::app.contacts.organizations.create-title') }}
@stop

@section('content-wrapper')
    <div class="content full-page adjacent-center">

        {!! view_render_event('admin.contacts.organizations.create.header.before') !!}

        <div class="page-header">

            {{ Breadcrumbs::render('contacts.organizations.create') }}
            
            <div class="page-title">
                <h1>{{ __('admin::app.contacts.organizations.create-title') }}</h1>
            </div>
        </div>

        {!! view_render_event('admin.contacts.organizations.create.header.after') !!}

        <form method="POST" action="{{ route('admin.contacts.organizations.store') }}" @submit.prevent="onSubmit" enctype="multipart/form-data">

            <div class="page-content">
                <div class="form-container">

                    <div class="panel">
                        <div class="panel-header">
                            {!! view_render_event('admin.contacts.organizations.create.form_buttons.before') !!}

                            <button type="submit" class="btn btn-md btn-primary">
                                {{ __('admin::app.contacts.organizations.save-btn-title') }}
                            </button>

                            <a href="{{ route('admin.contacts.organizations.index') }}">{{ __('admin::app.contacts.organizations.back') }}</a>

                            {!! view_render_event('admin.contacts.organizations.create.form_buttons.after') !!}
                        </div>
        
                        <div class="panel-body">
                            {!! view_render_event('admin.contacts.organizations.create.form_controls.before') !!}

                            @csrf()
                
                            @include('admin::common.custom-attributes.edit', [
                                'customAttributes' => app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                                    'entity_type' => 'organizations',
                                ]),
                            ])

                            {!! view_render_event('admin.contacts.organizations.edit.form_controls.after') !!}
                        </div>
                    </div>
                </div>
            </div>

        </form>
    </div>
@stop