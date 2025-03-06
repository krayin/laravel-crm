<x-admin::layouts>
    <!-- Page Title -->
    <x-slot:title>
        @lang('admin::app.settings.groups.index.title')
    </x-slot>

    <div class="flex flex-col gap-4">
        <!-- Header section -->
        <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
            <div class="flex flex-col gap-2">
                <div class="flex cursor-pointer items-center">
                    {!! view_render_event('admin.settings.groups.index.breadcrumbs.before') !!}

                    <!-- Breadcrumbs -->
                    <x-admin::breadcrumbs name="settings.groups" />

                    {!! view_render_event('admin.settings.groups.index.breadcrumbs.after') !!}
                </div>

                <div class="text-xl font-bold dark:text-gray-300">
                    @lang('admin::app.settings.groups.index.title')
                </div>
            </div>

            <div class="flex items-center gap-x-2.5">                
                <!-- Create button for Group -->
                <div class="flex items-center gap-x-2.5">
                    {!! view_render_event('admin.settings.groups.index.breadcrumbs.after') !!}

                    @if (bouncer()->hasPermission('settings.user.groups.create'))
                        <button
                            type="button"
                            class="primary-button"
                            @click="$refs.groupSettings.openModal()"
                        >
                            @lang('admin::app.settings.groups.index.create-btn')
                        </button>
                    @endif

                    {!! view_render_event('admin.settings.groups.index.create_button.after') !!}
                </div>
            </div>
        </div>
        
        <v-group-settings ref="groupSettings">
            <!-- DataGrid Shimmer -->
            <x-admin::shimmer.datagrid />
        </v-group-settings>
    </div>

    @pushOnce('scripts')
        <script
            type="text/x-template"
            id="group-settings-template"
        >
            {!! view_render_event('admin.settings.groups.index.datagrid.before') !!}
        
            <!-- DataGrid -->
            <x-admin::datagrid
                :src="route('admin.settings.groups.index')"
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
                            <!-- Group ID -->
                            <p>@{{ record.id }}</p>
        
                            <!-- Group Name -->
                            <p>@{{ record.name }}</p>
        
                            <!-- Group Description -->
                            <p>@{{ record.description }}</p>
        
                            <!-- Actions -->
                            <div class="flex justify-end">
                                <a @click="selectedGroup=true; editModal(record.actions.find(action => action.index === 'edit')?.url)">
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

            {!! view_render_event('admin.settings.groups.index.datagrid.after') !!}

            {!! view_render_event('admin.settings.groups.index.form.before') !!}

            <x-admin::form
                v-slot="{ meta, errors, handleSubmit }"
                as="div"
                ref="modalForm"
            >
                <form @submit="handleSubmit($event, updateOrCreate)">
                    {!! view_render_event('admin.settings.groups.index.create_form_controls.before') !!}

                    {!! view_render_event('admin.settings.groups.index.form.modal.before') !!}

                    <x-admin::modal ref="groupUpdateAndCreateModal">
                        <!-- Modal Header -->
                        <x-slot:header>
                            <p class="text-lg font-bold text-gray-800 dark:text-white">
                                @{{ 
                                    selectedGroup
                                    ? "@lang('admin::app.settings.groups.index.edit.title')" 
                                    : "@lang('admin::app.settings.groups.index.create.title')"
                                }}
                            </p>
                        </x-slot>

                        <!-- Modal Content -->
                        <x-slot:content>
                            {!! view_render_event('admin.settings.groups.index.content.before') !!}

                            <x-admin::form.control-group.control
                                type="hidden"
                                name="id"
                            />

                            {!! view_render_event('admin.settings.groups.index.form.form_controls.name.before') !!}

                            <!-- Name -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label class="required">
                                    @lang('admin::app.settings.groups.index.create.name')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="text"
                                    id="name"
                                    name="name"
                                    rules="required|min:0|max:50"
                                    :label="trans('admin::app.settings.groups.index.create.name')"
                                    :placeholder="trans('admin::app.settings.groups.index.create.name')"
                                />

                                <x-admin::form.control-group.error control-name="name" />
                            </x-admin::form.control-group>

                            {!! view_render_event('admin.settings.groups.index.form.form_controls.name.after') !!}

                            {!! view_render_event('admin.settings.groups.index.form.form_controls.description.before') !!}

                            <!-- Description -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label class="required">
                                    @lang('admin::app.settings.groups.index.create.description')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="textarea"
                                    id="description"
                                    name="description"
                                    rules="required|max:250"
                                    :label="trans('admin::app.settings.groups.index.create.description')"
                                    :placeholder="trans('admin::app.settings.groups.index.create.description')"
                                />

                                <x-admin::form.control-group.error control-name="description" />
                            </x-admin::form.control-group>

                            {!! view_render_event('admin.settings.groups.index.form.form_controls.description.after') !!}
                        </x-slot>

                        <!-- Modal Footer -->
                        <x-slot:footer>
                            {!! view_render_event('admin.settings.groups.index.form.form_controls.save_button.before') !!}

                            <!-- Save Button -->
                            <x-admin::button
                                button-type="submit"
                                class="primary-button justify-center"
                                :title="trans('admin::app.settings.groups.index.create.save-btn')"
                                ::loading="isProcessing"
                                ::disabled="isProcessing"
                            />

                            {!! view_render_event('admin.settings.groups.index.form.form_controls.save_button.after') !!}
                        </x-slot>
                    </x-admin::modal>

                    {!! view_render_event('admin.settings.groups.index.form.modal.after') !!}
                </form>
            </x-admin::form>

            {!! view_render_event('admin.settings.groups.index.form.after') !!}
        </script>

        <script type="module">
            app.component('v-group-settings', {
                template: '#group-settings-template',
        
                data() {
                    return {
                        isProcessing: false,

                        selectedGroup: false,
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
                        this.selectedGroup=false;
                        
                        this.$refs.groupUpdateAndCreateModal.toggle();
                    },
                    
                    updateOrCreate(params, {resetForm, setErrors}) {
                        this.isProcessing = true;

                        this.$axios.post(params.id ? `{{ route('admin.settings.groups.update', '') }}/${params.id}` : "{{ route('admin.settings.groups.store') }}", {
                            ...params,
                            _method: params.id ? 'put' : 'post'
                        }, {
                            headers: {
                                'Content-Type': 'multipart/form-data',
                            }
                        }).then(response => {
                            this.isProcessing = false;

                            this.$refs.groupUpdateAndCreateModal.toggle();

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
                                
                                this.$refs.groupUpdateAndCreateModal.toggle();
                            })
                            .catch(error => {});
                    },
                },
            });
        </script>
    @endPushOnce
</x-admin::layouts>
