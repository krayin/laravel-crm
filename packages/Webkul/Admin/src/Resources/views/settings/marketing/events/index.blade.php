<x-admin::layouts>
    <!-- Page Title -->
    <x-slot:title>
        @lang('admin::app.settings.marketing.events.index.title')
    </x-slot>

    <div class="flex flex-col gap-4">
        <!-- Header section -->
        <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
            <div class="flex flex-col gap-2">
                <div class="flex cursor-pointer items-center">
                    {!! view_render_event('admin.settings.marketing-events.index.breadcrumbs.before') !!}

                    <!-- Bredcrumbs -->
                    <x-admin::breadcrumbs name="settings.marketing_events" />

                    {!! view_render_event('admin.settings.marketing-events.index.breadcrumbs.after') !!}
                </div>

                <div class="text-xl font-bold dark:text-gray-300">
                    @lang('admin::app.settings.marketing.events.index.title')
                </div>
            </div>

            <div class="flex items-center gap-x-2.5">                
                <!-- Create button for Marketing Event -->
                <div class="flex items-center gap-x-2.5">
                    {!! view_render_event('admin.settings.marketing-events.index.breadcrumbs.after') !!}

                    <button
                        type="button"
                        class="primary-button"
                        @click="$refs.marketingEvent.actionType = 'create';$refs.marketingEvent.toggleModal()"
                    >
                        @lang('admin::app.settings.marketing.events.index.create-btn')
                    </button>

                    {!! view_render_event('admin.settings.marketing-events.index.create_button.after') !!}
                </div>
            </div>
        </div>
        
        <v-marketing-events ref="marketingEvent">
            <x-admin::shimmer.datagrid />
        </v-marketing-events>
    </div>

    @pushOnce('scripts')
        <script 
            type="text/x-template" 
            id="v-marketing-events-template"
        >
            <div>
                <!-- Datagrid -->
                <x-admin::datagrid
                    :src="route('admin.settings.marketing_events.index')"
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
                                
                                <!-- Marketing Event Id -->
                                <p>@{{ record.id }}</p>
            
                                <!-- Marketing Event Name -->
                                <p>@{{ record.name }}</p>

                                <!-- Marketing Event Description -->
                                <p>@{{ record.description }}</p>

                                <!-- Marketing Event Date -->
                                <p>@{{ record.date }}</p>

                                <!-- Actions -->
                                <div class="flex justify-end">
                                    <a @click.prevent="actionType = 'edit';edit(record)">
                                        <span
                                            :class="record.actions.find(action => action.index === 'edit')?.icon"
                                            class="cursor-pointer rounded-md p-1.5 text-2xl transition-all hover:bg-gray-200 dark:hover:bg-gray-800 max-sm:place-self-center"
                                        >
                                        </span>
                                    </a>

                                    <a @click.prevent="performAction(record.actions.find(action => action.index === 'delete'))">
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

                <Teleport to="body">
                    {!! view_render_event('admin.settings.marketing-events.index.form_controls.before') !!}
        
                    <x-admin::form
                        v-slot="{ meta, errors, handleSubmit }"
                        as="div"
                        ref="eventForm"
                    >
                        <form @submit="handleSubmit($event, createOrUpdate)">
                            {!! view_render_event('admin.settings.marketing-events.index.form_controls.modal.before') !!}
        
                            <x-admin::modal ref="marketingModal">
                                <x-slot:header>
                                    {!! view_render_event('admin.settings.marketing-events.index.form_controls.modal.header.dropdown.before') !!}
        
                                    <p class="text-lg font-bold text-gray-800 dark:text-white">
                                        @{{ 
                                            actionType == 'create'
                                            ? "@lang('admin::app.settings.marketing.events.index.create.title')"
                                            : "@lang('admin::app.settings.marketing.events.index.edit.title')" 
                                        }}
                                    </p>

                                    {!! view_render_event('admin.settings.marketing-events.index.form_controls.modal.header.dropdown.after') !!}
                                </x-slot>
        
                                <x-slot:content>
                                    {!! view_render_event('admin.settings.marketing-events.index.form_controls.modal.content.controls.before') !!}
        
                                    <!-- Name -->
                                    <x-admin::form.control-group>
                                        <x-admin::form.control-group.label class="required">
                                            @lang('admin::app.settings.marketing.events.index.create.name')
                                        </x-admin::form.control-group.label>
                                        
                                        <x-admin::form.control-group.control
                                            type="hidden"
                                            name="id"
                                        />

                                        <x-admin::form.control-group.control
                                            type="text"
                                            name="name"
                                            rules="required"
                                            :label="trans('admin::app.settings.marketing-events.index.create.name')"
                                        />
        
                                        <x-admin::form.control-group.error control-name="name" />
                                    </x-admin::form.control-group>
        
                                    <!-- Description -->
                                    <x-admin::form.control-group>
                                        <x-admin::form.control-group.label class="required">
                                            @lang('admin::app.settings.marketing.events.index.create.description')
                                        </x-admin::form.control-group.label>
                                        
                                        <x-admin::form.control-group.control
                                            type="textarea"
                                            name="description"
                                            rules="required"
                                            rows="4"
                                            :label="trans('admin::app.settings.marketing-events.index.create.description')"
                                        />
        
                                        <x-admin::form.control-group.error control-name="description" />
                                    </x-admin::form.control-group>

                                    <!-- Date -->
                                    <x-admin::form.control-group>
                                        <x-admin::form.control-group.label class="required">
                                            @lang('admin::app.settings.marketing.events.index.create.date')
                                        </x-admin::form.control-group.label>
                                        
                                        <x-admin::form.control-group.control
                                            type="date"
                                            name="date"
                                            rules="required"
                                            :label="trans('admin::app.settings.marketing-events.index.create.date')"
                                        />
        
                                        <x-admin::form.control-group.error control-name="date" />
                                    </x-admin::form.control-group>

                                    {!! view_render_event('admin.settings.marketing-events.index.form_controls.modal.content.controls.after') !!}
                                </x-slot>
        
                                <x-slot:footer>
                                    {!! view_render_event('admin.components.activities.actions.activity.form_controls.modal.footer.save_button.before') !!}
        
                                    <x-admin::button
                                        type="submit"
                                        class="primary-button"
                                        :title="trans('admin::app.components.activities.actions.activity.save-btn')"
                                        ::loading="isStoring"
                                        ::disabled="isStoring"
                                    />
        
                                    {!! view_render_event('admin.components.activities.actions.activity.form_controls.modal.footer.save_button.after') !!}
                                </x-slot>
                            </x-admin::modal>
        
                            {!! view_render_event('admin.components.activities.actions.activity.form_controls.modal.after') !!}
                        </form>
                    </x-admin::form>
        
                    {!! view_render_event('admin.components.activities.actions.activity.form_controls.after') !!}
                </Teleport>
            </div>
        </script>

        <script type="module">
            app.component('v-marketing-events', {
                template: '#v-marketing-events-template',

                data() {
                    return {
                        isStoring: false,

                        actionType: 'create',   
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
                    toggleModal() {
                        this.$refs.marketingModal.toggle();
                    },

                    createOrUpdate(paramas, { resetForm, setErrors }) {
                        this.isStoring = true;

                        if (paramas.id) {
                            paramas._method = 'PUT';
                        }

                        this.$axios.post(
                            paramas.id 
                            ? `{{ route('admin.settings.marketing_events.update', '') }}/${paramas.id}`
                            : '{{ route('admin.settings.marketing_events.store') }}', paramas
                        )
                            .then(response => {
                                this.isStoring = false;

                                this.$refs.marketingModal.toggle();

                                this.$refs.datagrid.get();

                                this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });
                            })
                            .catch(error => {
                                this.isStoring = false;

                                setErrors(error.response.data.errors);
                            });
                    },

                    edit(record) {
                        this.$refs.eventForm.setValues({...record });

                        this.toggleModal();
                    },
                },
            });
        </script>
    @endPushOnce
</x-admin::layouts>
