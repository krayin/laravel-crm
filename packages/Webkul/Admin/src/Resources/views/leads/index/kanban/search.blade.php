{!! view_render_event('admin.leads.index.kanban.search.before') !!}

<v-kanban-search
    :is-loading="isLoading"
    :available="available"
    :applied="applied"
    @search="search"
>
</v-kanban-search>

{!! view_render_event('admin.leads.index.kanban.search.after') !!}

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-kanban-search-template"
    >
        <div class="relative flex max-w-[445px] items-center max-sm:w-full max-sm:max-w-full">
            <div class="icon-search absolute top-1.5 flex items-center text-2xl ltr:left-3 rtl:right-3"></div>

            <input
                type="text"
                name="search"
                class="block w-full rounded-lg border bg-white py-1.5 leading-6 text-gray-600 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400 ltr:pl-10 ltr:pr-3 rtl:pl-3 rtl:pr-10"
                placeholder="@lang('admin::app.leads.index.kanban.toolbar.search.title')"
                autocomplete="off"
                :value="getSearchedValues()"
                @keyup.enter="search"
            >
        </div>
    </script>

    <script type="module">
        app.component('v-kanban-search', {
            template: '#v-kanban-search-template',

            props: ['isLoading', 'available', 'applied'],

            emits: ['search'],

            data() {
                return {
                    filters: {
                        columns: [],
                    },
                };
            },

            mounted() {
                this.filters.columns = this.applied.filters.columns.filter((column) => column.index === 'all');
            },

            methods: {
                /**
                 * Perform a search operation based on the input value.
                 *
                 * @param {Event} $event
                 * @returns {void}
                 */
                search($event) {
                    let requestedValue = $event.target.value;

                    let appliedColumn = this.filters.columns.find(column => column.index === 'all');

                    if (! requestedValue) {
                        appliedColumn.value = [];

                        this.$emit('search', this.filters);

                        return;
                    }

                    if (appliedColumn) {
                        appliedColumn.value = [requestedValue];
                    } else {
                        this.filters.columns.push({
                            index: 'all',
                            value: [requestedValue]
                        });
                    }

                    this.$emit('search', this.filters);
                },

                /**
                 * Get the searched values for a specific column.
                 *
                 * @returns {Array}
                 */
                getSearchedValues() {
                    let appliedColumn = this.filters.columns.find(column => column.index === 'all');

                    return appliedColumn?.value ?? [];
                },
            },
        });
    </script>
@endPushOnce
