{!! view_render_event('admin.dashboard.index.open_leads_by_states.before') !!}

<!-- Total Leads Vue Component -->
<v-dashboard-open-leads-by-states>
    <!-- Shimmer -->
    <x-admin::shimmer.dashboard.index.open-leads-by-states />
</v-dashboard-open-leads-by-states>

{!! view_render_event('admin.dashboard.index.open_leads_by_states.after') !!}

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-dashboard-open-leads--by-states-template"
    >
        <!-- Shimmer -->
        <template v-if="isLoading">
            <x-admin::shimmer.dashboard.index.open-leads-by-states />
        </template>

        <!-- Total Sales Section -->
        <template v-else>
            <div class="grid gap-4 rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                <div class="flex flex-col justify-between gap-1">
                    <p class="text-base font-semibold dark:text-gray-300">
                        @lang('admin::app.dashboard.index.open-leads-by-states.title')
                    </p>
                </div>

                <!-- Doughnut Chart -->
                <div
                    class="relative flex w-full max-w-full flex-col gap-4"
                    v-if="report.statistics.length"
                >
                    <canvas
                        :id="$.uid + '_chart'"
                        class="w-full max-w-full items-end px-12"
                        :style="{ height: report.statistics.length * 60 + 'px' }"
                    ></canvas>

                    <ul class="absolute flex w-full flex-col">
                        <li
                            class="flex w-full flex-col border-b border-gray-200 pb-[9px] pt-2.5 last:border-none dark:border-gray-800"
                            v-for="(stat, index) in report.statistics"
                        >
                            <span class="text-sm font-semibold dark:text-gray-100">
                                @{{ stat.total }}
                            </span>

                            <span class="text-sm font-semibold dark:text-gray-100">
                                @{{ stat.name }}
                            </span>
                        </li>
                    </ul>
                </div>

                <!-- Empty Product Design -->
                <div
                    class="flex flex-col gap-8 p-4"
                    v-else
                >
                    <div class="grid justify-center justify-items-center gap-3.5 py-2.5">
                        <!-- Placeholder Image -->
                        <img
                            src="{{ vite()->asset('images/empty-placeholders/default.svg') }}"
                            class="dark:mix-blend-exclusion dark:invert"
                        >

                        <!-- Add Variants Information -->
                        <div class="flex flex-col items-center">
                            <p class="text-base font-semibold text-gray-400">
                                @lang('admin::app.dashboard.index.open-leads-by-states.empty-title')
                            </p>

                            <p class="text-gray-400">
                                @lang('admin::app.dashboard.index.open-leads-by-states.empty-info')
                            </p>
                        </div>
                    </div>
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

                    if (this.report.statistics.length === 0) {
                        return;
                    }

                    const ctx = document.getElementById(this.$.uid + '_chart')?.getContext('2d');

                    // Create gradient
                    const gradient = ctx.createLinearGradient(0, 0, 0, 400);
                    gradient.addColorStop(0, 'rgba(144, 247, 236, 0.8)');
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
