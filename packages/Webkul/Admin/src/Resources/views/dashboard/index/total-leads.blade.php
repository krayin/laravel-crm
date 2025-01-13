{!! view_render_event('admin.dashboard.index.total_leads.before') !!}

<!-- Total Leads Vue Component -->
<v-dashboard-total-leads>
    <!-- Shimmer -->
    <x-admin::shimmer.dashboard.index.total-leads />
</v-dashboard-total-leads>

{!! view_render_event('admin.dashboard.index.total_leads.after') !!}

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-dashboard-total-leads-template"
    >
        <!-- Shimmer -->
        <template v-if="isLoading">
            <x-admin::shimmer.dashboard.index.total-leads />
        </template>

        <!-- Total Sales Section -->
        <template v-else>
            <div class="grid gap-4 rounded-lg border border-gray-200 bg-white px-4 py-2 dark:border-gray-800 dark:bg-gray-900">
                <div class="flex flex-col justify-between gap-1">
                    <p class="text-base font-semibold dark:text-gray-300">
                        @lang('admin::app.dashboard.index.total-leads.title')
                    </p>
                </div>

                <!-- Bar Chart -->
                <div class="flex w-full max-w-full flex-col gap-4">
                    <x-admin::charts.bar
                        ::labels="chartLabels"
                        ::datasets="chartDatasets"
                    />

                    <div class="flex justify-center gap-5">
                        <div class="flex items-center gap-2">
                            <span class="h-3.5 w-3.5 rounded-sm bg-[#8979FF]"></span>
                            
                            <p class="text-xs dark:text-gray-300">
                                @lang('admin::app.dashboard.index.total-leads.total')
                            </p>
                        </div>
                        
                        <div class="flex items-center gap-2">
                            <span class="h-3.5 w-3.5 rounded-sm bg-[#63CFE5]"></span>
                            
                            <p class="text-xs dark:text-gray-300">
                                @lang('admin::app.dashboard.index.total-leads.won')
                            </p>
                        </div>
                        
                        <div class="flex items-center gap-2">
                            <span class="h-3.5 w-3.5 rounded-sm bg-[#FFA8A1]"></span>
                            
                            <p class="text-xs dark:text-gray-300">
                                @lang('admin::app.dashboard.index.total-leads.lost')
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </script>

    <script type="module">
        app.component('v-dashboard-total-leads', {
            template: '#v-dashboard-total-leads-template',

            data() {
                return {
                    report: [],

                    isLoading: true,
                }
            },

            computed: {
                chartLabels() {
                    return this.report.statistics.all.over_time.map(({ label }) => label);
                },

                chartDatasets() {
                    return [{
                        data: this.report.statistics.all.over_time.map(({ count }) => count),
                        barThickness: 24,
                        backgroundColor: '#8979FF',
                    },{
                        data: this.report.statistics.won.over_time.map(({ count }) => count),
                        barThickness: 24,
                        backgroundColor: '#63CFE5',
                    },{
                        data: this.report.statistics.lost.over_time.map(({ count }) => count),
                        barThickness: 24,
                        backgroundColor: '#FFA8A1',
                    }];
                }
            },

            mounted() {
                this.getStats({});

                this.$emitter.on('reporting-filter-updated', this.getStats);
            },

            methods: {
                getStats(filtets) {
                    this.isLoading = true;

                    var filtets = Object.assign({}, filtets);

                    filtets.type = 'total-leads';

                    this.$axios.get("{{ route('admin.dashboard.stats') }}", {
                            params: filtets
                        })
                        .then(response => {
                            this.report = response.data;

                            this.isLoading = false;
                        })
                        .catch(error => {});
                }
            }
        });
    </script>
@endPushOnce