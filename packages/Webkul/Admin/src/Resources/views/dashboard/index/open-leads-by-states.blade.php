<!-- Total Leads Vue Component -->
<v-dashboard-open-leads-by-states>
    <!-- Shimmer -->
</v-dashboard-open-leads-by-states>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-dashboard-open-leads--by-states-template"
    >
        <!-- Shimmer -->
        <template v-if="isLoading">
        </template>

        <!-- Total Sales Section -->
        <template v-else>
            <div class="grid gap-4 rounded-lg border border-gray-200 bg-white p-4">
                <div class="flex flex-col justify-between gap-1">
                    <p class="text-base font-semibold">
                        @lang('admin::app.dashboard.index.open-leads-by-states.title')
                    </p>
                </div>

                <!-- Doughnut Chart -->
                <div class="relative flex w-full max-w-full flex-col gap-4">
                    <canvas
                        :id="$.uid + '_chart'"
                        class="w-full max-w-full items-end px-12"
                        :style="{ height: report.statistics.length * 60 + 'px' }"
                    ></canvas>

                    <ul class="absolute flex w-full flex-col">
                        <li
                            class="flex w-full flex-col border-b border-gray-200 pb-[9px] pt-2.5 last:border-none"
                            v-for="(stat, index) in report.statistics"
                        >
                            <span class="text-sm font-semibold">
                                @{{ stat.total }}
                            </span>

                            <span class="text-sm font-semibold">
                                @{{ stat.name }}
                            </span>
                        </li>
                    </ul>
                </div>
            </div>
        </template>
    </script>


    <script type="module">
        app.component('v-dashboard-open-leads-by-states', {
            template: '#v-dashboard-open-leads--by-states-template',

            data() {
                return {
                    report: [],

                    isLoading: true,
                    
                    chart: undefined,
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

                    filtets.type = 'open-leads-by-states';

                    this.$axios.get("{{ route('admin.dashboard.stats') }}", {
                            params: filtets
                        })
                        .then(response => {
                            this.report = response.data;

                            this.isLoading = false;

                            setTimeout(() => {
                                this.prepare();
                            }, 0);
                        })
                        .catch(error => {});
                },
                
                prepare() {
                    if (this.chart) {
                        this.chart.destroy();
                    }

                    const ctx = document.getElementById(this.$.uid + '_chart').getContext('2d');

                    // Create gradient
                    const gradient = ctx.createLinearGradient(0, 0, 0, 400);
                    gradient.addColorStop(0, 'rgba(144, 247, 236, 1)');
                    gradient.addColorStop(1, 'rgba(50, 204, 188, 1)');

                    this.chart = new Chart(ctx, {
                        type: 'funnel',
                        
                        data: {
                            labels: this.report.statistics.map(stat => stat.name),
                            datasets: [
                                {
                                    data: this.report.statistics.map(stat => stat.total),
                                    backgroundColor: gradient,
                                    borderColor: 'rgba(0, 0, 0, 0)',
                                    borderWidth: 0,
                                },
                            ],
                        },

                        options: {
                            indexAxis: 'y',
                        },
                    });
                }
            }
        });
    </script>
@endPushOnce