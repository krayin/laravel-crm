{!! view_render_event('admin.leads.index.kanban.before') !!}

<!-- Kanabn Vue Component -->
<v-leads-kanban>
    <div class="flex flex-col gap-4">
        <x-admin::shimmer.leads.kanban />
    </div>
</v-leads-kanban>

{!! view_render_event('admin.leads.index.kanban.after') !!}

@pushOnce('scripts')
    <script type="text/x-template" id="v-leads-kanban-tempalte">
        <tempalte v-if="isLoading">
            <div class="flex flex-col gap-4">
                <x-admin::shimmer.leads.kanban />
            </div>
        </tempalte>

        <template v-else>
            <div class="flex flex-col gap-4">
                @include('admin::leads.index.kanban.toolbar')

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

                                <span class="icon-add cursor-pointer rounded p-1 text-lg text-slate-600 transition-all hover:bg-slate-100 hover:text-slate-800"></span>
                            </div>

                            <!-- Stage Total Leads and Amount -->
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-xs">
                                    @{{ $admin.formatPrice(stage.lead_value) }}
                                </span>

                                <!-- Progress Bar -->
                                <div class="h-1 w-36 overflow-hidden rounded-full bg-slate-200">
                                    <div class="h-1 bg-green-500" style="width: 35%"></div>
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
                            @change="updateLeadStage(stage, $event)"
                        >
                            <!-- Lead Card -->
                            <template #item="{ element, index }">
                                <div class="lead-item flex cursor-pointer flex-col gap-5 rounded-md border border-gray-50 bg-gray-50 p-2">
                                    <!-- Header -->
                                    <div class="flex items-start justify-between">
                                        <div class="flex items-center gap-1">
                                            <div
                                                class="flex h-9 w-9 items-center justify-center rounded-full text-xs font-medium"
                                                :style="{ 'background-color': colors[Math.floor(Math.random() * colors.length)] }"
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

                                        <div class="flex items-center gap-2">
                                            <span class="icon-notification text-lg"></span>
                                            
                                            <span class="icon-bookmark text-lg"></span>
                                        </div>
                                    </div>
                                    
                                    <!-- Lead Title -->
                                    <p class="text-xs font-medium">
                                        @{{ element.title }}
                                    </p>

                                    <div class="flex flex-wrap gap-1">
                                        <div class="rounded-xl bg-rose-100 px-3 py-1 text-xs font-medium text-rose-700">Urgent</div>

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
                                </div>
                            </template>
                        </draggable>
                    </div>
                </div>
            </div>
        </template>
    </script>

    <script type="module">
        app.component('v-leads-kanban', {
            template: '#v-leads-kanban-tempalte',

            data: function () {
                return {
                    stages: @json($pipeline->stages->toArray()),

                    stageLeads: {},

                    isLoading: true,

                    colors: [
                        '#FEF08A',
                        '#FECACA',
                        '#D9F99D',
                        '#BFDBFE',
                        '#FED7AA',
                        '#86EFAC',
                        '#FBCFE8',
                        '#FACC15'
                    ],
                }
            },

            mounted: function () {
                this.getLeads({
                    limit: 10,
                    pileline_id: "{{ request('pipeline_id') }}"
                });
            },

            methods: {
                getLeads(params) {
                    var self = this;
                    
                    this.$axios.get("{{ route('admin.leads.get') }}", {
                            params: params
                        })
                        .then(response => {
                            self.isLoading = false;
                            
                            for (let [stageId, data] of Object.entries(response.data)) {
                                if (! self.stageLeads[stageId]) {
                                    self.stageLeads[stageId] = data;
                                } else {
                                    self.stageLeads[stageId].leads.data = self.stageLeads[stageId].leads.data.concat(data.leads.data);
                                    
                                    self.stageLeads[stageId].leads.meta = data.leads.meta;
                                }
                            }
                        })
                        .catch(error => {
                            console.log(error)
                        });
                },

                updateLeadStage: function (stage, event) {
                    if (event.removed) {
                        stage.lead_value = parseFloat(stage.lead_value) - parseFloat(event.removed.element.lead_value);

                        this.stageLeads[stage.id].leads.meta.total = this.stageLeads[stage.id].leads.meta.total - 1;

                        return;
                    }

                    stage.lead_value = parseFloat(stage.lead_value) + parseFloat(event.added.element.lead_value);

                    this.stageLeads[stage.id].leads.meta.total = this.stageLeads[stage.id].leads.meta.total + 1;

                    this.$axios.put("{{ route('admin.leads.update', 'replace') }}".replace('replace', event.added.element.id), {
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

                    this.getLeads({
                        pipeline_stage_id: stage.id,
                        pipeline_id: stage.lead_pipeline_id,
                        page: this.stageLeads[stage.id].leads.meta.current_page + 1,
                        limit: 10,
                    });
                }
            }
        });
    </script>
@endPushOnce
