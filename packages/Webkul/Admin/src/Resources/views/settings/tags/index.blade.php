<x-admin::layouts>
    <!-- Page Title -->
    <x-slot:title>
        @lang('admin::app.settings.tags.index.title')
    </x-slot>

    <div class="flex flex-col gap-4">
        <!-- Header Section -->
        <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
            <div class="flex flex-col gap-2">
                <div class="flex cursor-pointer items-center">
                    <!-- Breadcrumbs -->
                    <x-admin::breadcrumbs name="settings.tags" />
                </div>

                <div class="text-xl font-bold dark:text-white">
                    @lang('admin::app.settings.tags.index.title')
                </div>
            </div>

            <div class="flex items-center gap-x-2.5">
                {!! view_render_event('krayin.admin.settings.tags.index.create_button.before') !!}
                
                <!-- Create button for Tags -->
                @if (bouncer()->hasPermission('settings.other_settings.tags.create'))
                    <div class="flex items-center gap-x-2.5">
                        <button
                            type="button"
                            class="primary-button"
                            @click="$refs.tagSettings.openModal()"
                        >
                            @lang('admin::app.settings.tags.index.create-btn')
                        </button>
                    </div>
                @endif

                {!! view_render_event('krayin.admin.settings.tags.index.create_button.after') !!}
            </div>
        </div>
        
        <v-tag-settings ref="tagSettings">
            <!-- DataGrid Shimmer -->
            <x-admin::shimmer.datagrid />
        </v-tag-settings>
    </div>

    @pushOnce('scripts')
        <script
            type="text/x-template"
            id="tag-settings-template"
        >
            {!! view_render_event('krayin.admin.settings.tags.index.datagrid.before') !!}
        
            <!-- Datagrid -->
            <x-admin::datagrid
                src="{{ route('admin.settings.tags.index') }}"
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
                            <!-- Mass Actions, Title and Created By -->
                            <div class="flex select-none items-center gap-16">
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
                            </div>
                            
                            <!-- Tag ID -->
                            <p>@{{ record.id }}</p>
        
                            <!-- Tag Name -->
                            <p v-html="record.name"></p>


                            {{-- <p>@{{ record.name }}</p> --}}

                            <!-- Tag User Name -->
                            <p>@{{ record.user_name }}</p>

                            <!-- Tag Created Date -->
                            <p>@{{ record.created_at }}</p>

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
            {!! view_render_event('krayin.admin.settings.tags.index.datagrid.after') !!}
            
            <x-admin::form
                v-slot="{ meta, errors, handleSubmit }"
                as="div"
                ref="modalForm"
            >
                <form @submit="handleSubmit($event, updateOrCreate)">
                    {!! view_render_event('krayin.admin.settings.tags.index.form_controls.before') !!}

                    <x-admin::modal ref="tagsUpdateAndCreateModal">
                        <!-- Modal Header -->
                        <x-slot:header>
                            <p class="text-lg font-bold text-gray-800 dark:text-white">
                                @{{ 
                                    selectedType
                                    ? "@lang('admin::app.settings.tags.index.edit.title')" 
                                    : "@lang('admin::app.settings.tags.index.create.title')"
                                }}
                            </p>
                        </x-slot>

                        <!-- Modal Content -->
                        <x-slot:content>
                            {!! view_render_event('krayin.admin.settings.tags.index.content.before') !!}

                            <x-admin::form.control-group.control
                                type="hidden"
                                name="id"
                            />

                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label class="required">
                                    @lang('admin::app.settings.tags.index.create.name')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="text"
                                    id="name"
                                    name="name"
                                    rules="required"
                                    :label="trans('admin::app.settings.tags.index.create.name')"
                                    :placeholder="trans('admin::app.settings.tags.index.create.name')"
                                />

                                <x-admin::form.control-group.error control-name="name" />
                            </x-admin::form.control-group>

                            <x-admin::form.control-group.label>
                                @lang('admin::app.settings.tags.index.create.color')
                            </x-admin::form.control-group.label>
                            
                            <div class="flex gap-3">
                                <span class="relative inline-block">
                                    <x-admin::form.control-group.control
                                        type="radio" 
                                        id="blue-500" 
                                        name="color" 
                                        value="blue-500" 
                                        class="peer absolute left-0 right-3 top-5 z-10 h-full w-full cursor-pointer opacity-0"
                                    />
                                    <label 
                                        for="blue-500" 
                                        class="block h-6 w-6 cursor-pointer rounded-full bg-blue-500 shadow-md transition duration-200 ease-in-out peer-checked:border-4 peer-checked:border-solid peer-checked:border-white"
                                    >
                                    </label>
                                </span>
                            
                                <span class="relative inline-block">
                                    <x-admin::form.control-group.control
                                        type="radio" 
                                        id="yellow-400" 
                                        name="color" 
                                        value="yellow-400" 
                                        class="peer absolute left-0 right-3 top-5 z-10 h-full w-full cursor-pointer opacity-0"
                                    />
                                    <label 
                                        for="yellow-400" 
                                        class="inline-block h-6 w-6 cursor-pointer rounded-full bg-yellow-400 shadow-md transition duration-200 ease-in-out peer-checked:border-4 peer-checked:border-solid peer-checked:border-white"
                                    >
                                    </label>
                                </span>
                            
                                <span class="relative inline-block">
                                    <x-admin::form.control-group.control 
                                        type="radio" 
                                        id="pink-500" 
                                        name="color" 
                                        value="pink-500" 
                                        class="peer absolute left-0 right-3 top-5 z-10 h-full w-full cursor-pointer opacity-0"
                                    />
                                    <label 
                                        for="pink-500" 
                                        class="inline-block h-6 w-6 cursor-pointer rounded-full bg-pink-500 shadow-md transition duration-200 ease-in-out peer-checked:border-4 peer-checked:border-solid peer-checked:border-white"
                                    >
                                    </label>
                                </span>
                            
                                <span class="relative inline-block">
                                    <x-admin::form.control-group.control 
                                        type="radio" 
                                        id="cyan-500" 
                                        name="color" 
                                        value="cyan-500" 
                                        class="peer absolute left-0 right-3 top-5 z-10 h-full w-full cursor-pointer opacity-0"
                                    />
                                    <label 
                                        for="cyan-500"    
                                        class="inline-block h-6 w-6 cursor-pointer rounded-full bg-cyan-500 shadow-md transition duration-200 ease-in-out peer-checked:border-4 peer-checked:border-solid peer-checked:border-white"
                                    >
                                    </label>
                                </span>
                            
                                <span class="relative inline-block">
                                    <x-admin::form.control-group.control 
                                        type="radio" 
                                        id="orange-500" 
                                        name="color" 
                                        value="orange-500" 
                                        class="peer absolute left-0 right-3 top-5 z-10 h-full w-full cursor-pointer opacity-0"
                                    />
                                    <label 
                                        for="orange-500" 
                                        class="inline-block h-6 w-6 cursor-pointer rounded-full bg-orange-500 shadow-md transition duration-200 ease-in-out peer-checked:border-4 peer-checked:border-solid peer-checked:border-white"
                                        >
                                    </label>
                                </span>
                            
                                <span class="mr-1.25 relative inline-block">
                                    <x-admin::form.control-group.control 
                                        type="radio" 
                                        id="green-500" 
                                        name="color" 
                                        value="green-500" 
                                        class="peer absolute left-0 right-3 top-5 z-10 h-full w-full cursor-pointer opacity-0"
                                    />
                                    <label 
                                        for="green-500" 
                                        class="inline-block h-6 w-6 cursor-pointer rounded-full bg-green-500 shadow-md transition duration-200 ease-in-out peer-checked:border-4 peer-checked:border-solid peer-checked:border-white"
                                    >
                                    </label>
                                </span>
                            </div>

                            {!! view_render_event('krayin.admin.settings.tags.index.content.after') !!}
                        </x-slot>

                        <!-- Modal Footer -->
                        <x-slot:footer>
                            <!-- Save Button -->
                            <x-admin::button
                                button-type="submit"
                                class="primary-button justify-center"
                                :title="trans('admin::app.settings.tags.index.create.save-btn')"
                                ::loading="isProcessing"
                                ::disabled="isProcessing"
                            />
                        </x-slot>
                    </x-admin::modal>

                    {!! view_render_event('krayin.admin.settings.tags.index.form_controls.after') !!}
                </form>
            </x-admin::form>
        </script>

        <script type="module">
            app.component('v-tag-settings', {
                template: '#tag-settings-template',
        
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
                        this.$refs.tagsUpdateAndCreateModal.toggle();
                    },
                    
                    updateOrCreate(params, {resetForm, setErrors}) {
                        this.isProcessing = true;

                        this.$axios.post(params.id ? `{{ route('admin.settings.tags.update', '') }}/${params.id}` : "{{ route('admin.settings.tags.store') }}", {
                            ...params,
                            _method: params.id ? 'put' : 'post'
                        },

                        ).then(response => {
                            this.isProcessing = false;

                            this.$refs.tagsUpdateAndCreateModal.toggle();

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
                                
                                this.$refs.tagsUpdateAndCreateModal.toggle();
                            })
                            .catch(error => {});
                    },
                },
            });
        </script>
    @endPushOnce
</x-admin::layouts>
