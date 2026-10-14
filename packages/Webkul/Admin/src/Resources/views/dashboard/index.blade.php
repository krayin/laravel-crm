<x-admin::layouts>
    <x-slot:title>
        {{ menu()->getLabel('dashboard', 'admin::app.dashboard.index.title') }}
    </x-slot>

    <!-- Head Details Section -->
    {!! view_render_event('admin.dashboard.index.header.before') !!}

    <div class="mb-5 flex items-center justify-between gap-4 max-sm:flex-wrap">
        {!! view_render_event('admin.dashboard.index.header.left.before') !!}

        <div class="grid gap-1.5">
            <p class="text-2xl font-semibold dark:text-white">
                {{ menu()->getLabel('dashboard', 'admin::app.dashboard.index.title') }}
            </p>
        </div>

        {!! view_render_event('admin.dashboard.index.header.left.after') !!}

        <!-- Actions -->
        {!! view_render_event('admin.dashboard.index.header.right.before') !!}

        <v-dashboard-filters>
            <!-- Shimmer -->
            <div class="flex gap-1.5">
                @if ($pipelines->count() > 1)
                    <div class="light-shimmer-bg dark:shimmer h-[39px] w-[160px] rounded-md"></div>
                @endif

                <div class="light-shimmer-bg dark:shimmer h-[39px] w-[140px] rounded-md"></div>
                <div class="light-shimmer-bg dark:shimmer h-[39px] w-[140px] rounded-md"></div>
                <div class="light-shimmer-bg dark:shimmer h-[39px] w-[110px] rounded-md"></div>
            </div>
        </v-dashboard-filters>

        {!! view_render_event('admin.dashboard.index.header.right.after') !!}
    </div>

    {!! view_render_event('admin.dashboard.index.header.after') !!}

    <!-- Body Component -->
    {!! view_render_event('admin.dashboard.index.content.before') !!}

    <div class="mt-3.5 flex gap-4 max-xl:flex-wrap">
        <!-- Left Section -->
        {!! view_render_event('admin.dashboard.index.content.left.before') !!}

        <div class="flex flex-1 flex-col gap-4 max-xl:flex-auto">
            <!-- Revenue Stats -->
            @include('admin::dashboard.index.revenue')

            <!-- Over All Stats -->
            @include('admin::dashboard.index.over-all')

            <!-- Total Leads Stats -->
            @include('admin::dashboard.index.total-leads')

            <div class="flex gap-4 max-lg:flex-wrap">
                <!-- Total Products -->
                @include('admin::dashboard.index.top-selling-products')

                <!-- Total Persons -->
                @include('admin::dashboard.index.top-persons')
            </div>
        </div>

        {!! view_render_event('admin.dashboard.index.content.left.after') !!}

        <!-- Right Section -->
        {!! view_render_event('admin.dashboard.index.content.right.before') !!}

        <div class="flex w-[378px] max-w-full flex-col gap-4 max-sm:w-full">
            <!-- Revenue by Types -->
            @include('admin::dashboard.index.open-leads-by-states')

            <!-- Revenue by Sources -->
            @include('admin::dashboard.index.revenue-by-sources')

            <!-- Revenue by Types -->
            @include('admin::dashboard.index.revenue-by-types')
        </div>

        {!! view_render_event('admin.dashboard.index.content.left.after') !!}
    </div>

    {!! view_render_event('admin.dashboard.index.content.after') !!}

    @pushOnce('scripts')

        <script
            type="module"
            src="{{ vite()->asset('js/chart.js') }}"
        >
        </script>

        <script
            type="module"
            src="https://cdn.jsdelivr.net/npm/chartjs-chart-funnel@4.2.1/build/index.umd.min.js"
        >
        </script>

        <script
            type="text/x-template"
            id="v-dashboard-filters-template"
        >
            {!! view_render_event('admin.dashboard.index.date_filters.before') !!}

            <div class="flex gap-1.5">
                @if ($pipelines->count() > 1)
                    <!-- Pipeline Selector -->
                    <select
                        class="custom-select flex min-h-[39px] w-[160px] rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-normal text-gray-600 transition-all hover:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400"
                        v-model="filters.pipeline_id"
                    >
                        @foreach ($pipelines as $pipeline)
                            <option value="{{ $pipeline->id }}">{{ $pipeline->name }}</option>
                        @endforeach
                    </select>
                @endif

                {{--
                    One range control in place of the previous pair of start/end inputs: picking
                    "last 30 days" or "this month" was otherwise a manual job of choosing two dates
                    and knowing today's, and neither input showed the range as a whole.
                --}}
                <x-admin::flat-picker.date-range
                    ::start-date="filters.start"
                    ::end-date="filters.end"
                    @change="applyDateRange"
                />

                <!-- Export PDF -->
                <a
                    :href="exportUrl"
                    target="_blank"
                    class="secondary-button !min-h-[39px]"
                >
                    @lang('admin::app.dashboard.index.export-pdf')
                </a>
            </div>

            {!! view_render_event('admin.dashboard.index.date_filters.after') !!}
        </script>

        <script type="module">
            app.component('v-dashboard-filters', {
                template: '#v-dashboard-filters-template',

                data() {
                    return {
                        filters: {
                            channel: '',

                            pipeline_id: "{{ $defaultPipeline?->id }}",

                            start: "{{ $startDate->format('Y-m-d') }}",

                            end: "{{ $endDate->format('Y-m-d') }}",
                        }
                    }
                },

                computed: {
                    exportUrl() {
                        let params = new URLSearchParams({
                            start: this.filters.start,
                            end: this.filters.end,
                        });

                        if (this.filters.pipeline_id) {
                            params.set('pipeline_id', this.filters.pipeline_id);
                        }

                        return "{{ route('admin.dashboard.export_pdf') }}?" + params.toString();
                    }
                },

                methods: {
                    /**
                     * The range picker reports both ends together, so they are applied in one go —
                     * setting them separately would emit an intermediate range (the new start with
                     * the old end) and send every report a query nobody asked for.
                     */
                    applyDateRange({ start, end }) {
                        this.filters.start = start;

                        this.filters.end = end;
                    },
                },

                watch: {
                    filters: {
                        handler() {
                            this.$emitter.emit('reporting-filter-updated', this.filters);
                        },

                        deep: true
                    }
                },
            });
        </script>
    @endPushOnce
</x-admin::layouts>
