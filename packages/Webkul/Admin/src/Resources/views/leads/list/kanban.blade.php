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

        .content-container{
            overflow: hidden;
        }

        .full-page {
            height: 100%;
        }
    </style>
@stop

@section('post-heading')
    <kanban-filters></kanban-filters>
@stop

@section('table-action')
    <button class="btn btn-md btn-primary" id="add-new" @click="openModal('addLeadModal')">
        {{ __('admin::app.leads.add-title') }}
    </button>

    <div class="float-right">
        <a class="icon-container active">
            <i class="icon layout-column-line-active-icon"></i>
        </a>

        <a href="{{ route('admin.leads.index', ['type' => 'table']) }}" class="icon-container">
            <i class="icon table-line-icon"></i>
        </a>
    </div>
@stop

@section('table-section')
    <kanban-component
        no-data-text="{{ __('admin::app.leads.no-lead') }}"
        get-url="{{ route('admin.leads.kanban.index') }}"
        detail-text="{{ __('admin::app.leads.add-title') }}"
        update-url="{{ route('admin.leads.kanban.update') }}"
    ></kanban-component>
@stop

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