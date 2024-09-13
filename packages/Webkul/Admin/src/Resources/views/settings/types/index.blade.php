<x-admin::layouts>
    <x-slot:title>
        @lang('admin::app.settings.types.index.title')
    </x-slot>

    <div class="flex flex-col gap-4">
        <!-- Header Section -->
        <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
            <div class="flex flex-col gap-2">
                <div class="flex cursor-pointer items-center">
                    <!-- Breadcrumbs -->
                    <x-admin::breadcrumbs name="settings.types" />
                </div>

                <div class="text-xl font-bold dark:text-gray-300">
                    @lang('admin::app.settings.types.index.title')
                </div>
            </div>

            <div class="flex items-center gap-x-2.5">
                {!! view_render_event('admin.settings.types.index.create_button.before') !!}
                
                <!-- Create button for Types -->
                <div class="flex items-center gap-x-2.5">
                    @if (bouncer()->hasPermission('settings.lead.types.create'))
                        <button
                            type="button"
                            class="primary-button"
                            @click="$refs.typeSettings.openModal()"
                        >
                            @lang('admin::app.settings.types.index.create-btn')
                        </button>
                    @endif
                </div>

                {!! view_render_event('admin.settings.types.index.create_button.after') !!}
            </div>
        </div>

        <v-types-settings ref="typeSettings">
            <!-- DataGrid Shimmer -->
            <x-admin::shimmer.datagrid />
        </v-types-settings>
    </div>
    
    @pushOnce('scripts')
        <script
            type="text/x-template"
            id="types-settings-template"
        >
            {!! view_render_event('admin.settings.types.index.datagrid.before') !!}
        
            <!-- Datagrid -->
            <x-admin::datagrid
                :src="route('admin.settings.types.index')"
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
                            class="row grid items-center gap-2.5 border-b px-4 py-4 text-gray-600 transition-all hover:bg-gray-50 dark:border-gray-800 dark:text-gray-300 dark:hover:bg-gray-950"
                            :style="`grid-template-columns: repeat(${gridsCount}, minmax(0, 1fr))`"
                        >
                            <!-- Type ID -->
                            <p>@{{ record.id }}</p>
        
                            <!-- Type Name -->
                            <p>@{{ record.name }}</p>

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
                    </template>
                </template>
            </x-admin::datagrid>

            {!! view_render_event('admin.settings.types.index.datagrid.after') !!}
            
            <x-admin::form
                v-slot="{ meta, errors, handleSubmit }"
                as="div"
                ref="modalForm"
            >
                <form @submit="handleSubmit($event, updateOrCreate)">
                    {!! view_render_event('admin.settings.types.index.form_controls.before') !!}

                    <x-admin::modal ref="typeUpdateAndCreateModal">
                        <!-- Modal Header -->
                        <x-slot:header>
                            <p class="text-lg font-bold text-gray-800 dark:text-white">
                                @{{ 
                                    selectedType
                                    ? "@lang('admin::app.settings.types.index.edit.title')" 
                                    : "@lang('admin::app.settings.types.index.create.title')"
                                }}
                            </p>
                        </x-slot>

                        <!-- Modal Content -->
                        <x-slot:content>
                            {!! view_render_event('admin.settings.types.index.content.before') !!}

                            <x-admin::form.control-group.control
                                type="hidden"
                                name="id"
                            />

                            <!-- Name -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label class="required">
                                    @lang('admin::app.settings.types.index.create.name')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="text"
                                    id="name"
                                    name="name"
                                    rules="required"
                                    :label="trans('admin::app.settings.types.index.create.name')"
                                    :placeholder="trans('admin::app.settings.types.index.create.name')"
                                />

                                <x-admin::form.control-group.error control-name="name" />
                            </x-admin::form.control-group>

                            {!! view_render_event('admin.settings.types.index.content.after') !!}
                        </x-slot>

                        <!-- Modal Footer -->
                        <x-slot:footer>
                            {!! view_render_event('admin.settings.types.index.footer.create_button.before') !!}

                            <!-- Save Button -->
                            <x-admin::button
                                button-type="submit"
                                class="primary-button justify-center"
                                :title="trans('admin::app.settings.types.index.create.save-btn')"
                                ::loading="isProcessing"
                                ::disabled="isProcessing"
                            />

                            {!! view_render_event('admin.settings.types.index.footer.create_button.after') !!}
                        </x-slot>
                    </x-admin::modal>

                    {!! view_render_event('admin.settings.types.index.form_controls.after') !!}
                </form>
            </x-admin::form>
        </script>

        <script type="module">
            app.component('v-types-settings', {
                template: '#types-settings-template',
        
                data() {
                    return {
                        isProcessing: false,
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
                        this.$refs.typeUpdateAndCreateModal.toggle();
                    },
                    
                    updateOrCreate(params, {resetForm, setErrors}) {
                        this.isProcessing = true;

                        this.$axios.post(params.id ? `{{ route('admin.settings.types.update', '') }}/${params.id}` : "{{ route('admin.settings.types.store') }}", {
                            ...params,
                            _method: params.id ? 'put' : 'post'
                        },

                        ).then(response => {
                            this.isProcessing = false;

                            this.$refs.typeUpdateAndCreateModal.toggle();

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
                                
                                this.$refs.typeUpdateAndCreateModal.toggle();
                            })
                            .catch(error => {});
                    },
                },
            });
        </script>
    @endPushOnce
</x-admin::layouts>
