@extends('ui::datagrid.table')

@section('table-header')
    {{ __('admin::app.leads.title') }}
@stop

@php
    $tableClass = "\Webkul\Admin\DataGrids\Lead\LeadDataGrid";
@endphp

@section('css')
    <style>
        .modal-container .modal-header {
            border: 0;
        }

        .modal-container .modal-body {
            padding: 0;
        }
    </style>
@stop

@section('table-action')
    <button class="btn btn-md btn-primary" @click="openModal('addLeadModal')">{{ __('admin::app.leads.add-title') }}</button>
@stop

@section('meta-content')
    <form action="{{ route('admin.leads.store') }}" method="post" @submit.prevent="onSubmit">
        <modal id="addLeadModal" :is-open="modalIds.addLeadModal">
            <h3 slot="header-title">{{ __('admin::app.leads.add-title') }}</h3>
            
            <div slot="header-actions">
                <button class="btn btn-sm btn-secondary-outline" @click="closeModal('addLeadModal')">{{ __('admin::app.leads.cancel') }}</button>

                <button class="btn btn-sm btn-primary">{{ __('admin::app.leads.save-btn-title') }}</button>
            </div>

            <div slot="body">
                @csrf()
                
                <input type="hidden" name="quick_add" value="1"/>

                <tabs>
                    <tab name="{{ __('admin::app.leads.details') }}" :selected="true">
                        @include('admin::common.custom-attributes.edit', [
                            'customAttributes' => app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                                'entity_type' => 'leads',
                                'quick_add'   => 1
                            ])
                        ])
                    </tab>

                    <tab name="{{ __('admin::app.leads.contact-person') }}">
                        @include('admin::leads.common.contact')
                    </tab>

                    <tab name="{{ __('admin::app.leads.products') }}">
                        @include('admin::leads.common.products')

                        <product-list></product-list>
                    </tab>
                </tabs>
            </div>
        </modal>
    </form>
@stop