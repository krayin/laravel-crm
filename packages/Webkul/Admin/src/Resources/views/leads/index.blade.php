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

        .modal-container .modal-header {
            border: 0;
        }

        .modal-container .modal-body {
            padding: 0;
        }
    </style>
@stop

@if ($viewType == "kanban")
    @section('post-heading')
        <kanban-filters></kanban-filters>
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
            get-url="{{ route('admin.leads.kanban.index') }}"
            detail-text="{{ __('admin::app.leads.add-title') }}"
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

@if ($viewType == "kanban")
    @push('scripts')
        <script type="text/x-template" id="kanban-filters-tempalte">
            <div class="form-group post-heading">
                <input
                    type="search"
                    class="control"
                    id="search-field"
                    :placeholder="__('ui.datagrid.search')"
                />

                <sidebar-filter :columns="columns"></sidebar-filter>

                <div class="filter-btn">
                    <div class="grid-dropdown-header" @click="toggleSidebarFilter">
                        <span class="name">{{ __('ui::app.datagrid.filter.title') }}</span>

                        <i class="icon add-icon"></i>
                    </div>
                </div>
            </div>
        </script>

        <script>
            Vue.component('kanban-filters', {
                template: '#kanban-filters-tempalte',

                data: function () {
                    return {
                        debounce: [],
                        columns: {
                            'created_at': {
                                'filterable_type'   : 'date_range',
                                'label'             : "{{ trans('admin::app.datagrid.created_at') }}",
                                'values'            : [null, null]
                            },
                        }
                    }
                },

                mounted: function () {
                    EventBus.$on('updateFilter', data => {
                        EventBus.$emit('updateKanbanFilter', data);
                    });
                },

                methods: {
                    toggleSidebarFilter: function () {
                        $('.sidebar-filter').toggleClass('show');
                    },
                }
            });
        </script>
    @endpush
@endif