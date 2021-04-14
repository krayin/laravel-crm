@extends('admin::layouts.master')

@section('page_title')
    {{ __('admin::app.contacts.organizations.title') }}
@stop

@section('content-wrapper')
    <div class="content full-page dashboard">
        <div class="page-header">
            <div class="page-title">
                <h1>{{ __('admin::app.contacts.organizations.title') }}</h1>
            </div>

            <div class="page-action">
                <button class="btn btn-md btn-primary" @click="openModal('addOrganizationModal')">{{ __('admin::app.contacts.organizations.add-title') }}</button>
            </div>
        </div>

        <div class="page-content">
        </div>
    </div>

    {{-- <form action="{{ route('admin.contacts.organizations.store') }}" method="post" @submit.prevent="onSubmit"> --}}
        <form action="{{ route('admin.contacts.organizations.store') }}" method="post">
        <modal id="addOrganizationModal" :is-open="modalIds.addOrganizationModal">
            <h3 slot="header-title">{{ __('admin::app.contacts.organizations.add-title') }}</h3>
            
            <div slot="header-actions">
                <button class="btn btn-sm btn-secondary-outline" @click="closeModal('addOrganizationModal')">{{ __('admin::app.contacts.organizations.cancel') }}</button>

                <button class="btn btn-sm btn-primary">{{ __('admin::app.contacts.organizations.save-btn-title') }}</button>
            </div>

            <div slot="body">
                @csrf()
                
                <input type="hidden" name="quick_add" value="1"/>

                @include('admin::common.custom-attribute-controls', [
                    'customAttributes' => app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                        'entity_type' => 'organizations',
                        'quick_add'   => 1
                    ])
                ])
            </div>
        </modal>
    </form>
@stop