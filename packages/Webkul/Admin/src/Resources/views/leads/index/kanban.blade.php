{!! view_render_event('admin.leads.index.kanban.before') !!}

<!-- Kanabn Vue Component -->
<v-leads-kanban>
    <div class="flex flex-col gap-4">
        <!-- Shimmer -->
        <x-admin::shimmer.leads.index.kanban />
    </div>
</v-leads-kanban>

{!! view_render_event('admin.leads.index.kanban.after') !!}

@pushOnce('scripts')
    <script type="text/x-template" id="v-leads-kanban-template">
        <template v-if="isLoading">
            <div class="flex flex-col gap-4">
                <x-admin::shimmer.leads.index.kanban />
            </div>
        </template>

        <template v-else>
            <div class="flex flex-col gap-4">
                @include('admin::leads.index.kanban.toolbar')

                {!! view_render_event('admin.leads.index.kanban.content.before') !!}

                <div class="flex gap-4 overflow-x-auto">
                    <div
                        class="flex min-w-[275px] max-w-[275px] flex-col gap-1 rounded-lg bg-white"
                        v-for="(stage, index) in stageLeads"
                    >
                        <!-- Stage Header -->
                        <div class="flex flex-col px-2 py-3">
                            <!-- Stage Title and Action -->
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-medium">
                                    @{{ stage.name }} (@{{ stage.leads.meta.total }})
                                </span>

                                @if (bouncer()->hasPermission('leads.create'))
                                    <a
                                        :href="'{{ route('admin.leads.create') }}' + '?stage_id=' + stage.id"
                                        class="icon-add cursor-pointer rounded p-1 text-lg text-slate-600 transition-all hover:bg-slate-100 hover:text-slate-800"
                                        target="_blank"
                                    >
                                    </a>
                                @endif
                            </div>

                            <!-- Stage Total Leads and Amount -->
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-xs">
                                    @{{ $admin.formatPrice(stage.lead_value) }}
                                </span>

                                <!-- Progress Bar -->
                                <div class="h-1 w-36 overflow-hidden rounded-full bg-slate-200">
                                    <div
                                        class="h-1 bg-green-500"
                                        :style="{ width: (stage.lead_value / totalStagesAmount) * 100 + '%' }"
                                    ></div>
                                </div>
                            </div>
                        </div>

                        <!-- Draggable Stage Lead Cards -->
                        <draggable
                            class="flex h-[calc(100vh-315px)] flex-col gap-2 overflow-y-auto p-2"
                            ghost-class="draggable-ghost"
                            handle=".lead-item"
                            v-bind="{animation: 200}"
                            :list="stage.leads.data"
                            item-key="id"
                            group="leads"
                            @scroll="handleScroll(stage, $event)"
                            @change="updateStage(stage, $event)"
                        >
                            <!-- Lead Card -->
                            <template #item="{ element, index }">
                                <a
                                    class="lead-item flex cursor-grab flex-col gap-5 rounded-md border border-gray-50 bg-gray-50 p-2"
                                    :href="'{{ route('admin.leads.view', 'replaceId') }}'.replace('replaceId', element.id)"
                                >
                                    <!-- Header -->
                                    <div class="flex items-start justify-between">
                                        <div class="flex items-center gap-1">
                                            <div
                                                class="flex h-9 w-9 items-center justify-center rounded-full text-xs font-medium"
                                                :class="backgroundColors[Math.floor(Math.random() * backgroundColors.length)]"
                                            >
                                                @{{ element.person.name.split(' ').map(word => word[0].toUpperCase()).join('') }}
                                            </div>

                                            <div class="flex flex-col gap-1">
                                                <span class="text-xs font-medium">
                                                    @{{ element.person.name }}
                                                </span>

                                                <span class="text-[10px]">
                                                    @{{ element.person.organization?.name }}
                                                </span>
                                            </div>
                                        </div>

                                        <span
                                            class="icon-rotten cursor-default text-xl text-rose-600"
                                            v-if="element.rotten_days > 0"
                                        ></span>
                                    </div>

                                    <!-- Lead Title -->
                                    <p class="text-xs font-medium">
                                        @{{ element.title }}
                                    </p>

                                    <div class="flex flex-wrap gap-1">
                                        <!-- Tags -->
                                        <template v-for="tag in element.tags">
                                            <div
                                                class="rounded-xl bg-slate-200 px-3 py-1 text-xs font-medium"
                                                :style="{
                                                    backgroundColor: tag.color,
                                                    color: tagTextColor[tag.color]
                                                }"
                                            >
                                                @{{ tag.name }}
                                            </div>
                                        </template>

                                        <div class="rounded-xl bg-slate-200 px-3 py-1 text-xs font-medium">
                                            @{{ element.formatted_lead_value }}
                                        </div>

                                        <div class="rounded-xl bg-slate-200 px-3 py-1 text-xs font-medium">
                                            @{{ element.source.name }}
                                        </div>

                                        <div class="rounded-xl bg-slate-200 px-3 py-1 text-xs font-medium">
                                            @{{ element.type.name }}
                                        </div>
                                    </div>
                                </a>
                            </template>
                        </draggable>
                    </div>
                </div>

                {!! view_render_event('admin.leads.index.kanban.content.after') !!}
            </div>
        </template>
    </script>

    <script type="module">
        app.component('v-leads-kanban', {
            template: '#v-leads-kanban-template',

            data: function () {
                return {
                    available: {
                        columns: [
                            {
                                "index": "id",
                                "label": "ID",
                                "type": "integer",
                                "searchable": false,
                                "search_field": "in",
                                "filterable": true,
                                "filterable_type": null,
                                "filterable_options": [],
                                "allow_multiple_values": true,
                                "sortable": true,
                                "visibility": true
                            },
                            {
                                "index": "title",
                                "label": "Subject",
                                "type": "string",
                                "searchable": true,
                                "search_field": "in",
                                "filterable": true,
                                "filterable_type": null,
                                "filterable_options": [],
                                "allow_multiple_values": true,
                                "sortable": true,
                                "visibility": true
                            },
                        ],
                    },

                    applied: {
                        filters: {
                            columns: [],
                        }
                    },

                    stages: @json($pipeline->stages->toArray()),

                    stageLeads: {},

                    isLoading: true,

                    backgroundColors: [
                        'bg-yellow-200',
                        'bg-red-200',
                        'bg-lime-200',
                        'bg-blue-200',
                        'bg-orange-200',
                        'bg-green-200',
                        'bg-pink-200',
                        'bg-yellow-400'
                    ],

                    tagTextColor: {
                        '#FEE2E2': '#DC2626',
                        '#FFEDD5': '#EA580C',
                        '#FEF3C7': '#D97706',
                        '#FEF9C3': '#CA8A04',
                        '#ECFCCB': '#65A30D',
                        '#DCFCE7': '#16A34A',
                    },
                };
            },

            computed: {
                totalStagesAmount() {
                    let totalAmount = 0;

                    for (let [key, stage] of Object.entries(this.stageLeads)) {
                        totalAmount += parseFloat(stage.lead_value);
                    }

                    return totalAmount;
                }
            },

            mounted: function () {
                this.get()
                    .then(response => {
                        this.isLoading = false;

                        for (let [stageId, data] of Object.entries(response.data)) {
                            this.stageLeads[stageId] = data;
                        }
                    });
            },

            methods: {
                get(params = {}) {
                    let search = '';
                    let searchFields = '';

                    this.applied.filters.columns.forEach((column) => {
                        if (column.index === 'all') {
                            return;
                        }

                        search += `${column.index}:${column.value.join(',')};`;
                        searchFields += `${column.index}:${column.search_field};`;
                    });

                    return this.$axios
                        .get("{{ route('admin.leads.get') }}", {
                            params: {
                                search,
                                searchFields,
                                pipeline_id: "{{ request('pipeline_id') }}",
                                limit: 10,

                                ...params,
                            }
                        })
                        .catch(error => {
                            console.log(error)
                        });
                },

                filter(filters) {
                    this.applied.filters.columns = [
                        ...(this.applied.filters.columns.filter((column) => column.index === 'all')),
                        ...filters.columns,
                    ];

                    this.get()
                        .then(response => {
                            this.isLoading = false;

                            for (let [stageId, data] of Object.entries(response.data)) {
                                this.stageLeads[stageId] = data;
                            }
                        });
                },

                search(filters) {
                    this.applied.filters.columns = [
                        ...(this.applied.filters.columns.filter((column) => column.index !== 'all')),
                        ...filters.columns,
                    ];

                    this.get()
                        .then(response => {
                            this.isLoading = false;

                            for (let [stageId, data] of Object.entries(response.data)) {
                                this.stageLeads[stageId] = data;
                            }
                        });
                },

                append(params) {
                    this.get(params)
                        .then(response => {
                            this.isLoading = false;

                            for (let [stageId, data] of Object.entries(response.data)) {
                                if (! this.stageLeads[stageId]) {
                                    this.stageLeads[stageId] = data;
                                } else {
                                    this.stageLeads[stageId].leads.data = this.stageLeads[stageId].leads.data.concat(data.leads.data);

                                    this.stageLeads[stageId].leads.meta = data.leads.meta;
                                }
                            }
                        });
                },

                updateStage: function (stage, event) {
                    if (event.removed) {
                        stage.lead_value = parseFloat(stage.lead_value) - parseFloat(event.removed.element.lead_value);

                        this.stageLeads[stage.id].leads.meta.total = this.stageLeads[stage.id].leads.meta.total - 1;

                        return;
                    }

                    stage.lead_value = parseFloat(stage.lead_value) + parseFloat(event.added.element.lead_value);

                    this.stageLeads[stage.id].leads.meta.total = this.stageLeads[stage.id].leads.meta.total + 1;

                    this.$axios
                        .put("{{ route('admin.leads.update', 'replace') }}".replace('replace', event.added.element.id), {
                            'lead_pipeline_stage_id': stage.id
                        })
                        .then(response => {
                            this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });
                        })
                        .catch(error => {
                            this.$emitter.emit('add-flash', { type: 'error', message: error.response.data.message });
                        });
                },

                handleScroll(stage, event) {
                    const bottom = event.target.scrollHeight - event.target.scrollTop === event.target.clientHeight

                    if (! bottom) {
                        return;
                    }

                    if (this.stageLeads[stage.id].leads.meta.current_page == this.stageLeads[stage.id].leads.meta.last_page) {
                        return;
                    }

                    this.append({
                        pipeline_stage_id: stage.id,
                        pipeline_id: stage.lead_pipeline_id,
                        page: this.stageLeads[stage.id].leads.meta.current_page + 1,
                        limit: 10,
                    });
                },
            }
        });
    </script>
@endPushOnce
