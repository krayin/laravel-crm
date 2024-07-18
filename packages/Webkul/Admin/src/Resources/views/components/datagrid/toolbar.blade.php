<template v-if="isLoading">
    <x-admin::shimmer.datagrid.toolbar />
</template>

<template v-else>
    <div class="mt-2 flex h-[58px] items-center justify-between gap-4 rounded-t-lg border border-b-0 border-gray-200 bg-white pl-2 pr-4 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 max-md:flex-wrap">
        <!-- Left Toolbar -->
        <div class="flex gap-x-1">
            <!-- Mass Actions Panel -->
            <template v-if="applied.massActions.indices.length">
                <x-admin::datagrid.toolbar.mass-action>
                    <template #mass-action="{
                        available,
                        applied,
                        massActions,
                        validateMassAction,
                        performMassAction
                    }">
                        <slot
                            name="mass-action"
                            :available="available"
                            :applied="applied"
                            :mass-actions="massActions"
                            :validate-mass-action="validateMassAction"
                            :perform-mass-action="performMassAction"
                        >
                        </slot>
                    </template>
                </x-admin::datagrid.toolbar.mass-action>
            </template>

            <!-- Search Panel -->
            <x-admin::datagrid.toolbar.search>
                <template #search="{
                    available,
                    applied,
                    search,
                    getSearchedValues,
                }">
                    <slot
                        name="search"
                        :available="available"
                        :applied="applied"
                        :search="search"
                        :get-searched-values="getSearchedValues"
                    >
                    </slot>
                </template>
            </x-admin::datagrid.toolbar.search>
        </div>

        <!-- Right Toolbar -->
        <div class="flex gap-x-4">   
            <!-- Pagination Panel -->
            <x-admin::datagrid.toolbar.pagination>
                <template #pagination="{
                    available,
                    applied,
                    changePage,
                    changePerPageOption
                }">
                    <slot
                        name="pagination"
                        :available="available"
                        :applied="applied"
                        :change-page="changePage"
                        :change-per-page-option="changePerPageOption"
                    >
                    </slot>
                </template>
            </x-admin::datagrid.toolbar.pagination>
        </div>
    </div>
</template>
