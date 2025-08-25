<x-admin::layouts>
    <x-slot:title>
        @lang('admin::app.settings.sources.index.title')
    </x-slot>

    <div class="flex flex-col gap-4">
        <!-- Header section -->
        <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
            <div class="flex flex-col gap-2">
                {!! view_render_event('admin.settings.sources.index.breadcrumbs.before') !!}

                <!-- Breadcrumbs -->
                <x-admin::breadcrumbs name="settings.sources" />

                {!! view_render_event('admin.settings.sources.index.breadcrumbs.after') !!}

                <div class="text-xl font-bold dark:text-white">
                    @lang('admin::app.settings.sources.index.title')
                </div>
            </div>

            <div class="flex items-center gap-x-2.5">
                {!! view_render_event('admin.settings.sources.index.create_button.before') !!}
                
                <!-- Create button for Sources -->
                @if (bouncer()->hasPermission('settings.lead.sources.create'))
                    <div class="flex items-center gap-x-2.5">
                        <button
                            type="button"
                            class="primary-button"
                            @click="$refs.sourceSettings.openModal()"
                        >
                            @lang('admin::app.settings.sources.index.create-btn')
                        </button>
                    </div>
                @endif

                {!! view_render_event('admin.settings.sources.index.create_button.after') !!}
            </div>
        </div>
        
        <v-sources-settings ref="sourceSettings">
            <!-- DataGrid Shimmer -->
            <x-admin::shimmer.datagrid />
        </v-sources-settings>
    </div>

    @pushOnce('scripts')
        <script
            type="text/x-template"
            id="sources-settings-template"
        >
            {!! view_render_event('admin.settings.sources.index.datagrid.before') !!}
        
            <!-- Datagrid -->
            <x-admin::datagrid
                :src="route('admin.settings.sources.index')"
                ref="datagrid"
            >
                <template #body="{
                    isLoading,
                    available,
                    applied,
                    selectAll,
                    sort,
                    performAction
                }">
                    <template v-if="isLoading">
                        <x-admin::shimmer.datagrid.table.body />
                    </template>
        
                    <template v-else>
                        <div
                            v-for="record in available.records"
                            class="row grid items-center gap-2.5 border-b px-4 py-4 text-gray-600 transition-all hover:bg-gray-50 dark:border-gray-800 dark:text-gray-300 dark:hover:bg-gray-950 max-lg:hidden"
                            :style="`grid-template-columns: repeat(${gridsCount}, minmax(0, 1fr))`"
                        >
                            <!-- Sources ID -->
                            <p>@{{ record.id }}</p>
        
                            <!-- Sources Name -->
                            <p class="break-words">@{{ record.name }}</p>

                            <!-- Actions -->
                            <div class="flex justify-end">
                                <a @click="selectedType=true; editModal(record.actions.find(action => action.index === 'edit')?.url)">
                                    <span
                                        :class="record.actions.find(action => action.index === 'edit')?.icon"
                                        class="cursor-pointer rounded-md p-1.5 text-2xl transition-all hover:bg-gray-200 dark:hover:bg-gray-800 max-sm:place-self-center"
                                    >
                                    </span>
                                </a>
    
                                <a @click="performAction(record.actions.find(action => action.index === 'delete'))">
                                    <span
                                        :class="record.actions.find(action => action.index === 'delete')?.icon"
                                        class="cursor-pointer rounded-md p-1.5 text-2xl transition-all hover:bg-gray-200 dark:hover:bg-gray-800 max-sm:place-self-center"
                                    >
                                    </span>
                                </a>
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
                                        <a @click="selectedType=true; editModal(record.actions.find(action => action.index === 'edit')?.url)">
                                            <span
                                                :class="record.actions.find(action => action.index === 'edit')?.icon"
                                                class="cursor-pointer rounded-md p-1.5 text-2xl transition-all hover:bg-gray-200 dark:hover:bg-gray-800 max-sm:place-self-center"
                                            >
                                            </span>
                                        </a>
            
                                        <a @click="performAction(record.actions.find(action => action.index === 'delete'))">
                                            <span
                                                :class="record.actions.find(action => action.index === 'delete')?.icon"
                                                class="cursor-pointer rounded-md p-1.5 text-2xl transition-all hover:bg-gray-200 dark:hover:bg-gray-800 max-sm:place-self-center"
                                            >
                                            </span>
                                        </a>
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

            {!! view_render_event('admin.settings.sources.index.datagrid.after') !!}
            
            <x-admin::form
                v-slot="{ meta, errors, handleSubmit }"
                as="div"
                ref="modalForm"
            >
                <form @submit="handleSubmit($event, updateOrCreate)">
                    {!! view_render_event('admin.settings.sources.index.form_controls.before') !!}

                    <x-admin::modal ref="sourceUpdateAndCreateModal">
                        <!-- Modal Header -->
                        <x-slot:header>
                            <p class="text-lg font-bold text-gray-800 dark:text-white">
                                @{{ 
                                    selectedType
                                    ? "@lang('admin::app.settings.sources.index.edit.title')" 
                                    : "@lang('admin::app.settings.sources.index.create.title')"
                                }}
                            </p>
                        </x-slot>

                        <!-- Modal Content -->
                        <x-slot:content>
                            {!! view_render_event('admin.settings.sources.index.content.before') !!}

                            <x-admin::form.control-group.control
                                type="hidden"
                                name="id"
                            />

                            {!! view_render_event('admin.settings.sources.index.form.name.before') !!}

                            <!-- Name -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label class="required">
                                    @lang('admin::app.settings.sources.index.create.name')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="text"
                                    id="name"
                                    name="name"
                                    rules="required"
                                    :label="trans('admin::app.settings.sources.index.create.name')"
                                    :placeholder="trans('admin::app.settings.sources.index.create.name')"
                                />

                                <x-admin::form.control-group.error control-name="name" />
                            </x-admin::form.control-group>

                            {!! view_render_event('admin.settings.sources.index.form.name.after') !!}
                        </x-slot>

                        <!-- Modal Footer -->
                        <x-slot:footer>
                            {!! view_render_event('admin.settings.sources.index.form.save_button.before') !!}

                            <!-- Save Button -->
                            <x-admin::button
                                button-type="submit"
                                class="primary-button justify-center"
                                :title="trans('admin::app.settings.sources.index.create.save-btn')"
                                ::loading="isProcessing"
                                ::disabled="isProcessing"
                            />

                            {!! view_render_event('admin.settings.sources.index.form.save_button.after') !!}
                        </x-slot>
                    </x-admin::modal>

                    {!! view_render_event('admin.settings.sources.index.form_controls.after') !!}
                </form>
            </x-admin::form>
        </script>

        <script type="module">
            app.component('v-sources-settings', {
                template: '#sources-settings-template',
        
                data() {
                    return {
                        isProcessing: false,
                        
                        selectedType: false,
                    };
                },
        
                computed: {
                    gridsCount() {
                        let count = this.$refs.datagrid.available.columns.length;

                        if (this.$refs.datagrid.available.actions.length) {
                            ++count;
                        }

                        if (this.$refs.datagrid.available.massActions.length) {
                            ++count;
                        }

                        return count;
                    },
                },

                methods: {
                    openModal() {
                        this.selectedType = false;
                        
                        this.$refs.sourceUpdateAndCreateModal.toggle();
                    },
                    
                    updateOrCreate(params, {resetForm, setErrors}) {
                        this.isProcessing = true;

                        this.$axios.post(params.id ? `{{ route('admin.settings.sources.update', '') }}/${params.id}` : "{{ route('admin.settings.sources.store') }}", {
                            ...params,
                            _method: params.id ? 'put' : 'post'
                        },

                        ).then(response => {
                            this.isProcessing = false;

                            this.$refs.sourceUpdateAndCreateModal.toggle();

                            this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });

                            this.$refs.datagrid.get();

                            resetForm();
                        }).catch(error => {
                            this.isProcessing = false;

                            if (error.response.status === 422) {
                                setErrors(error.response.data.errors);
                            }
                        });
                    },
                    
                    editModal(url) {
                        this.$axios.get(url)
                            .then(response => {
                                this.$refs.modalForm.setValues(response.data.data);
                                
                                this.$refs.sourceUpdateAndCreateModal.toggle();
                            })
                            .catch(error => {});
                    },
                },
            });
        </script>
    @endPushOnce
</x-admin::layouts>
