@extends('admin::layouts.master')

@section('page_title')
    {{ __('admin::app.contacts.persons.title') }}
@stop

@section('content-wrapper')
    <div class="content full-page dashboard">
        <div class="page-header">
            <div class="page-title">
                <h1>{{ __('admin::app.contacts.persons.title') }}</h1>
            </div>

            <div class="page-action">
                <button class="btn btn-md btn-primary" @click="openModal('addPersonModal')">{{ __('admin::app.contacts.persons.add-title') }}</button>
            </div>
        </div>

        <div class="page-content">
        </div>
    </div>

    {{-- <form action="{{ route('admin.contacts.persons.store') }}" method="post" @submit.prevent="onSubmit"> --}}
    <form action="{{ route('admin.contacts.persons.store') }}" method="post">
        <modal id="addPersonModal" :is-open="modalIds.addPersonModal">
            <h3 slot="header-title">{{ __('admin::app.contacts.persons.add-title') }}</h3>
            
            <div slot="header-actions">
                <button class="btn btn-sm btn-secondary-outline" @click="closeModal('addPersonModal')">{{ __('admin::app.contacts.persons.cancel') }}</button>

                <button class="btn btn-sm btn-primary">{{ __('admin::app.contacts.persons.save-btn-title') }}</button>
            </div>

            <div slot="body">
                @csrf()
                
                <input type="hidden" name="quick_add" value="1"/>

                @include('admin::common.custom-attribute-controls', [
                    'customAttributes' => app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                        'entity_type' => 'persons',
                        'quick_add'   => 1
                    ])
                ])
            </div>
        </modal>
    </form>
@stop