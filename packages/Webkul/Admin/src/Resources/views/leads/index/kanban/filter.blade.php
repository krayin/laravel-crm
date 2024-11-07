{!! view_render_event('admin.leads.index.kanban.filter.before') !!}

<v-kanban-filter
    :is-loading="isLoading"
    :available="available"
    :applied="applied"
    @applyFilters="filter"
>
</v-kanban-filter>

{!! view_render_event('admin.leads.index.kanban.filter.after') !!}

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-kanban-filter-template"
    >
        {!! view_render_event('admin.leads.index.kanban.filter.drawer.before') !!}

        <x-admin::drawer
            width="350px"
            ref="kanbanFilterDrawer"
        >
            <!-- Drawer Toggler -->
            <x-slot:toggle>
                {!! view_render_event('admin.leads.index.kanban.filter.drawer.toggle_button.before') !!}

                <div class="relative flex cursor-pointer items-center rounded-md bg-sky-100 px-4 py-[9px] font-semibold text-sky-600 dark:bg-brandColor dark:text-white">
                    @lang('admin::app.leads.index.kanban.toolbar.filters.filter')

                    <span
                        class="absolute right-2 top-2 h-1.5 w-1.5 rounded-full bg-sky-600 dark:bg-white"
                        v-if="hasAnyAppliedColumn()"
                    >
                    </span>
                </div>

                {!! view_render_event('admin.leads.index.kanban.filter.drawer.toggle_button.after') !!}
            </x-slot>

            <!-- Drawer Header -->
            <x-slot:header class="p-3.5">
                {!! view_render_event('admin.leads.index.kanban.filter.drawer.header.title.before') !!}

                <div class="grid gap-3">
                    <div class="flex items-center justify-between">
                        <p class="text-xl font-semibold dark:text-white">
                            @lang('admin::app.leads.index.kanban.toolbar.filters.filters')
                        </p>
                    </div>
                </div>

                {!! view_render_event('admin.leads.index.kanban.filter.drawer.header.title.after') !!}
            </x-slot>

            <!-- Drawer Content -->
            <x-slot:content>
                {!! view_render_event('admin.leads.index.kanban.filter.drawer.content.before') !!}

                <div>
                    <div v-for="column in available.columns">
                        <div v-if="column.filterable">
                            <!-- Boolean -->
                            <div v-if="column.type === 'boolean'">
                                <!-- Dropdown -->
                                <template v-if="column.filterable_type === 'dropdown'">
                                    <div class="flex items-center justify-between">
                                        <p
                                            class="text-xs font-medium text-gray-800 dark:text-white"
                                            v-text="column.label"
                                        >
                                        </p>

                                        <div
                                            class="flex items-center gap-x-1.5"
                                            @click="removeAppliedColumnAllValues(column.index)"
                                        >
                                            <p
                                                class="cursor-pointer text-xs font-medium leading-6 text-brandColor"
                                                v-if="hasAnyAppliedColumnValues(column.index)"
                                            >
                                                @lang('admin::app.leads.index.kanban.toolbar.filters.clear-all')
                                            </p>
                                        </div>
                                    </div>

                                    <div class="mb-2 mt-1.5">
                                        <x-admin::dropdown>
                                            <x-slot:toggle>
                                                <button
                                                    type="button"
                                                    class="inline-flex w-full cursor-pointer appearance-none items-center justify-between gap-x-2 rounded-md border bg-white px-2.5 py-1.5 text-center leading-6 text-gray-600 transition-all marker:shadow hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400"
                                                >
                                                    <!-- If Allow Multiple Values -->
                                                    <span
                                                        class="text-sm text-gray-400 dark:text-gray-400"
                                                        v-text="'@lang('admin::app.leads.index.kanban.toolbar.filters.select')'"
                                                        v-if="column.allow_multiple_values"
                                                    >
                                                    </span>

                                                    <!-- If Allow Single Value -->
                                                    <span
                                                        class="text-sm text-gray-400 dark:text-gray-400"
                                                        v-text="column.filterable_options.find((option => option.value === getAppliedColumnValues(column.index)))?.label ?? '@lang('admin::app.leads.index.kanban.toolbar.filters.select')'"
                                                        v-else
                                                    >
                                                    </span>

                                                    <span class="icon-down-arrow text-2xl"></span>
                                                </button>
                                            </x-slot>

                                            <x-slot:menu>
                                                <x-admin::dropdown.menu.item
                                                    v-for="option in column.filterable_options"
                                                    v-text="option.label"
                                                    @click="addFilter(option.value, column)"
                                                >
                                                </x-admin::dropdown.menu.item>
                                            </x-slot>
                                        </x-admin::dropdown>
                                    </div>

                                    <div class="mb-4 flex flex-wrap gap-2">
                                        <!-- If Allow Multiple Values -->
                                        <template v-if="column.allow_multiple_values">
                                            <p
                                                class="flex items-center rounded bg-gray-600 px-2 py-1 font-semibold text-white"
                                                v-for="appliedColumnValue in getAppliedColumnValues(column.index)"
                                            >
                                                <!-- Retrieving the label from the options based on the applied column value. -->
                                                <span v-text="column.filterable_options.find((option => option.value == appliedColumnValue)).label"></span>

                                                <span
                                                    class="icon-cross-large-large cursor-pointer text-lg text-white ltr:ml-1.5 rtl:mr-1.5"
                                                    @click="removeAppliedColumnValue(column.index, appliedColumnValue)"
                                                >
                                                </span>
                                            </p>
                                        </template>
                                    </div>
                                </template>

                                <!-- Basic (If Needed) -->
                                <template v-else></template>
                            </div>

                            <!-- Date -->
                            <div v-else-if="column.type === 'date'">
                                <!-- Range -->
                                <template v-if="column.filterable_type === 'date_range'">
                                    <div class="flex items-center justify-between">
                                        <p
                                            class="text-xs font-medium text-gray-800 dark:text-white"
                                            v-text="column.label"
                                        >
                                        </p>

                                        <div
                                            class="flex items-center gap-x-1.5"
                                            @click="removeAppliedColumnAllValues(column.index)"
                                        >
                                            <p
                                                class="cursor-pointer text-xs font-medium leading-6 text-brandColor"
                                                v-if="hasAnyAppliedColumnValues(column.index)"
                                            >
                                                @lang('admin::app.leads.index.kanban.toolbar.filters.clear-all')
                                            </p>
                                        </div>
                                    </div>

                                    <div class="mt-1.5 grid grid-cols-2 gap-1.5">
                                        <p
                                            class="cursor-pointer rounded-md border px-3 py-2 text-center text-sm font-medium leading-6 text-gray-600 transition-all hover:border-gray-400 dark:border-gray-800 dark:text-gray-300 dark:hover:border-gray-400"
                                            v-for="option in column.filterable_options"
                                            v-text="option.label"
                                            @click="addFilter(
                                                $event,
                                                column,
                                                { quickFilter: { isActive: true, selectedFilter: option } }
                                            )"
                                        >
                                        </p>

                                        <x-admin::flat-picker.date ::allow-input="false">
                                            <input
                                                type="date"
                                                :name="`${column.index}[from]`"
                                                value=""
                                                class="flex min-h-[39px] w-full rounded-md border px-3 py-2 text-sm text-gray-600 transition-all hover:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 ltr:pr-8 rtl:pl-8"
                                                :placeholder="column.label"
                                                :ref="`${column.index}[from]`"
                                                @change="addFilter(
                                                    $event,
                                                    column,
                                                    { range: { name: 'from' }, quickFilter: { isActive: false } }
                                                )"
                                            />
                                        </x-admin::flat-picker.date>

                                        <x-admin::flat-picker.date ::allow-input="false">
                                            <input
                                                type="date"
                                                :name="`${column.index}[to]`"
                                                value=""
                                                class="flex min-h-[39px] w-full rounded-md border px-3 py-2 text-sm text-gray-600 transition-all hover:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 ltr:pr-8 rtl:pl-8"
                                                :placeholder="column.label"
                                                :ref="`${column.index}[from]`"
                                                @change="addFilter(
                                                    $event,
                                                    column,
                                                    { range: { name: 'to' }, quickFilter: { isActive: false } }
                                                )"
                                            />
                                        </x-admin::flat-picker.date>

                                        <div class="mb-4 flex flex-wrap gap-2">
                                            <p
                                                class="flex items-center rounded bg-gray-600 px-2 py-1 font-semibold text-white"
                                                v-if="findAppliedColumn(column.index)"
                                            >
                                                <span>
                                                    @{{ getFormattedDates(findAppliedColumn(column.index)) }}
                                                </span>

                                                <span
                                                    class="icon-cross-large cursor-pointer text-lg text-white ltr:ml-1.5 rtl:mr-1.5"
                                                    @click="removeAppliedColumnValue(column.index)"
                                                >
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                </template>

                                <!-- Basic -->
                                <template v-else>
                                    <div class="flex items-center justify-between">
                                        <p
                                            class="text-xs font-medium text-gray-800 dark:text-white"
                                            v-text="column.label"
                                        >
                                        </p>

                                        <div
                                            class="flex items-center gap-x-1.5"
                                            @click="removeAppliedColumnAllValues(column.index)"
                                        >
                                            <p
                                                class="cursor-pointer text-xs font-medium leading-6 text-brandColor"
                                                v-if="hasAnyAppliedColumnValues(column.index)"
                                            >
                                                @lang('admin::app.leads.index.kanban.toolbar.filters.clear-all')
                                            </p>
                                        </div>
                                    </div>

                                    <div class="mt-1.5 grid">
                                        <x-admin::flat-picker.date ::allow-input="false">
                                            <input
                                                type="date"
                                                :name="column.index"
                                                value=""
                                                class="flex min-h-[39px] w-full rounded-md border px-3 py-2 text-sm text-gray-600 transition-all hover:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400"
                                                :placeholder="column.label"
                                                :ref="column.index"
                                                @change="addFilter($event, column)"
                                            />
                                        </x-admin::flat-picker.date>

                                        <div class="mb-4 flex flex-wrap gap-2">
                                            <p
                                                class="flex items-center rounded bg-gray-600 px-2 py-1 font-semibold text-white"
                                                v-if="findAppliedColumn(column.index)"
                                            >
                                                <span>
                                                    @{{ getFormattedDates(findAppliedColumn(column.index)) }}
                                                </span>

                                                <span
                                                    class="icon-cross-large cursor-pointer text-lg text-white ltr:ml-1.5 rtl:mr-1.5"
                                                    @click="removeAppliedColumnValue(column.index)"
                                                >
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <!-- Date Time -->
                            <div v-else-if="column.type === 'datetime'">
                                <!-- Range -->
                                <template v-if="column.filterable_type === 'datetime_range'">
                                    <div class="flex items-center justify-between">
                                        <p
                                            class="text-xs font-medium text-gray-800 dark:text-white"
                                            v-text="column.label"
                                        >
                                        </p>

                                        <div
                                            class="flex items-center gap-x-1.5"
                                            @click="removeAppliedColumnAllValues(column.index)"
                                        >
                                            <p
                                                class="cursor-pointer text-xs font-medium leading-6 text-brandColor"
                                                v-if="hasAnyAppliedColumnValues(column.index)"
                                            >
                                                @lang('admin::app.leads.index.kanban.toolbar.filters.clear-all')
                                            </p>
                                        </div>
                                    </div>

                                    <div class="my-4 grid grid-cols-2 gap-1.5">
                                        <p
                                            class="cursor-pointer rounded-md border px-3 py-2 text-center text-sm font-medium leading-6 text-gray-600 transition-all hover:border-gray-400 dark:border-gray-800 dark:text-gray-300 dark:hover:border-gray-400"
                                            v-for="option in column.filterable_options"
                                            v-text="option.label"
                                            @click="addFilter(
                                                $event,
                                                column,
                                                { quickFilter: { isActive: true, selectedFilter: option } }
                                            )"
                                        >
                                        </p>

                                        <x-admin::flat-picker.datetime ::allow-input="false">
                                            <input
                                                type="datetime-local"
                                                :name="`${column.index}[from]`"
                                                value=""
                                                class="flex min-h-[39px] w-full rounded-md border px-3 py-2 text-sm text-gray-600 transition-all hover:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400"
                                                :placeholder="column.label"
                                                :ref="`${column.index}[from]`"
                                                @change="addFilter(
                                                    $event,
                                                    column,
                                                    { range: { name: 'from' }, quickFilter: { isActive: false } }
                                                )"
                                            />
                                        </x-admin::flat-picker.datetime>

                                        <x-admin::flat-picker.datetime ::allow-input="false">
                                            <input
                                                type="datetime-local"
                                                :name="`${column.index}[to]`"
                                                value=""
                                                class="flex min-h-[39px] w-full rounded-md border px-3 py-2 text-sm text-gray-600 transition-all hover:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400"
                                                :placeholder="column.label"
                                                :ref="`${column.index}[from]`"
                                                @change="addFilter(
                                                    $event,
                                                    column,
                                                    { range: { name: 'to' }, quickFilter: { isActive: false } }
                                                )"
                                            />
                                        </x-admin::flat-picker.datetime>

                                        <div class="mb-4 flex flex-wrap gap-2">
                                            <p
                                                class="flex items-center rounded bg-gray-600 px-2 py-1 font-semibold text-white"
                                                v-if="findAppliedColumn(column.index)"
                                            >
                                                <span>
                                                    @{{ getFormattedDates(findAppliedColumn(column.index)) }}
                                                </span>

                                                <span
                                                    class="icon-cross-large cursor-pointer text-lg text-white ltr:ml-1.5 rtl:mr-1.5"
                                                    @click="removeAppliedColumnValue(column.index)"
                                                >
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                </template>

                                <!-- Basic -->
                                <template v-else>
                                    <div class="flex items-center justify-between">
                                        <p
                                            class="text-xs font-medium text-gray-800 dark:text-white"
                                            v-text="column.label"
                                        >
                                        </p>

                                        <div
                                            class="flex items-center gap-x-1.5"
                                            @click="removeAppliedColumnAllValues(column.index)"
                                        >
                                            <p
                                                class="cursor-pointer text-xs font-medium leading-6 text-brandColor"
                                                v-if="hasAnyAppliedColumnValues(column.index)"
                                            >
                                                @lang('admin::app.leads.index.kanban.toolbar.filters.clear-all')
                                            </p>
                                        </div>
                                    </div>

                                    <div class="my-4 grid">
                                        <x-admin::flat-picker.datetime ::allow-input="false">
                                            <input
                                                type="datetime-local"
                                                :name="column.index"
                                                value=""
                                                class="flex min-h-[39px] w-full rounded-md border px-3 py-2 text-sm text-gray-600 transition-all hover:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400"
                                                :placeholder="column.label"
                                                :ref="column.index"
                                                @change="addFilter($event, column)"
                                            />
                                        </x-admin::flat-picker.datetime>

                                        <div class="mb-4 flex flex-wrap gap-2">
                                            <p
                                                class="flex items-center rounded bg-gray-600 px-2 py-1 font-semibold text-white"
                                                v-if="findAppliedColumn(column.index)"
                                            >
                                                <span>
                                                    @{{ getFormattedDates(findAppliedColumn(column.index)) }}
                                                </span>

                                                <span
                                                    class="icon-cross-large cursor-pointer text-lg text-white ltr:ml-1.5 rtl:mr-1.5"
                                                    @click="removeAppliedColumnValue(column.index)"
                                                >
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <!-- Rest -->
                            <div v-else>
                                <!-- Dropdown -->
                                <template v-if="column.filterable_type === 'dropdown'">
                                    <div class="flex items-center justify-between">
                                        <p
                                            class="text-xs font-medium text-gray-800 dark:text-white"
                                            v-text="column.label"
                                        >
                                        </p>

                                        <div
                                            class="flex items-center gap-x-1.5"
                                            @click="removeAppliedColumnAllValues(column.index)"
                                        >
                                            <p
                                                class="cursor-pointer text-xs font-medium leading-6 text-brandColor"
                                                v-if="hasAnyAppliedColumnValues(column.index)"
                                            >
                                                @lang('admin::app.leads.index.kanban.toolbar.filters.clear-all')
                                            </p>
                                        </div>
                                    </div>

                                    <div class="mb-2 mt-1.5">
                                        <x-admin::dropdown>
                                            <x-slot:toggle>
                                                <button
                                                    type="button"
                                                    class="inline-flex w-full cursor-pointer appearance-none items-center justify-between gap-x-2 rounded-md border bg-white px-2.5 py-1.5 text-center leading-6 text-gray-600 transition-all marker:shadow hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400"
                                                >
                                                    <!-- If Allow Multiple Values -->
                                                    <span
                                                        class="text-sm text-gray-400 dark:text-gray-400"
                                                        v-text="'@lang('admin::app.leads.index.kanban.toolbar.filters.select')'"
                                                        v-if="column.allow_multiple_values"
                                                    >
                                                    </span>

                                                    <!-- If Allow Single Value -->
                                                    <span
                                                        class="text-sm text-gray-400 dark:text-gray-400"
                                                        v-text="column.filterable_options.find((option => option.value === getAppliedColumnValues(column.index)))?.label ?? '@lang('admin::app.leads.index.kanban.toolbar.filters.select')'"
                                                        v-else
                                                    >
                                                    </span>

                                                    <span class="icon-down-arrow text-2xl"></span>
                                                </button>
                                            </x-slot>

                                            <x-slot:menu>
                                                <x-admin::dropdown.menu.item
                                                    v-for="option in column.filterable_options"
                                                    v-text="option.label"
                                                    @click="addFilter(option.value, column)"
                                                >
                                                </x-admin::dropdown.menu.item>
                                            </x-slot>
                                        </x-admin::dropdown>
                                    </div>

                                    <div class="mb-4 flex flex-wrap gap-2">
                                        <!-- If Allow Multiple Values -->
                                        <template v-if="column.allow_multiple_values">
                                            <p
                                                class="flex items-center rounded bg-gray-600 px-2 py-1 font-semibold text-white"
                                                v-for="appliedColumnValue in getAppliedColumnValues(column.index)"
                                            >
                                                <!-- Retrieving the label from the options based on the applied column value. -->
                                                <span v-text="column.filterable_options.find((option => option.value == appliedColumnValue)).label"></span>

                                                <span
                                                    class="icon-cross-large cursor-pointer text-lg text-white ltr:ml-1.5 rtl:mr-1.5"
                                                    @click="removeAppliedColumnValue(column.index, appliedColumnValue)"
                                                >
                                                </span>
                                            </p>
                                        </template>
                                    </div>
                                </template>

                                <template v-else-if="column.filterable_type === 'searchable_dropdown'">
                                    <div class="flex items-center justify-between">
                                        <p
                                            class="text-xs font-medium text-gray-800 dark:text-white"
                                            v-text="column.label"
                                        >
                                        </p>

                                        <div
                                            class="flex items-center gap-x-1.5"
                                            @click="removeAppliedColumnAllValues(column.index)"
                                        >
                                            <p
                                                class="cursor-pointer text-xs font-medium leading-6 text-blue-600"
                                                v-if="hasAnyAppliedColumnValues(column.index)"
                                            >
                                                @lang('admin::app.components.datagrid.filters.custom-filters.clear-all')
                                            </p>
                                        </div>
                                    </div>

                                    <div class="mb-2 mt-1.5">
                                        <v-kanban-searchable-dropdown
                                            :column="column"
                                            @select-option="addFilter($event.target.value, column);"
                                        >
                                        </v-kanban-searchable-dropdown>
                                    </div>

                                    <div class="mb-4 flex flex-wrap gap-2">
                                        <p
                                            class="flex items-center rounded bg-gray-600 px-2 py-1 font-semibold text-white"
                                            v-for="appliedColumnValue in getAppliedColumnValues(column.index)"
                                        >
                                            <span v-text="appliedColumnValue.label"></span>

                                            <span
                                                class="icon-cross-large cursor-pointer text-lg text-white ltr:ml-1.5 rtl:mr-1.5"
                                                @click="removeAppliedColumnValue(column.index, appliedColumnValue)"
                                            >
                                            </span>
                                        </p>
                                    </div>
                                </template>

                                <!-- Basic -->
                                <template v-else>
                                    <div class="flex items-center justify-between">
                                        <p
                                            class="text-xs font-medium text-gray-800 dark:text-white"
                                            v-text="column.label"
                                        >
                                        </p>

                                        <div
                                            class="flex items-center gap-x-1.5"
                                            @click="removeAppliedColumnAllValues(column.index)"
                                        >
                                            <p
                                                class="cursor-pointer text-xs font-medium leading-6 text-brandColor"
                                                v-if="hasAnyAppliedColumnValues(column.index)"
                                            >
                                                @lang('admin::app.leads.index.kanban.toolbar.filters.clear-all')
                                            </p>
                                        </div>
                                    </div>

                                    <div class="mb-2 mt-1.5 grid">
                                        <input
                                            type="text"
                                            class="block w-full rounded-md border bg-white px-2 py-1.5 text-sm leading-6 text-gray-600 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400"
                                            :name="column.index"
                                            :placeholder="column.label"
                                            @keyup.enter="addFilter($event, column)"
                                        />
                                    </div>

                                    <div class="mb-4 flex flex-wrap gap-2">
                                        <!-- If Allow Multiple Values -->
                                        <template v-if="column.allow_multiple_values">
                                            <p
                                                class="flex items-center rounded bg-gray-600 px-2 py-1 font-semibold text-white"
                                                v-for="appliedColumnValue in getAppliedColumnValues(column.index)"
                                            >
                                                <span v-text="appliedColumnValue"></span>

                                                <span
                                                    class="icon-cross-large cursor-pointer text-lg text-white ltr:ml-1.5 rtl:mr-1.5"
                                                    @click="removeAppliedColumnValue(column.index, appliedColumnValue)"
                                                >
                                                </span>
                                            </p>
                                        </template>

                                        <!-- If Allow Single Value -->
                                        <template v-else>
                                            <p
                                                class="flex items-center rounded bg-gray-600 px-2 py-1 font-semibold text-white"
                                                v-if="getAppliedColumnValues(column.index) !== ''"
                                            >
                                                <span v-text="getAppliedColumnValues(column.index)"></span>

                                                <span
                                                    class="icon-cross-large cursor-pointer text-lg text-white ltr:ml-1.5 rtl:mr-1.5"
                                                    @click="removeAppliedColumnValue(column.index, getAppliedColumnValues(column.index))"
                                                >
                                                </span>
                                            </p>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                {!! view_render_event('admin.leads.index.kanban.filter.drawer.content.after') !!}
            </x-slot:content>

            <!-- Drawer Footer -->
            <x-slot:footer class="!pb-3">
                {!! view_render_event('admin.leads.index.kanban.filter.drawer.footer.before') !!}

                <div class="flex justify-end gap-2 px-2 pt-3">
                    <!-- Apply Filter Button -->
                    <button
                        type="button"
                        class="primary-button"
                        @click="applyFilters"
                    >
                        @lang('admin::app.leads.index.kanban.toolbar.filters.apply-filters')
                    </button>
                </div>

                {!! view_render_event('admin.leads.index.kanban.filter.drawer.footer.after') !!}
            </x-slot>
        </x-admin::drawer>

        {!! view_render_event('admin.leads.index.kanban.filter.drawer.after') !!}
    </script>

    <script type="module">
        app.component('v-kanban-filter', {
            template: '#v-kanban-filter-template',

            props: ['isLoading', 'available', 'applied'],

            emits: ['applyFilters'],

            data() {
                return {
                    filters: {
                        columns: [],
                    },
                };
            },

            mounted() {
                this.filters.columns = this.getAppliedColumns();
            },

            methods: {
                /**
                 * Get applied columns.
                 *
                 * @returns {object}
                 */
                getAppliedColumns() {
                    return this.applied.filters.columns.filter((column) => column.index !== 'all');
                },

                /**
                 * Has any applied column.
                 *
                 * @returns {boolean}
                 */
                hasAnyAppliedColumn() {
                    return this.getAppliedColumns().length > 0;
                },

                /**
                 * Apply all added filters.
                 *
                 * @returns {void}
                 */
                applyFilters() {
                    this.$emit('applyFilters', this.filters);

                    this.$refs.kanbanFilterDrawer.close();
                },

                /**
                 * Add filter.
                 *
                 * @param {Event} $event
                 * @param {object} column
                 * @param {object} additional
                 * @returns {void}
                 */
                addFilter($event, column = null, additional = {}) {
                    let quickFilter = additional?.quickFilter;

                    if (quickFilter?.isActive) {
                        let options = quickFilter.selectedFilter;

                        switch (column.type) {
                            case 'date':
                            case 'datetime':
                                this.applyColumnValues(column, options.name);

                                break;

                            default:
                                break;
                        }
                    } else {
                        /**
                         * Here, either a real event will come or a string value. If a string value is present, then
                         * we create a similar event-like structure to avoid any breakage and make it easy to use.
                         */
                        if ($event?.target?.value === undefined) {
                            $event = {
                                target: {
                                    value: $event,
                                }
                            };
                        }

                        this.applyColumnValues(column, $event.target.value, additional);

                        if (column) {
                            $event.target.value = '';
                        }
                    }
                },

                /**
                 * Apply column values.
                 *
                 * @param {object} column
                 * @param {string} requestedValue
                 * @param {object} additional
                 * @returns {void}
                 */
                applyColumnValues(column, requestedValue, additional = {}) {
                    let appliedColumn = this.findAppliedColumn(column?.index);

                    if (
                        requestedValue === undefined ||
                        requestedValue === '' ||
                        (appliedColumn?.allow_multiple_values && appliedColumn?.value.includes(requestedValue)) ||
                        (! appliedColumn?.allow_multiple_values && appliedColumn?.value === requestedValue)
                    ) {
                        return;
                    }

                    switch (column.type) {
                        case 'date':
                        case 'datetime':
                            let { range } = additional;

                            if (appliedColumn) {
                                if (range) {
                                    let appliedRanges = ['', ''];

                                    if (typeof appliedColumn.value !== 'string') {
                                        appliedRanges = appliedColumn.value[0];
                                    }

                                    if (range.name == 'from') {
                                        appliedRanges[0] = requestedValue;
                                    }

                                    if (range.name == 'to') {
                                        appliedRanges[1] = requestedValue;
                                    }

                                    appliedColumn.value = [appliedRanges];
                                } else {
                                    appliedColumn.value = requestedValue;
                                }
                            } else {
                                if (range) {
                                    let appliedRanges = ['', ''];

                                    if (range.name == 'from') {
                                        appliedRanges[0] = requestedValue;
                                    }

                                    if (range.name == 'to') {
                                        appliedRanges[1] = requestedValue;
                                    }

                                    this.filters.columns.push({
                                        ...column,
                                        value: [appliedRanges]
                                    });
                                } else {
                                    this.filters.columns.push({
                                        ...column,
                                        value: requestedValue
                                    });
                                }
                            }

                            break;

                        default:
                            if (appliedColumn) {
                                if (appliedColumn.allow_multiple_values) {
                                    appliedColumn.value.push(requestedValue);
                                } else {
                                    appliedColumn.value = requestedValue;
                                }
                            } else {
                                this.filters.columns.push({
                                    ...column,
                                    value: column.allow_multiple_values ? [requestedValue] : requestedValue,
                                });
                            }

                            break;
                    }
                },

                /**
                 * Get formatted dates.
                 *
                 * @param {object} appliedColumn
                 * @returns {string}
                 */
                getFormattedDates(appliedColumn)
                {
                    if (! appliedColumn) {
                        return '';
                    }

                    if (typeof appliedColumn.value === 'string') {
                        const availableColumn = this.available.columns.find(column => column.index === appliedColumn.index);

                        if (availableColumn.filterable_type === 'date_range' || availableColumn.filterable_type === 'datetime_range') {
                            const option = availableColumn.filterable_options.find(option => option.name === appliedColumn.value);

                            return option.label;
                        }

                        return appliedColumn.value;
                    }

                    if (! appliedColumn.value.length) {
                        return '';
                    }

                    return appliedColumn.value[0].join(' to ');
                },

                /**
                 * Check if any values are applied for the specified column.
                 *
                 * @param {object} column
                 * @returns {boolean}
                 */
                hasAnyValue(column) {
                    if (column.allow_multiple_values) {
                        return column.value.length > 0;
                    }

                    return column.value !== '';
                },

                /**
                 * Find applied column.
                 *
                 * @param {string} columnIndex
                 * @returns {object}
                 */
                findAppliedColumn(columnIndex) {
                    return this.filters.columns.find(column => column.index === columnIndex);
                },

                /**
                 * Check if any values are applied for the specified column.
                 *
                 * @param {string} columnIndex
                 * @returns {boolean}
                 */
                hasAnyAppliedColumnValues(columnIndex) {
                    let appliedColumn = this.findAppliedColumn(columnIndex);

                    if (! appliedColumn) {
                        return false;
                    }

                    return this.hasAnyValue(appliedColumn);
                },

                /**
                 * Get applied values for the specified column.
                 *
                 * @param {string} columnIndex
                 * @returns {Array}
                 */
                getAppliedColumnValues(columnIndex) {
                    const appliedColumn = this.findAppliedColumn(columnIndex);

                    if (appliedColumn?.allow_multiple_values) {
                        return appliedColumn?.value ?? [];
                    }

                    return appliedColumn?.value ?? '';
                },

                /**
                 * Remove a specific value from the applied values of the specified column.
                 *
                 * @param {string} columnIndex
                 * @param {any} appliedColumnValue
                 * @returns {void}
                 */
                removeAppliedColumnValue(columnIndex, appliedColumnValue) {
                    let appliedColumn = this.findAppliedColumn(columnIndex);

                    if (appliedColumn?.type === 'date' || appliedColumn?.type === 'datetime') {
                        appliedColumn.value = [];
                    } else {
                        if (appliedColumn.allow_multiple_values) {
                            appliedColumn.value = appliedColumn?.value.filter(value => value !== appliedColumnValue);
                        } else {
                            appliedColumn.value = '';
                        }
                    }

                    /**
                     * Clean up is done here. If there are no applied values present, there is no point in including the applied column as well.
                     */
                    if (! appliedColumn.value.length) {
                        this.filters.columns = this.filters.columns.filter(column => column.index !== columnIndex);
                    }
                },

                /**
                 * Remove all values from the applied values of the specified column.
                 *
                 * @param {string} columnIndex
                 * @returns {void}
                 */
                removeAppliedColumnAllValues(columnIndex) {
                    this.filters.columns = this.filters.columns.filter(column => column.index !== columnIndex);
                },
            },
        });
    </script>

    <script
        type="text/x-template"
        id="v-kanban-searchable-dropdown-template"
    >
        <x-admin::dropdown ::close-on-click="false">
            <!-- Dropdown Toggler -->
            <x-slot:toggle>
                <button
                    type="button"
                    class="inline-flex h-[38px] w-full cursor-pointer appearance-none items-center justify-between gap-x-2 rounded-md border bg-white px-2.5 py-1.5 text-center leading-6 text-gray-600 transition-all marker:shadow hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400"
                >
                    <span
                        class="text-sm text-gray-400 dark:text-gray-400"
                        v-text="'@lang('admin::app.components.datagrid.filters.select')'"
                    >
                    </span>

                    <span class="icon-down-arrow text-2xl"></span>
                </button>
            </x-slot>

            <!-- Dropdown Content -->
            <x-slot:menu>
                <div class="relative">
                    <div class="relative rounded">
                        <ul class="list-reset">
                            <li class="p-2">
                                <input
                                    class="block w-full rounded-md border bg-white px-2 py-1.5 text-sm leading-6 text-gray-600 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400"
                                    @keyup="lookUp($event)"
                                >
                            </li>

                            <ul class="p-2">
                                <li v-if="!isMinimumCharacters">
                                    <p
                                        class="block p-2 text-gray-600 dark:text-gray-300"
                                        v-text="'@lang('admin::app.components.datagrid.filters.dropdown.searchable.at-least-two-chars')'"
                                    >
                                    </p>
                                </li>

                                <li v-else-if="!searchedOptions.length">
                                    <p
                                        class="block p-2 text-gray-600 dark:text-gray-300"
                                        v-text="'@lang('admin::app.components.datagrid.filters.dropdown.searchable.no-results')'"
                                    >
                                    </p>
                                </li>

                                <li
                                    v-for="option in searchedOptions"
                                    v-else
                                >
                                    <p
                                        class="cursor-pointer p-2 text-sm text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-950"
                                        v-text="option.label"
                                        @click="selectOption(option)"
                                    >
                                    </p>
                                </li>
                            </ul>
                        </ul>
                    </div>
                </div>
            </x-slot>
        </x-admin::dropdown>
    </script>

    <script type="module">
        app.component('v-kanban-searchable-dropdown', {
            template: '#v-kanban-searchable-dropdown-template',

            props: ['column'],

            data() {
                return {
                    isMinimumCharacters: false,

                    searchedOptions: [],
                };
            },

            methods: {
                lookUp($event) {
                    let params = {
                        column: this.column.index,
                        search: $event.target.value,
                    };

                    if (! (params['search'].length > 1)) {
                        this.searchedOptions = [];

                        this.isMinimumCharacters = false;

                        return;
                    }

                    this.$axios
                        .get('{{ route('admin.leads.kanban.look_up') }}', {
                            params
                        })
                        .then(({
                            data
                        }) => {
                            this.isMinimumCharacters = true;

                            this.searchedOptions = data;
                        });
                },

                selectOption(option) {
                    this.searchedOptions = [];

                    this.$emit('select-option', {
                        target: {
                            value: option,
                        }
                    });
                },
            }
        });
    </script>
@endpushOnce
