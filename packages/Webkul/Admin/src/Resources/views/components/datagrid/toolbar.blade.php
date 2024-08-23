<template v-if="isLoading">
    <x-admin::shimmer.datagrid.toolbar />
</template>

<template v-else>
    <div class="flex h-[58px] items-center justify-between gap-4 rounded-t-lg border border-b-0 border-gray-200 bg-white pl-2 pr-4 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 max-md:flex-wrap">
        <!-- Left Toolbar -->
        <div class="flex gap-x-1">
            {{ $toolbarLeftBefore }}
            
            <!-- Mass Actions Panel -->
            <transition-group
                tag='div'
                name="flash-group"
                enter-from-class="ltr:translate-y-full rtl:-translate-y-full"
                enter-active-class="transform transition duration-300 ease-[cubic-bezier(.4,0,.2,1)]"
                enter-to-class="ltr:translate-y-0 rtl:-translate-y-0"
                leave-from-class="ltr:translate-y-0 rtl:-translate-y-0"
                leave-active-class="transform transition duration-300 ease-[cubic-bezier(.4,0,.2,1)]"
                leave-to-class="ltr:translate-y-full rtl:-translate-y-full"
                class='fixed bottom-10 left-1/2 z-[10003] grid -translate-x-1/2 justify-items-end gap-2.5'
            >
                <div v-if="applied.massActions.indices.length">
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
                </div>
            </transition-group>

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

            {{ $toolbarLeftAfter }}
        </div>

        <!-- Right Toolbar -->
        <div class="flex gap-x-4">
            {{ $toolbarRightBefore }}
            
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

            {{ $toolbarRightAfter }}
        </div>
    </div>
</template>
