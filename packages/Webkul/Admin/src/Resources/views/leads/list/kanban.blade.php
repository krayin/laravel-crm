@push('css')
    <style>
        .content-container {
            overflow: hidden;
        }
    </style>
@endpush

<div class="content full-page table">
    <div class="table-header">
        <h1>
            {!! view_render_event('admin.leads.index.header.before') !!}

            {{ Breadcrumbs::render('leads') }}

            {{ __('admin::app.leads.title') }}

            {!! view_render_event('admin.leads.index.header.after') !!}
        </h1>

        <div class="table-action">
            <a href="{{ route('admin.leads.create') }}" class="btn btn-md btn-primary">{{ __('admin::app.leads.create-title') }}</a>
        </div>
    </div>

    <div class="table-body inner-section">
        <kanban-filters></kanban-filters>

        <kanban-component
            no-data-text="{{ __('admin::app.leads.no-lead') }}"
            get-url="{{ route('admin.leads.kanban.index') }}"
            detail-text="{{ __('admin::app.leads.create-title') }}"
            update-url="{{ route('admin.leads.kanban.update') }}"
            create-url="{{ route('admin.leads.create') }}"
        ></kanban-component>
    </div>
</div>

@push('scripts')
    <script type="text/x-template" id="kanban-filters-tempalte">
        <div class="form-group datagrid-filters">
            <div class="search-filter">
                <i class="icon search-icon input-search-icon"></i>

                <input
                    type="search"
                    class="control"
                    id="search-field"
                    :placeholder="__('ui.datagrid.search')"
                />
            </div>

            <sidebar-filter :columns="columns"></sidebar-filter>

            <div class="filter-right">
                <div class="switch-icons-container">
                    <a class="icon-container active">
                        <i class="icon layout-column-line-active-icon"></i>
                    </a>

                    <a href="{{ route('admin.leads.index', ['view_type' => 'table']) }}" class="icon-container">
                        <i class="icon table-line-icon"></i>
                    </a>
                </div>

                <div class="filter-btn">
                    <div class="grid-dropdown-header" @click="toggleSidebarFilter">
                        <span class="name">{{ __('ui::app.datagrid.filter.title') }}</span>

                        <i class="icon add-icon"></i>
                    </div>
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
