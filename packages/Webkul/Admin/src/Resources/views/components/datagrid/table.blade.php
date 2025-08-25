@props(['isMultiRow' => false])

<v-datagrid-table
    :is-loading="isLoading"
    :available="available"
    :applied="applied"
    @selectAll="selectAll"
    @sort="sort"
    @actionSuccess="get"
>
    {{ $slot }}
</v-datagrid-table>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-datagrid-table-template"
    >
        <div class="w-full">
            <!-- Table view for larger screens, Card view for mobile -->
            <div class="table-responsive box-shadow rounded-t-0 grid w-full overflow-hidden border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                <!-- Table Header - Always visible on all screens -->
                <slot
                    name="header"
                    :is-loading="isLoading"
                    :available="available"
                    :applied="applied"
                    :select-all="selectAll"
                    :sort="sort"
                    :perform-action="performAction"
                >
                    <template v-if="isLoading">
                        <x-admin::shimmer.datagrid.table.head :isMultiRow="$isMultiRow" />
                    </template>

                    <template v-else>
                        <div
                            class="row grid min-h-[47px] items-center gap-2.5 border-b bg-gray-50 px-4 py-2.5 text-black dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 max-lg:hidden"
                            :style="`grid-template-columns: repeat(${gridsCount}, minmax(0, 1fr))`"
                        >
                            <!-- Mass Actions -->
                            <p v-if="available.massActions.length">
                                <label for="mass_action_select_all_records">
                                    <input
                                        type="checkbox"
                                        name="mass_action_select_all_records"
                                        id="mass_action_select_all_records"
                                        class="peer hidden"
                                        :checked="['all', 'partial'].includes(applied.massActions.meta.mode)"
                                        @change="selectAll"
                                    >

                                    <span
                                        class="icon-checkbox-outline cursor-pointer rounded-md text-2xl text-gray-500 peer-checked:text-brandColor"
                                        :class="[
                                            applied.massActions.meta.mode === 'all' ? 'peer-checked:icon-checkbox-select peer-checked:text-brandColor ' : (
                                                applied.massActions.meta.mode === 'partial' ? 'peer-checked:icon-checkbox-multiple peer-checked:brandColor' : ''
                                            ),
                                        ]"
                                    >
                                    </span>
                                </label>
                            </p>

                            <!-- Columns -->
                            <template v-for="column in available.columns">
                                <div
                                    class="flex items-center gap-1.5 break-words"
                                    :class="{'cursor-pointer select-none hover:text-gray-800 dark:hover:text-white': column.sortable}"
                                    @click="sort(column)"
                                    v-if="column.visibility"
                                > 
                                    <p v-html="column.label"></p>

                                    <i
                                        class="align-text-bottom text-base text-gray-600 dark:text-gray-300"
                                        :class="[applied.sort.order === 'asc' ? 'icon-stats-down': 'icon-stats-up']"
                                        v-if="column.index == applied.sort.column"
                                    ></i>
                                </div>
                            </template>

                            <!-- Actions -->
                            <p
                                class="text-end"
                                v-if="available.actions.length"
                            >
                                @lang('admin::app.components.datagrid.table.actions')
                            </p>
                        </div>
                        
                        <!-- Mobile Sort/Filter Header -->
                        <div class="hidden border-b bg-gray-50 px-4 py-3 text-black dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 max-lg:block">
                            <div class="flex items-center justify-between">
                                <!-- Mass Actions for Mobile -->
                                <div v-if="available.massActions.length">
                                    <label for="mass_action_select_all_records">
                                        <input
                                            type="checkbox"
                                            name="mass_action_select_all_records"
                                            id="mass_action_select_all_records"
                                            class="peer hidden"
                                            :checked="['all', 'partial'].includes(applied.massActions.meta.mode)"
                                            @change="selectAll"
                                        >
    
                                        <span
                                            class="icon-checkbox-outline cursor-pointer rounded-md text-2xl text-gray-500 peer-checked:text-brandColor"
                                            :class="[
                                                applied.massActions.meta.mode === 'all' ? 'peer-checked:icon-checkbox-select peer-checked:text-brandColor ' : (
                                                    applied.massActions.meta.mode === 'partial' ? 'peer-checked:icon-checkbox-multiple peer-checked:brandColor' : ''
                                                ),
                                            ]"
                                        >
                                        </span>
                                    </label>
                                </div>
                                
                                <!-- Mobile Sort Dropdown -->
                                <div class="flex w-full justify-end" v-if="available.columns.some(column => column.sortable)">
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
                </slot>

                <slot
                    name="body"
                    :is-loading="isLoading"
                    :available="available"
                    :applied="applied"
                    :select-all="selectAll"
                    :sort="sort"
                    :perform-action="performAction"
                >
                    <template v-if="isLoading">
                        <x-admin::shimmer.datagrid.table.body :isMultiRow="$isMultiRow" />
                    </template>

                    <template v-else>
                        <template v-if="available.records.length">
                            <!-- Desktop View -->
                            <div
                                class="row grid items-center gap-2.5 border-b px-4 py-4 text-black transition-all hover:bg-gray-50 dark:border-gray-800 dark:text-gray-300 dark:hover:bg-gray-950 max-lg:hidden"
                                v-for="record in available.records"
                                :style="`grid-template-columns: repeat(${gridsCount}, minmax(0, 1fr))`"
                            >
                                <!-- Mass Actions -->
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

                                <!-- Columns -->
                                <template v-for="column in available.columns">
                                    <p
                                        class="break-words"
                                        v-html="record[column.index]"
                                        v-if="column.visibility"
                                    >
                                    </p>
                                </template>

                                <!-- Actions -->
                                <p
                                    class="flex h-full items-center place-self-end"
                                    v-if="available.actions.length"
                                >
                                    <span
                                        class="cursor-pointer rounded-md p-1.5 text-2xl transition-all hover:bg-gray-200 dark:hover:bg-gray-800 max-sm:place-self-center"
                                        :class="action.icon"
                                        v-text="! action.icon ? action.title : ''"
                                        v-for="action in record.actions"
                                        @click="performAction(action)"
                                    >
                                    </span>
                                </p>
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

                        <template v-else>
                            <div class="row grid border-b px-4 py-4 text-center text-gray-600 dark:border-gray-800 dark:text-gray-300">
                                <p>
                                    @lang('admin::app.components.datagrid.table.no-records-available')
                                </p>
                            </div>
                        </template>
                    </template>
                </slot>
            </div>
        </div>
    </script>

    <script type="module">
        app.component('v-datagrid-table', {
            template: '#v-datagrid-table-template',

            props: ['isLoading', 'available', 'applied'],
            
            computed: {
                gridsCount() {
                    let count = this.available.columns.filter((column) => column.visibility).length;

                    if (this.available.actions.length) {
                        ++count;
                    }

                    if (this.available.massActions.length) {
                        ++count;
                    }

                    return count;
                },
            },

            methods: {
                /**
                 * Select all records in the datagrid.
                 *
                 * @returns {void}
                 */
                selectAll() {
                    this.$emit('selectAll');
                },

                /**
                 * Perform a sorting operation on the specified column.
                 *
                 * @param {object} column
                 * @returns {void}
                 */
                sort(column) {
                    this.$emit('sort', column);
                },

                /**
                 * Perform the specified action.
                 *
                 * @param {object} action
                 * @returns {void}
                 */
                performAction(action) {
                    const method = action.method.toLowerCase();

                    switch (method) {
                        case 'get':
                            window.location.href = action.url;

                            break;

                        case 'post':
                        case 'put':
                        case 'patch':
                        case 'delete':
                            this.$emitter.emit('open-confirm-modal', {
                                agree: () => {
                                    this.$axios[method](action.url)
                                        .then(response => {
                                            this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });

                                            this.$emit('actionSuccess', response.data);
                                        })
                                        .catch((error) => {
                                            this.$emitter.emit('add-flash', { type: 'error', message: error.response.data.message });

                                            this.$emit('actionError', error.response.data);
                                        });
                                }
                            });

                            break;

                        default:
                            console.error('Method not supported.');

                            break;
                    }
                },
            },
        });
    </script>
@endpushOnce
