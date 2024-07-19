<x-admin::layouts>
    <x-slot:title>
        Activities
    </x-slot>

    <!-- Activities Datagrid -->
    <v-activities>
        <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white py-2 pl-2 pr-4 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
            <div class="flex flex-col">
                <div class="flex cursor-pointer items-center">
                    <i class="icon-left-arrow text-2xl text-gray-800"></i>
    
                    <a
                        href="{{ route('admin.activities.index') }}"
                        class="text-xs text-gray-800 dark:text-gray-300"
                    >
                        Settings
                    </a>
                </div>
    
                <div class="pl-3 text-xl font-normal dark:text-gray-300">
                    @lang('admin::app.activities.index.title')
                </div>
            </div>
        </div>

        <!-- DataGrid Shimmer -->
        <x-admin::shimmer.datagrid />
    </v-activities>

    @pushOnce('scripts')
        <script 
            type="text/x-template"
            id="v-activities-template"
        >
            <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white py-2 pl-2 pr-4 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                <div class="flex flex-col">
                    <div class="flex cursor-pointer items-center">
                        <i class="icon-left-arrow text-2xl text-gray-800"></i>
        
                        <a
                            href="{{ route('admin.activities.index') }}"
                            class="text-xs text-gray-800 dark:text-gray-300"
                        >
                            Settings
                        </a>
                    </div>
        
                    <div class="pl-3 text-xl font-normal dark:text-gray-300">
                        @lang('admin::app.activities.index.title')
                    </div>
                </div>
            </div>

            <!-- DataGrid Shimmer -->
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
                        <div class="row grid grid-cols-[.3fr_.1fr_.3fr_.5fr] grid-rows-1 items-center border-b px-4 py-2.5 dark:border-gray-800">
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
                                        :class="[applied.sort.order === 'asc' ? 'icon-down-stat': 'icon-up-stat']"
                                        v-if="columnGroup.includes(applied.sort.column)"
                                    ></i>
                                </p>
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
                            class="row grid grid-cols-[.3fr_.1fr_.3fr_.5fr] grid-rows-1 border-b px-4 py-2.5 transition-all hover:bg-gray-50 dark:border-gray-800 dark:hover:bg-gray-950"
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
                                    class="icon-checkbox-outline peer-checked:icon-checkbox-select cursor-pointer rounded-md text-2xl text-gray-600 dark:text-gray-300 peer-checked:text-brandColor"
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
                                        @{{ record.comment }}
                                    </p>

                                    <p v-html="record.lead_title"></p>

                                    <p class="text-gray-600 dark:text-gray-300">
                                        @{{ record.type ?? 'N/A'}}
                                    </p>
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-between gap-x-4">
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
                                    <a
                                        v-for="action in record.actions"
                                        :href="action.url"
                                    >
                                        <span
                                            class="cursor-pointer rounded-md p-1.5 text-2xl transition-all hover:bg-gray-200 dark:hover:bg-gray-800 ltr:ml-1 rtl:mr-1"
                                            :class="action.icon"
                                        ></span>
                                    </a>
                                </div>
                            </div> 
                        </div>
                    </template>
                </template>
            </x-admin::datagrid>
        </script>

        <script type="module">
            app.component('v-activities', {
                template: '#v-activities-template',
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
</x-admin::layouts>