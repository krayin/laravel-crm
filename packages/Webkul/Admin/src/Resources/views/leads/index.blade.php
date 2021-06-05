@extends('ui::datagrid.table')

@section('table-header')
    {{ __('admin::app.leads.title') }}
@stop

@php
    $viewType = request()->type ?? "table";
    $tableClass = "\Webkul\Admin\DataGrids\Lead\LeadDataGrid";
@endphp

@section('css')
    <style>
        .table-header h1 {
            padding-bottom: 15px;
        }
    </style>
@stop

@if ($viewType == "kanban")
    @section('post-heading')
        <div class="search-filter float-right">
            <input
                type="search"
                class="control"
                id="search-field"
                :placeholder="__('ui.datagrid.search')"
            />
        </div>
    @stop
@endif

@section('table-action')
    <button class="btn btn-md btn-primary" id="add-new" @click="openModal('addLeadModal')">
        {{ __('admin::app.leads.add-title') }}
    </button>

    <div class="float-right">
        <a
            @if ($viewType == 'kanban')
                href="{{ route('admin.leads.index') }}"
            @endif
            class="icon-container {{ $viewType == 'kanban' ? '' : 'active' }}">
            <i class="icon {{ $viewType == 'kanban' ? 'table-line-icon' : 'table-line-active-icon' }}"></i>
        </a>

        <a
            @if ($viewType != 'kanban')
                href="{{ route('admin.leads.index', ['type' => 'kanban']) }}"
            @endif
            class="icon-container {{ $viewType == 'kanban' ? 'active' : '' }}">
            <i class="icon {{ $viewType == 'kanban' ? 'layout-column-line-active-icon' : 'layout-column-line-icon' }}"></i>
        </a>
    </div>
@stop

@if ($viewType == "kanban")
    @section('table-section')
        <kanban-component
            detail-text="{{ __('admin::app.leads.add-title') }}"
            get-url="{{ route('admin.leads.kanban.index') }}"
            update-url="{{ route('admin.leads.kanban.update') }}"
        ></kanban-component>
    @stop
@endif

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