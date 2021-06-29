@extends('ui::datagrid.table')

@section('page_title')
    {{ __('admin::app.leads.title') }}
@stop

@section('table-header')
    {{ Breadcrumbs::render('leads') }}

    {{ __('admin::app.leads.title') }}
@stop

@php
    $viewType = request()->type ?? "kanban";
    
    $tableClass = "\Webkul\Admin\DataGrids\Lead\LeadDataGrid";
@endphp

@push('css')
    <style>
        .modal-container {
            overflow-y: hidden;
        }

        .modal-container .tabs-content {
            overflow-y: scroll;
            max-height: calc(100vh - 300px);
        }

        .modal-container .tabs-content .form-group:last-child {
            margin-bottom: 0;
        }

        .modal-container .modal-header {
            border: 0;
        }

        .modal-container .modal-body {
            padding: 0;
        }

        .modal-container .modal-body .add-more-link {
            display: block;
            padding: 5px 0 0 0;
        }
    </style>
@endpush

@if ($viewType == "table")
    @include('admin::leads.list.table')
@else
    @php($showDefaultTable = false)

    @include('admin::leads.list.kanban')
@endif

@section('meta-content')
    <form action="{{ route('admin.leads.store') }}" method="post" @submit.prevent="onSubmit">
        <modal id="addLeadModal" :is-open="modalIds.addLeadModal">
            <h2 slot="header-title">{{ __('admin::app.leads.add-title') }}</h2>
            
            <div slot="header-actions">
                <button class="btn btn-sm btn-secondary-outline" @click="closeModal('addLeadModal')">{{ __('admin::app.leads.cancel') }}</button>

                <button class="btn btn-sm btn-primary">{{ __('admin::app.leads.save-btn-title') }}</button>
            </div>

            <div slot="body" style="padding: 0">
                @csrf()
                
                <input type="hidden" name="quick_add" value="1" />

                <input type="hidden" id="lead_stage_id" name="lead_stage_id" value="1" />

                <tabs>
                    <tab name="{{ __('admin::app.leads.details') }}" :selected="true">
                        @include('admin::common.custom-attributes.edit', [
                            'customAttributes' => app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                                'entity_type' => 'leads',
                                'quick_add'   => 1
                            ]),
                        ])
                    </tab>

                    <tab name="{{ __('admin::app.leads.contact-person') }}">
                        @include('admin::leads.common.contact')

                        <contact-component></contact-component>
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