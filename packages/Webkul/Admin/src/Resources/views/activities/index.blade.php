<x-admin::layouts>
    <x-slot:title>
        @lang('admin::app.activities.index.title')
    </x-slot>

    {!! view_render_event('admin.activities.index.activities.before') !!}

    <!-- Activities Datagrid -->
    <v-activities>
        <div class="flex flex-col gap-4">
            <div class="scroll-reactive-sticky sticky top-[60px] z-[1000] flex items-center justify-between rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm shadow-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                <div class="flex flex-col gap-2">
                    <x-admin::breadcrumbs name="activities" />

                    <div class="text-xl font-bold dark:text-white">
                        @lang('admin::app.activities.index.title')
                    </div>
                </div>

                <div class="flex gap-2">
                    <i class="icon-list cursor-pointer rounded p-2 text-2xl"></i>

                    <i class="icon-calendar cursor-pointe rounded p-2 text-2xl"></i>
                </div>
            </div>

            <!-- DataGrid Shimmer -->
            @if (
                request()->get('view-type') == 'table'
                || ! request()->has('view-type')
            )
                <x-admin::shimmer.datagrid :is-multi-row="true"/>
            @endif
        </div>
    </v-activities>

    {!! view_render_event('admin.activities.index.activities.after') !!}

    @pushOnce('scripts')
        <script
            type="text/x-template"
            id="v-activities-template"
        >
            <div class="flex flex-col gap-4">
                <div class="scroll-reactive-sticky sticky top-[60px] z-[1000] flex items-center justify-between rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm shadow-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                    <div class="flex flex-col gap-2">
                        <x-admin::breadcrumbs name="activities" />

                        <div class="text-xl font-bold dark:text-white">
                            @lang('admin::app.activities.index.title')
                        </div>
                    </div>

                    {!! view_render_event('admin.activities.index.toggle_view.before') !!}

                    <div class="flex">
                        <i
                            class="icon-list cursor-pointer rounded-md p-2 text-2xl"
                            :class="{'bg-gray-200 dark:bg-gray-800 text-gray-800 dark:text-white': viewType == 'table'}"
                            @click="toggleView('table')"
                        ></i>

                        <i
                            class="icon-calendar cursor-pointer rounded-md p-2 text-2xl"
                            :class="{'bg-gray-200 dark:bg-gray-800 text-gray-800 dark:text-white': viewType == 'calendar'}"
                            @click="toggleView('calendar')"
                        ></i>
                    </div>

                    {!! view_render_event('admin.activities.index.toggle_view.after') !!}
                </div>

                <!-- DataGrid Shimmer -->
                <div>
                    <template v-if="viewType == 'table'">
                        {!! view_render_event('admin.activities.index.datagrid.before') !!}

                        <x-admin::datagrid
                            src="{{ route('admin.activities.get') }}"
                            :isMultiRow="true"
                            ref="datagrid"
                        >
                            <template #header="{
                                isLoading,
                                available,
                                applied,
                                selectAll,
                                sort,
                                performAction
                            }">
                                <template v-if="isLoading">
                                    <x-admin::shimmer.datagrid.table.head :isMultiRow="true" />
                                </template>

                                <template v-else>
                                    <div class="row grid grid-cols-[.3fr_.1fr_.3fr_.5fr] grid-rows-1 items-center gap-x-2.5 border-b px-4 py-2.5 dark:border-gray-800 max-lg:hidden">
                                        <div
                                            class="flex select-none items-center gap-2.5"
                                            v-for="(columnGroup, index) in [['id', 'title', 'created_by_id'], ['is_done'], ['comment', 'lead_title', 'type'], ['schedule_from', 'schedule_to', 'created_at']]"
                                        >
                                            <label
                                                class="flex w-max cursor-pointer select-none items-center gap-1"
                                                for="mass_action_select_all_records"
                                                v-if="! index"
                                            >
                                                <input
                                                    type="checkbox"
                                                    name="mass_action_select_all_records"
                                                    id="mass_action_select_all_records"
                                                    class="peer hidden"
                                                    :checked="['all', 'partial'].includes(applied.massActions.meta.mode)"
                                                    @change="selectAll"
                                                >

                                                <span
                                                    class="icon-checkbox-outline cursor-pointer rounded-md text-2xl text-gray-600 dark:text-gray-300"
                                                    :class="[
                                                        applied.massActions.meta.mode === 'all' ? 'peer-checked:icon-checkbox-select peer-checked:text-brandColor' : (
                                                            applied.massActions.meta.mode === 'partial' ? 'peer-checked:icon-checkbox-multiple peer-checked:text-brandColor' : ''
                                                        ),
                                                    ]"
                                                >
                                                </span>
                                            </label>

                                            <p class="text-gray-600 dark:text-gray-300">
                                                <span class="[&>*]:after:content-['_/_']">
                                                    <template v-for="column in columnGroup">
                                                        <span
                                                            class="after:content-['/'] last:after:content-['']"
                                                            :class="{
                                                                'font-medium text-gray-800 dark:text-white': applied.sort.column == column,
                                                                'cursor-pointer hover:text-gray-800 dark:hover:text-white': available.columns.find(columnTemp => columnTemp.index === column)?.sortable,
                                                            }"
                                                            @click="
                                                                available.columns.find(columnTemp => columnTemp.index === column)?.sortable ? sort(available.columns.find(columnTemp => columnTemp.index === column)): {}
                                                            "
                                                        >
                                                            @{{ available.columns.find(columnTemp => columnTemp.index === column)?.label }}
                                                        </span>
                                                    </template>
                                                </span>

                                                <i
                                                    class="align-text-bottom text-base text-gray-800 dark:text-white ltr:ml-1.5 rtl:mr-1.5"
                                                    :class="[applied.sort.order === 'asc' ? 'icon-stats-down': 'icon-stats-up']"
                                                    v-if="columnGroup.includes(applied.sort.column)"
                                                ></i>
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Mobile Sort/Filter Header -->
                                    <div class="hidden border-b bg-gray-50 px-4 py-3 text-black dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 max-lg:block">
                                        <div class="flex items-center justify-between">
                                            <!-- Mass Actions for Mobile -->
                                            <div v-if="available.massActions.length">
                                                <label
                                                    class="flex w-max cursor-pointer select-none items-center gap-1"
                                                    for="mass_action_select_all_records"
                                                >
                                                    <input
                                                        type="checkbox"
                                                        name="mass_action_select_all_records"
                                                        id="mass_action_select_all_records"
                                                        class="peer hidden"
                                                        :checked="['all', 'partial'].includes(applied.massActions.meta.mode)"
                                                        @change="selectAll"
                                                    >

                                                    <span
                                                        class="icon-checkbox-outline cursor-pointer rounded-md text-2xl text-gray-600 dark:text-gray-300"
                                                        :class="[
                                                            applied.massActions.meta.mode === 'all' ? 'peer-checked:icon-checkbox-select peer-checked:text-brandColor' : (
                                                                applied.massActions.meta.mode === 'partial' ? 'peer-checked:icon-checkbox-multiple peer-checked:text-brandColor' : ''
                                                            ),
                                                        ]"
                                                    >
                                                    </span>
                                                </label>
                                            </div>

                                            <!-- Mobile Sort Dropdown -->
                                            <div v-if="available.columns.some(column => column.sortable)">
                                                <x-admin::dropdown position="bottom-{{ in_array(app()->getLocale(), ['fa', 'ar']) ? 'left' : 'right' }}">
                                                    <x-slot:toggle>
                                                        <div class="flex items-center gap-1">
                                                            <button
                                                                type="button"
                                                                class="inline-flex w-full max-w-max cursor-pointer appearance-none items-center justify-between gap-x-2 rounded-md border bg-white px-2.5 py-1.5 text-center leading-6 text-gray-600 transition-all marker:shadow hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400"
                                                            >
                                                                <span>
                                                                    Sort
                                                                </span>

                                                                <span class="icon-down-arrow text-2xl"></span>
                                                            </button>
                                                        </div>
                                                    </x-slot>

                                                    <x-slot:menu>
                                                        <x-admin::dropdown.menu.item
                                                            v-for="column in available.columns.filter(column => column.sortable && column.visibility)"
                                                            @click="sort(column)"
                                                        >
                                                            <div class="flex items-center gap-2">
                                                                <span v-html="column.label"></span>
                                                                <i
                                                                    class="align-text-bottom text-base text-gray-600 dark:text-gray-300"
                                                                    :class="[applied.sort.order === 'asc' ? 'icon-stats-down': 'icon-stats-up']"
                                                                    v-if="column.index == applied.sort.column"
                                                                ></i>
                                                            </div>
                                                        </x-admin::dropdown.menu.item>
                                                    </x-slot>
                                                </x-admin::dropdown>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </template>

                            <template #body="{
                                isLoading,
                                available,
                                applied,
                                selectAll,
                                sort,
                                performAction
                            }">
                                <template v-if="isLoading">
                                    <x-admin::shimmer.datagrid.table.body :isMultiRow="true" />
                                </template>

                                <template v-else>
                                    <div
                                        class="row grid grid-cols-[.3fr_.1fr_.3fr_.5fr] grid-rows-1 gap-x-2.5 border-b px-4 py-2.5 transition-all hover:bg-gray-50 dark:border-gray-800 dark:hover:bg-gray-950 max-lg:hidden"
                                        v-for="record in available.records"
                                    >
                                        <!-- Mass Actions, Title and Created By -->
                                        <div class="flex gap-2.5">
                                            <input
                                                type="checkbox"
                                                :name="`mass_action_select_record_${record.id}`"
                                                :id="`mass_action_select_record_${record.id}`"
                                                :value="record.id"
                                                class="peer hidden"
                                                v-model="applied.massActions.indices"
                                            >

                                            <label
                                                class="icon-checkbox-outline peer-checked:icon-checkbox-select cursor-pointer rounded-md text-2xl text-gray-600 peer-checked:text-brandColor dark:text-gray-300"
                                                :for="`mass_action_select_record_${record.id}`"
                                            ></label>

                                            <div class="flex flex-col gap-1.5">
                                                <p class="text-gray-600 dark:text-gray-300">
                                                    @{{ record.id }}
                                                </p>

                                                <p class="text-gray-600 dark:text-gray-300">
                                                    @{{ record.title }}
                                                </p>

                                                <p
                                                    class="text-gray-600 dark:text-gray-300"
                                                    v-html="record.created_by_id"
                                                >
                                                </p>
                                            </div>
                                        </div>

                                        <!-- Is Done -->
                                        <div class="flex gap-1.5">
                                            <div class="flex flex-col gap-1.5">
                                                <p
                                                    class="text-gray-600 dark:text-gray-300"
                                                    v-html="record.is_done"
                                                >
                                                </p>
                                            </div>
                                        </div>

                                        <!-- Comment, Lead Title and Type -->
                                        <div class="flex gap-1.5">
                                            <div class="flex flex-col gap-1.5">
                                                <p class="text-gray-600 dark:text-gray-300">
                                                    {{-- @{{ record.comment }} --}}
                                                    @{{ (record.comment || '').length > 180 ? (record.comment || '').slice(0, 180) + '...' : (record.comment || '') }}
                                                </p>

                                                <p v-html="record.lead_title"></p>

                                                <p class="text-gray-600 dark:text-gray-300">
                                                    @{{ record.type ?? 'N/A'}}
                                                </p>
                                            </div>
                                        </div>

                                        <div class="flex items-start justify-between gap-x-4">
                                            <div class="flex flex-col gap-1.5">
                                                <p class="text-gray-600 dark:text-gray-300">
                                                    @{{ record.schedule_from ?? 'N/A' }}
                                                </p>

                                                <p class="text-gray-600 dark:text-gray-300">
                                                    @{{ record.schedule_to }}
                                                </p>

                                                <p class="text-gray-600 dark:text-gray-300">
                                                    @{{ record.created_at }}
                                                </p>
                                            </div>

                                            <div class="flex items-center gap-1.5">
                                                <p
                                                    class="place-self-end"
                                                    v-if="available.actions.length"
                                                >
                                                    <span
                                                        class="cursor-pointer rounded-md p-1.5 text-2xl transition-all hover:bg-gray-200 dark:hover:bg-gray-800 max-sm:place-self-center"
                                                        :class="action.icon"
                                                        v-text="! action.icon ? action.title : ''"
                                                        v-for="action in record.actions"
                                                        @click="performAction(action)"
                                                    ></span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Mobile Card View -->
                                    <div
                                        class="hidden border-b px-4 py-4 text-black dark:border-gray-800 dark:text-gray-300 max-lg:block"
                                        v-for="record in available.records"
                                    >
                                        <div class="mb-2 flex items-center justify-between">
                                            <!-- Mass Actions for Mobile Cards -->
                                            <div class="flex w-full items-center justify-between gap-2">
                                                <p v-if="available.massActions.length">
                                                    <label :for="`mass_action_select_record_${record[available.meta.primary_column]}`">
                                                        <input
                                                            type="checkbox"
                                                            :name="`mass_action_select_record_${record[available.meta.primary_column]}`"
                                                            :value="record[available.meta.primary_column]"
                                                            :id="`mass_action_select_record_${record[available.meta.primary_column]}`"
                                                            class="peer hidden"
                                                            v-model="applied.massActions.indices"
                                                        >

                                                        <span class="icon-checkbox-outline peer-checked:icon-checkbox-select cursor-pointer rounded-md text-2xl text-gray-500 peer-checked:text-brandColor">
                                                        </span>
                                                    </label>
                                                </p>

                                                <!-- Actions for Mobile -->
                                                <div
                                                    class="flex w-full items-center justify-end"
                                                    v-if="available.actions.length"
                                                >
                                                    <span
                                                        class="dark:hover:bg-gray-80 cursor-pointer rounded-md p-1.5 text-2xl transition-all hover:bg-gray-200"
                                                        :class="action.icon"
                                                        v-text="! action.icon ? action.title : ''"
                                                        v-for="action in record.actions"
                                                        @click="performAction(action)"
                                                    >
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Card Content -->
                                        <div class="grid gap-2">
                                            <template v-for="column in available.columns">
                                                <div class="flex flex-wrap items-baseline gap-x-2">
                                                    <span class="text-slate-600 dark:text-gray-300" v-html="column.label + ':'"></span>
                                                    <span class="break-words font-medium text-slate-900 dark:text-white" v-html="record[column.index]"></span>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </template>
                        </x-admin::datagrid>

                        {!! view_render_event('admin.activities.index.datagrid.after') !!}
                    </template>

                    <template v-else>
                        {!! view_render_event('admin.activities.index.vue_calender.before') !!}

                        <v-calendar></v-calendar>

                        {!! view_render_event('admin.activities.index.vue_calender.after') !!}
                    </template>
                </div>
            </div>
        </script>

        <script
            type="text/x-template"
            id="v-calendar-template"
        >
            <div class="relative">
                <v-vue-cal
                    ref="calendar"
                    hide-view-selector
                    :watchRealTime="true"
                    :twelveHour="true"
                    :disable-views="['years', 'year', 'month', 'day']"
                    style="height: calc(100vh - 240px);"
                    :class="{'vuecal--dark': theme === 'dark'}"
                    :events="events"
                    :editable-events="false"
                    :time-format="'H:mm'"
                    :events-on-month-view="'stack'"
                    :events-count-on-year-view="3"
                    :overlaps-per-time-step="false"
                    :cell-click-hold="false"
                    :sticky-events="true"
                    :events-overlap="true"
                    :detailed-time="true"
                    @ready="getActivities"
                    @view-change="getActivities"
                    @event-click="goToActivity"
                    locale="{{ app()->getLocale() }}"
                >
                    <template #event="{ event }">
                        <div
                            class="event-resize-handle event-resize-handle--top"
                            title="Drag to change start time"
                            @mousedown.stop.prevent="beginResize($event, event, 'start')"
                        >
                            <span class="event-resize-handle__grip"></span>
                        </div>

                        <div
                            class="vuecal__event-content"
                            :style="{ backgroundColor: event._bgColor || '#0e90d9' }"
                            draggable="false"
                            @dragstart.prevent
                            v-tooltip="{
                                content: `
                                    <div class='mb-1 font-semibold text-white'>${event.title}</div>
                                    <div class='mb-1 text-xs text-gray-300'>${formatTime(event.start)} - ${formatTime(event.end)}</div>
                                    ${event.description ? `<div class='text-xs text-gray-200'>${event.description}</div>` : ''
                                }`,
                                html: true,
                                placement: 'top',
                                trigger: 'hover',
                                delay: { show: 200, hide: 100 }
                            }"
                        >
                            <div class="vuecal__event-title font-medium">
                                @{{ event.title }}
                            </div>

                            <div class="vuecal__event-time text-sm">
                                @{{ formatTime(event.start) }} - @{{ formatTime(event.end) }}
                            </div>
                        </div>

                        <div
                            class="event-resize-handle event-resize-handle--bottom"
                            title="Drag to change end time"
                            @mousedown.stop.prevent="beginResize($event, event, 'end')"
                        >
                            <span class="event-resize-handle__grip"></span>
                        </div>
                    </template>
                </v-vue-cal>

                <div
                    class="activity-drag-preview"
                    v-if="dragPreview.visible"
                    :style="{ left: dragPreview.x + 'px', top: dragPreview.y + 'px' }"
                >
                    <div class="font-semibold">
                        @{{ dragPreview.title }}
                    </div>

                    <div class="text-xs opacity-90">
                        @{{ dragPreview.currentLabel }}
                    </div>

                    <div class="text-xs opacity-90" v-if="dragPreview.targetLabel">
                        @{{ dragPreview.targetLabel }}
                    </div>

                    <div class="text-xs opacity-80 mt-1" v-if="dragPreview.actionLabel">
                        @{{ dragPreview.actionLabel }}
                    </div>
                </div>
            </div>
        </script>

        <script type="module">
            app.component('v-activities', {
                template: '#v-activities-template',

                data() {
                    return {
                        viewType: "{{ request('view-type') }}" || 'table',
                    };
                },

                methods: {
                    /**
                     * Toggle view type.
                     *
                     * @param {String} type
                     * @return {void}
                     */
                    toggleView(type) {
                        this.viewType = type;

                        let currentUrl = new URL(window.location);

                        currentUrl.searchParams.set('view-type', type);

                        window.history.pushState({}, '', currentUrl);
                    },
                },
            });
        </script>

        <script type="module">
            app.component('v-calendar', {
                template: '#v-calendar-template',

                data() {
                    return {
                        events: [],
                        theme: document.documentElement.classList.contains('dark') ? 'dark' : 'light',
                        lastViewRange: null,
                        lastDragUpdateAt: 0,
                        isUpdatingTimeline: false,
                        resizeContext: null,
                        dragPreview: {
                            visible: false,
                            x: 0,
                            y: 0,
                            title: '',
                            currentLabel: '',
                            targetLabel: '',
                            actionLabel: '',
                        },
                    };
                },

                mounted() {
                    /**
                     * Listen for the theme change event.
                     *
                     * @return {void}
                     */
                    this.$emitter.on('change-theme', (theme) => this.theme = theme);
                },

                beforeUnmount() {
                    window.removeEventListener('mousemove', this.onResizeMove);
                    window.removeEventListener('mouseup', this.onResizeEnd);
                },

                methods: {
                    /**
                     * Get the activities for the calendar.
                     *
                     * @param {Object} {startDate}
                     * @param {Object} {endDate}
                     * @return {void}
                     */
                    getActivities({startDate, endDate}) {
                        this.lastViewRange = { startDate, endDate };

                        this.$root.pageLoaded = false;

                        this.$axios.get("{{ route('admin.activities.get', ['view_type' => 'calendar']) }}" + `&startDate=${new window['Date'](startDate).toLocaleDateString("en-US")}&endDate=${new window['Date'](endDate).toLocaleDateString("en-US")}`)
                            .then(response => {
                                this.events = this.processEvents(response.data.activities);
                            })
                            .catch(error => {});
                    },

                    /**
                     * Process events to improve their display
                     *
                     * @param {Array} events
                     * @return {Array}
                     */
                    processEvents(events) {
                        const segments = [];

                        events.forEach(event => {
                            const startDate = new window['Date'](event.start);
                            const endDate = new window['Date'](event.end);

                            const startDay = new window['Date'](startDate.getFullYear(), startDate.getMonth(), startDate.getDate());
                            const endDay = new window['Date'](endDate.getFullYear(), endDate.getMonth(), endDate.getDate());

                            if (startDay.getTime() === endDay.getTime()) {
                                segments.push(Object.assign({}, event, {
                                    _originalStart: event.start,
                                    _originalEnd: event.end,
                                    _segmentBaseStart: event.start,
                                }));
                            } else {
                                let currentDay = new window['Date'](startDay);

                                while (currentDay.getTime() <= endDay.getTime()) {
                                    const y = currentDay.getFullYear();
                                    const m = currentDay.getMonth();
                                    const d = currentDay.getDate();

                                    let segStart;
                                    let segEnd;

                                    if (currentDay.getTime() === startDay.getTime()) {
                                        segStart = this.formatDateTime(new window['Date'](y, m, d, startDate.getHours(), startDate.getMinutes()));
                                        segEnd = this.formatDateTime(new window['Date'](y, m, d, 23, 59));
                                    } else if (currentDay.getTime() === endDay.getTime()) {
                                        segStart = this.formatDateTime(new window['Date'](y, m, d, 0, 0));
                                        segEnd = this.formatDateTime(new window['Date'](y, m, d, endDate.getHours(), endDate.getMinutes()));
                                    } else {
                                        segStart = this.formatDateTime(new window['Date'](y, m, d, 0, 0));
                                        segEnd = this.formatDateTime(new window['Date'](y, m, d, 23, 59));
                                    }

                                    segments.push(Object.assign({}, event, {
                                        start: segStart,
                                        end: segEnd,
                                        _originalStart: event.start,
                                        _originalEnd: event.end,
                                        _segmentBaseStart: segStart,
                                    }));

                                    currentDay.setDate(currentDay.getDate() + 1);
                                }
                            }
                        });

                        return segments.map(event => {
                            if (! event._bgColor) {
                                event._bgColor = this.generateEventColor(String(event.id ?? event.title ?? ''));
                            }

                            event.background = false;

                            return event;
                        });
                    },

                    beginResize(domEvent, event, mode) {
                        if (! event?.id || ! event?.start || ! event?.end || this.isUpdatingTimeline) {
                            return;
                        }

                        const fullOriginalStart = new window['Date'](event._originalStart ?? event.start);
                        const fullOriginalEnd = new window['Date'](event._originalEnd ?? event.end);

                        if (Number.isNaN(fullOriginalStart.getTime()) || Number.isNaN(fullOriginalEnd.getTime())) {
                            return;
                        }

                        this.resizeContext = {
                            id: event.id,
                            mode: mode,
                            originalStart: fullOriginalStart,
                            originalEnd: fullOriginalEnd,
                            nextValue: null,
                            title: event.title || 'Activity',
                        };

                        const label = mode === 'start' ? 'start time' : 'end time';

                        this.dragPreview.visible = true;
                        this.dragPreview.title = this.resizeContext.title;
                        this.dragPreview.currentLabel = `${this.formatDayTime(fullOriginalStart)} - ${this.formatDayTime(fullOriginalEnd)}`;
                        this.dragPreview.targetLabel = '';
                        this.dragPreview.actionLabel = `Drag to change ${label}`;

                        this.updateDragPreviewPosition(domEvent.clientX, domEvent.clientY);

                        document.body.style.cursor = 'ns-resize';
                        document.body.style.userSelect = 'none';

                        window.addEventListener('mousemove', this.onResizeMove);
                        window.addEventListener('mouseup', this.onResizeEnd);
                    },

                    onResizeMove(domEvent) {
                        if (! this.resizeContext) {
                            return;
                        }

                        this.updateDragPreviewPosition(domEvent.clientX, domEvent.clientY);

                        const slotTime = this.getSlotDateFromPoint(domEvent.clientX, domEvent.clientY);

                        if (! slotTime) {
                            this.dragPreview.targetLabel = '';

                            return;
                        }

                        const ctx = this.resizeContext;

                        if (ctx.mode === 'start') {
                            const maxMs = ctx.originalEnd.getTime() - (15 * 60 * 1000);
                            const clamped = new window['Date'](Math.min(slotTime.getTime(), maxMs));

                            ctx.nextValue = clamped;
                            this.dragPreview.targetLabel = `New: ${this.formatDayTime(clamped)} - ${this.formatDayTime(ctx.originalEnd)}`;
                        } else {
                            const minMs = ctx.originalStart.getTime() + (15 * 60 * 1000);
                            const clamped = new window['Date'](Math.max(slotTime.getTime(), minMs));

                            ctx.nextValue = clamped;
                            this.dragPreview.targetLabel = `New: ${this.formatDayTime(ctx.originalStart)} - ${this.formatDayTime(clamped)}`;
                        }
                    },

                    onResizeEnd() {
                        window.removeEventListener('mousemove', this.onResizeMove);
                        window.removeEventListener('mouseup', this.onResizeEnd);

                        document.body.style.cursor = '';
                        document.body.style.userSelect = '';

                        if (! this.resizeContext) {
                            return;
                        }

                        const { id, mode, originalStart, originalEnd, nextValue } = this.resizeContext;

                        this.resizeContext = null;
                        this.dragPreview.visible = false;
                        this.dragPreview.targetLabel = '';
                        this.dragPreview.actionLabel = '';

                        if (! id || ! nextValue) {
                            return;
                        }

                        let scheduleFrom;
                        let scheduleTo;

                        if (mode === 'start') {
                            if (nextValue.getTime() === originalStart.getTime()) {
                                return;
                            }

                            scheduleFrom = nextValue;
                            scheduleTo = originalEnd;
                        } else {
                            if (nextValue.getTime() === originalEnd.getTime()) {
                                return;
                            }

                            scheduleFrom = originalStart;
                            scheduleTo = nextValue;
                        }

                        this.persistTimelineUpdate(id, scheduleFrom, scheduleTo);
                    },

                    persistTimelineUpdate(activityId, scheduleFrom, scheduleTo) {
                        if (this.isUpdatingTimeline) {
                            return;
                        }

                        this.isUpdatingTimeline = true;

                        const url = `{{ route('admin.activities.update', ':id') }}`.replace(':id', activityId);

                        this.$axios.put(url, {
                            schedule_from: this.formatDateTimeForApi(scheduleFrom),
                            schedule_to: this.formatDateTimeForApi(scheduleTo),
                        })
                            .then(() => {
                                this.lastDragUpdateAt = window['Date'].now();
                            })
                            .catch(() => {})
                            .finally(() => {
                                this.isUpdatingTimeline = false;

                                if (this.lastViewRange) {
                                    this.getActivities(this.lastViewRange);
                                }
                            });
                    },

                    getSlotDateFromPoint(clientX, clientY) {
                        const cal = this.$refs.calendar;

                        if (! this.lastViewRange?.startDate || ! cal?.$el) {
                            return null;
                        }

                        const cellsEl = cal.cellsEl || cal.$el.querySelector('.vuecal__bg');

                        if (! cellsEl) {
                            return null;
                        }

                        const cellsRect = cellsEl.getBoundingClientRect();

                        const timeStep = cal.timeStep ?? cal.$props?.timeStep ?? 60;
                        const timeCellHeight = cal.timeCellHeight ?? cal.$props?.timeCellHeight ?? 40;
                        const timeFrom = cal.timeFrom ?? cal.$props?.timeFrom ?? 0;

                        const tolerance = 20;

                        if (
                            clientX < cellsRect.left - tolerance
                            || clientX > cellsRect.right + tolerance
                            || clientY < cellsRect.top - tolerance
                            || clientY > cellsRect.bottom + tolerance
                        ) {
                            return null;
                        }

                        const viewStart = new window['Date'](this.lastViewRange.startDate);
                        viewStart.setHours(0, 0, 0, 0);

                        const viewEnd = new window['Date'](this.lastViewRange.endDate);
                        viewEnd.setHours(0, 0, 0, 0);

                        const dayMs = 24 * 60 * 60 * 1000;
                        const totalDays = Math.max(Math.round((viewEnd.getTime() - viewStart.getTime()) / dayMs) + 1, 1);
                        const dayWidth = cellsRect.width / totalDays;
                        const dayIndex = Math.min(Math.max(Math.floor((clientX - cellsRect.left) / dayWidth), 0), totalDays - 1);

                        const slotDate = new window['Date'](viewStart.getTime() + (dayIndex * dayMs));

                        const clampedY = Math.max(cellsRect.top, Math.min(clientY, cellsRect.bottom));
                        const y = clampedY - cellsRect.top;
                        const rawMinutes = Math.round(y * timeStep / timeCellHeight + timeFrom);

                        const snappedMinutes = Math.round(rawMinutes / 15) * 15;
                        const boundedMinutes = Math.max(0, Math.min(snappedMinutes, 23 * 60 + 45));

                        slotDate.setHours(Math.floor(boundedMinutes / 60), boundedMinutes % 60, 0, 0);

                        return slotDate;
                    },

                    updateDragPreviewPosition(clientX, clientY) {
                        const safeX = Number.isFinite(clientX) ? clientX : 0;
                        const safeY = Number.isFinite(clientY) ? clientY : 0;

                        this.dragPreview.x = safeX + 16;
                        this.dragPreview.y = safeY + 16;
                    },

                    formatDayTime(date) {
                        const day = date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });

                        return `${day} ${this.formatTime(date)}`;
                    },

                    formatDateTimeForApi(date) {
                        const year = date.getFullYear();
                        const month = String(date.getMonth() + 1).padStart(2, '0');
                        const day = String(date.getDate()).padStart(2, '0');
                        const hours = String(date.getHours()).padStart(2, '0');
                        const minutes = String(date.getMinutes()).padStart(2, '0');
                        const seconds = String(date.getSeconds()).padStart(2, '0');

                        return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
                    },

                    formatDateTime(date) {
                        const year = date.getFullYear();
                        const month = String(date.getMonth() + 1).padStart(2, '0');
                        const day = String(date.getDate()).padStart(2, '0');
                        const hours = String(date.getHours()).padStart(2, '0');
                        const minutes = String(date.getMinutes()).padStart(2, '0');

                        return `${year}-${month}-${day} ${hours}:${minutes}`;
                    },

                    /**
                     * Simple string hash function for consistent color generation
                     *
                     * @param {string} str
                     * @return {number}
                     */
                    hashString(str) {
                        let hash = 0;

                        for (let i = 0; i < str.length; i++) {
                            hash = ((hash << 5) - hash) + str.charCodeAt(i);
                            hash |= 0;
                        }

                        return hash;
                    },

                    generateEventColor(value) {
                        const hash = this.hashString(value);

                        const colors = [
                            '#e11d48',
                            '#f97316',
                            '#eab308',
                            '#22c55e',
                            '#14b8a6',
                            '#06b6d4',
                            '#3b82f6',
                            '#6366f1',
                            '#8b5cf6',
                            '#d946ef',
                            '#ec4899',
                            '#84cc16',
                        ];

                        return colors[Math.abs(hash) % colors.length];
                    },

                    /**
                     * Format time for display in event template
                     *
                     * @param {object} date
                     * @return {string}
                     */
                    formatTime(date) {
                        if (! date) {
                            return '';
                        }

                        const dateObj = new (window['Date'])(date);

                        let hours = dateObj.getHours().toString().padStart(2, '0');

                        const minutes = dateObj.getMinutes().toString().padStart(2, '0');

                        return `${hours}:${minutes}`;
                    },

                    /**
                     * Redirect to the activity edit page.
                     *
                     * @param {Object} event
                     * @return {void}
                     */
                    goToActivity(event) {
                        if (window['Date'].now() - this.lastDragUpdateAt < 350) {
                            return;
                        }

                        if (event.id) {
                            window.location.href = `{{ route('admin.activities.edit', ':id') }}`.replace(':id', event.id);
                        }
                    },
                },
            });
        </script>

        <script>
            /**
             * Update status for `is_done`.
             *
             * @param {Event} {target}
             * @return {void}
             */
            const updateStatus = ({ target }, url) => {
                axios
                    .post(url, {
                        _method: 'put',
                        is_done: target.checked,
                    })
                    .then(response => {
                        window.emitter.emit('add-flash', { type: 'success', message: response.data.message });
                    })
                    .catch(error => {});
            };
        </script>
    @endPushOnce

    @pushOnce('styles')
        <style>
            /* Base Event Styling */
            .vuecal__event {
                background-color: transparent;
                color: #fff !important;
                cursor: pointer;
                min-height: 20px;
                overflow: hidden;
                padding: 0;
                transition: box-shadow 0.2s ease, transform 0.2s ease;
                -webkit-user-select: none;
                user-select: none;
            }

            .vuecal__event-content {
                position: absolute;
                inset: 3px;
                padding: 6px 8px;
                font-size: 14px;
                color: #fff;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
                overflow: hidden;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                gap: 4px;
                text-align: center;
            }

            .vuecal__event:hover .vuecal__event-content {
                box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
                filter: brightness(1.1);
            }

            .event-resize-handle {
                position: absolute;
                left: 0;
                right: 0;
                height: 12px;
                z-index: 10;
                cursor: ns-resize;
                display: flex;
                justify-content: center;
                align-items: center;
            }

            .event-resize-handle--top {
                top: 0;
                border-radius: 4px 4px 0 0;
            }

            .event-resize-handle--bottom {
                bottom: 0;
                border-radius: 0 0 4px 4px;
            }

            .event-resize-handle__grip {
                width: 28px;
                height: 3px;
                border-radius: 2px;
                background: rgba(255, 255, 255, 0.5);
                opacity: 0;
                transition: opacity 0.15s ease;
            }

            .vuecal__event:hover .event-resize-handle__grip,
            .event-resize-handle:hover .event-resize-handle__grip {
                opacity: 1;
            }

            .event-resize-handle:hover .event-resize-handle__grip {
                background: rgba(255, 255, 255, 0.9);
            }

            .vuecal__event .vuecal__event-resize-handle {
                display: none;
            }

            .vuecal__event.done .vuecal__event-content {
                background-color: #53c41a !important;
            }

            /* Event Title & Time */
            .vuecal__event-title {
                font-weight: 500;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
                width: 100%;
            }

            .vuecal__event-time {
                font-size: 12px;
                opacity: 0.8;
                width: 100%;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            /* More Events Indicator */
            .vuecal__cell-more-events {
                font-size: 12px;
                color: #666;
                padding: 2px 5px;
                text-align: center;
                cursor: pointer;
                background-color: rgba(0, 0, 0, 0.04);
            }

            /* Events Count Badge */
            .vuecal__cell-events-count {
                background-color: rgba(66, 92, 240, 0.85);
                padding: 0 4px;
                font-size: 11px;
            }

            /* Week View Stacking */
            .vuecal--week-view .vuecal__event-container {
                padding: 1px;
            }

            .vuecal__event-container--overlapped .vuecal__event {
                margin-top: 2px;
                min-height: 28px;
            }

            /* Dark Mode Styles */
            .vuecal--dark {
                background-color: #1F2937 !important;
                color: #FFFFFF !important;
            }

            .vuecal--dark .vuecal__header,
            .vuecal--dark .vuecal__header-weekdays,
            .vuecal--dark .vuecal__header-months {
                background-color: #374151 !important;
                color: #FFFFFF !important;
            }

            .vuecal--dark .vuecal__day,
            .vuecal--dark .vuecal__month-view,
            .vuecal--dark .vuecal__week-view,
            .vuecal--dark .vuecal__day--weekend,
            .vuecal--dark .vuecal__day--selected {
                background-color: #1F2937 !important;
                color: #FFFFFF !important;
            }

            .vuecal--dark .vuecal__event .vuecal__event-content {
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
            }

            .vuecal--dark .vuecal__cell-more-events {
                color: #ddd;
                background-color: rgba(255, 255, 255, 0.1);
            }

            .activity-drag-preview {
                position: fixed;
                z-index: 9999;
                pointer-events: none;
                min-width: 180px;
                max-width: 280px;
                padding: 8px 10px;
                border-radius: 8px;
                color: #fff;
                background: rgba(17, 24, 39, 0.92);
                box-shadow: 0 6px 18px rgba(0, 0, 0, 0.25);
                border: 1px solid rgba(255, 255, 255, 0.2);
                line-height: 1.3;
            }
        </style>
    @endPushOnce
</x-admin::layouts>
