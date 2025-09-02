<x-admin::layouts>
    <!-- Page Title -->
    <x-slot:title>
        @lang('admin::app.settings.marketing.campaigns.index.title')
    </x-slot>

    <div class="flex flex-col gap-4">
        <!-- Header section -->
        <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
            <div class="flex flex-col gap-2">
                <div class="flex cursor-pointer items-center">
                    {!! view_render_event('admin.settings.marketing.campaigns.index.breadcrumbs.before') !!}

                    <!-- Bredcrumbs -->
                    <x-admin::breadcrumbs name="settings.marketing.campaigns" />

                    {!! view_render_event('admin.settings.marketing.campaigns.index.breadcrumbs.after') !!}
                </div>

                <div class="text-xl font-bold dark:text-gray-300">
                    @lang('admin::app.settings.marketing.campaigns.index.title')
                </div>
            </div>

            <div class="flex items-center gap-x-2.5">
                <!-- Create button for Campaings -->
                <div class="flex items-center gap-x-2.5">
                    {!! view_render_event('admin.settings.marketing.campaigns.index.breadcrumbs.after') !!}

                    @if (bouncer()->hasPermission('settings.automation.campaigns.create'))
                        <button
                            type="button"
                            class="primary-button"
                            @click="$refs.marketingCampaigns.handleCreate()"
                        >
                            @lang('admin::app.settings.marketing.campaigns.index.create-btn')
                        </button>
                    @endif

                    {!! view_render_event('admin.settings.marketing.campaigns.index.create_button.after') !!}
                </div>
            </div>
        </div>

        <v-campaigns ref="marketingCampaigns">
            <x-admin::shimmer.datagrid />
        </v-campaigns>
    </div>

    @pushOnce('scripts')
        <script
            type="text/x-template"
            id="v-campaigns-template"
        >
            <div>
                <!-- Datagrid -->
                <x-admin::datagrid
                    :src="route('admin.settings.marketing.campaigns.index')"
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
                                <!-- Mass Actions, Title and Created By -->
                                @if (bouncer()->hasPermission('settings.automation.campaigns.mass_delete'))
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
                                @endif

                                <!-- Id -->
                                <p>@{{ record.id }}</p>

                                <!-- Name -->
                                <p>@{{ record.name }}</p>

                                <!-- Subject -->
                                <p>@{{ record.subject }}</p>

                                <!-- Status -->
                                <span
                                    :class="record.status == 1 ? 'label-active' : 'label-inactive'"
                                >
                                    @{{ record.status == 1 ? '@lang('admin::app.settings.marketing.campaigns.index.datagrid.active')' : '@lang('admin::app.settings.marketing.campaigns.index.datagrid.inactive')' }}
                                </span>

                                <!-- Actions -->
                                <div class="flex justify-end">
                                    @if (bouncer()->hasPermission('settings.automation.campaigns.edit'))
                                        <a @click.prevent="actionType = 'edit';edit(record)">
                                            <span
                                                :class="record.actions.find(action => action.index === 'edit')?.icon"
                                                class="cursor-pointer rounded-md p-1.5 text-2xl transition-all hover:bg-gray-200 dark:hover:bg-gray-800 max-sm:place-self-center"
                                            >
                                            </span>
                                        </a>
                                    @endif

                                    @if (bouncer()->hasPermission('settings.automation.campaigns.delete'))
                                        <a @click.prevent="performAction(record.actions.find(action => action.index === 'delete'))">
                                            <span
                                                :class="record.actions.find(action => action.index === 'delete')?.icon"
                                                class="cursor-pointer rounded-md p-1.5 text-2xl transition-all hover:bg-gray-200 dark:hover:bg-gray-800 max-sm:place-self-center"
                                            >
                                            </span>
                                        </a>
                                    @endif
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
                                        @if (bouncer()->hasPermission('settings.automation.campaigns.mass_delete'))
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
                                        @endif

                                        <!-- Actions for Mobile -->
                                        <div
                                            class="flex w-full items-center justify-end"
                                            v-if="available.actions.length"
                                        >
                                            @if (bouncer()->hasPermission('settings.automation.campaigns.edit'))
                                                <a @click.prevent="actionType = 'edit';edit(record)">
                                                    <span
                                                        :class="record.actions.find(action => action.index === 'edit')?.icon"
                                                        class="cursor-pointer rounded-md p-1.5 text-2xl transition-all hover:bg-gray-200 dark:hover:bg-gray-800 max-sm:place-self-center"
                                                    >
                                                    </span>
                                                </a>
                                            @endif

                                            @if (bouncer()->hasPermission('settings.automation.campaigns.delete'))
                                                <a @click.prevent="performAction(record.actions.find(action => action.index === 'delete'))">
                                                    <span
                                                        :class="record.actions.find(action => action.index === 'delete')?.icon"
                                                        class="cursor-pointer rounded-md p-1.5 text-2xl transition-all hover:bg-gray-200 dark:hover:bg-gray-800 max-sm:place-self-center"
                                                    >
                                                    </span>
                                                </a>
                                            @endif
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

                <Teleport to="body">
                    {!! view_render_event('admin.settings.marketing.campaigns.index.form_controls.before') !!}

                    <x-admin::form
                        v-slot="{ meta, errors, handleSubmit }"
                        as="div"
                    >
                        <form
                            @submit="handleSubmit($event, createOrUpdate)"
                            ref="campaignForm"
                        >
                            {!! view_render_event('admin.settings.marketing.campaigns.index.form_controls.modal.before') !!}

                            <x-admin::modal ref="campaignModal">
                                <x-slot:header>
                                    {!! view_render_event('admin.settings.marketing.campaigns.index.form_controls.modal.header.dropdown.before') !!}

                                    <p class="text-lg font-bold text-gray-800 dark:text-white">
                                        @{{
                                            actionType == 'create'
                                            ? "@lang('admin::app.settings.marketing.campaigns.index.create.title')"
                                            : "@lang('admin::app.settings.marketing.campaigns.index.edit.title')"
                                        }}
                                    </p>

                                    {!! view_render_event('admin.settings.marketing.campaigns.index.form_controls.modal.header.dropdown.after') !!}
                                </x-slot>

                                <x-slot:content>
                                    {!! view_render_event('admin.settings.marketing.campaigns.index.form_controls.modal.content.controls.before') !!}

                                    <!-- Name -->
                                    <x-admin::form.control-group>
                                        <x-admin::form.control-group.label
                                            class="required"
                                            for="name"
                                        >
                                            @lang('admin::app.settings.marketing.campaigns.index.create.name')
                                        </x-admin::form.control-group.label>

                                        <x-admin::form.control-group.control
                                            type="hidden"
                                            name="id"
                                            ::value="campaign.id"
                                        />

                                        <x-admin::form.control-group.control
                                            type="text"
                                            name="name"
                                            id="name"
                                            rules="required"
                                            ::value="campaign.name"
                                            :label="trans('admin::app.settings.marketing.campaigns.index.create.name')"
                                        />

                                        <x-admin::form.control-group.error control-name="name" />
                                    </x-admin::form.control-group>

                                    <!-- Subject -->
                                    <x-admin::form.control-group>
                                        <x-admin::form.control-group.label
                                            class="required"
                                            for="subject"
                                        >
                                            @lang('admin::app.settings.marketing.campaigns.index.create.subject')
                                        </x-admin::form.control-group.label>

                                        <x-admin::form.control-group.control
                                            type="text"
                                            name="subject"
                                            id="subject"
                                            rules="required"
                                            rows="4"
                                            ::value="campaign.subject"
                                            :label="trans('admin::app.settings.marketing.campaigns.index.create.subject')"
                                        />

                                        <x-admin::form.control-group.error control-name="subject" />
                                    </x-admin::form.control-group>

                                    <!-- Event -->
                                    <x-admin::form.control-group>
                                        <x-admin::form.control-group.label
                                            class="required"
                                            for="marketing_event_id"
                                        >
                                            @lang('admin::app.settings.marketing.campaigns.index.create.event')
                                        </x-admin::form.control-group.label>

                                        <x-admin::form.control-group.control
                                            type="select"
                                            class="cursor-pointer"
                                            name="marketing_event_id"
                                            id="marketing_event_id"
                                            rules="required"
                                            ::value="campaign.marketing_event_id"
                                            :label="trans('admin::app.settings.marketing.campaigns.index.create.event')"
                                        >
                                            <option
                                                v-for="event in events"
                                                v-text="event.name"
                                                :value="event.id"
                                            ></option>
                                        </x-admin::form.control-group.control>

                                        <x-admin::form.control-group.error control-name="marketing_event_id" />
                                    </x-admin::form.control-group>

                                    <!-- Email Template -->
                                    <x-admin::form.control-group>
                                        <x-admin::form.control-group.label
                                            class="required"
                                            for="marketing_template_id"
                                        >
                                            @lang('admin::app.settings.marketing.campaigns.index.create.email-template')
                                        </x-admin::form.control-group.label>

                                        <x-admin::form.control-group.control
                                            type="select"
                                            class="cursor-pointer"
                                            name="marketing_template_id"
                                            id="marketing_template_id"
                                            rules="required"
                                            ::value="campaign.marketing_template_id"
                                            :label="trans('admin::app.settings.marketing.campaigns.index.create.email-template')"
                                        >
                                            <option
                                                v-for="template in emailTemplates"
                                                v-text="template.name"
                                                :value="template.id"
                                            ></option>
                                        </x-admin::form.control-group.control>

                                        <x-admin::form.control-group.error control-name="marketing_template_id" />
                                    </x-admin::form.control-group>

                                    <!-- Status -->
                                    <x-admin::form.control-group>
                                        <x-admin::form.control-group.label for="status">
                                            @lang('admin::app.settings.marketing.campaigns.index.create.status')
                                        </x-admin::form.control-group.label>

                                        <input
                                            type="hidden"
                                            name="status"
                                            :value="0"
                                        />

                                        <x-admin::form.control-group.control
                                            type="switch"
                                            name="status"
                                            value="1"
                                            :label="trans('admin::app.settings.marketing.campaigns.index.create.status')"
                                            ::checked="parseInt(campaign.status || 0)"
                                        />
                                    </x-admin::form.control-group>

                                    {!! view_render_event('admin.settings.marketing.campaigns.index.form_controls.modal.content.controls.after') !!}
                                </x-slot>

                                <x-slot:footer>
                                    {!! view_render_event('admin.components.activities.actions.activity.form_controls.modal.footer.save_button.before') !!}

                                    <!-- Save Button -->
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
            app.component('v-campaigns', {
                template: '#v-campaigns-template',

                data() {
                    return {
                        isStoring: false,

                        actionType: 'create',

                        campaign: {},

                        events: [],

                        emailTemplates: [],
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

                mounted() {
                    this.getEvents();

                    this.getEmailTemplates();
                },

                methods: {
                    /**
                     * Toggle the modal.
                     *
                     * @return {void}
                     */
                    toggleModal() {
                        this.$refs.campaignModal.toggle();
                    },

                    handleCreate() {
                        this.actionType = 'create';

                        this.campaign = {};

                        this.toggleModal();
                    },

                    /**
                     * Get the all marketing events.
                     *
                     * @return {void}
                     */
                    getEvents() {
                        this.$axios.get('{{ route('admin.settings.marketing.campaigns.events') }}')
                            .then(response => this.events = response.data.data)
                            .catch(error => {});
                    },

                    /**
                     * Get the all Email Templates.
                     *
                     * @return {void}
                     */
                    getEmailTemplates() {
                        this.$axios.get('{{ route('admin.settings.marketing.campaigns.email-templates') }}')
                            .then(response => this.emailTemplates = response.data.data)
                            .catch(error => {});
                    },

                    /**
                     * Create or Update the campaigns.
                     *
                     * @param {Object} params
                     * @param {Function} helpers.resetForm
                     * @param {Function} helpers.setErrors
                     * @return {void}
                     */
                    createOrUpdate(paramas, { resetForm, setErrors }) {
                        this.isStoring = true;

                        const campaignForm = new FormData(this.$refs.campaignForm);

                        const isUpdating = paramas.id && this.actionType === 'edit';

                        campaignForm.append('_method', isUpdating ? 'put' : 'post');

                        this.$axios.post(
                            isUpdating
                            ? `{{ route('admin.settings.marketing.campaigns.update', '') }}/${paramas.id}`
                            : '{{ route('admin.settings.marketing.campaigns.store') }}',
                            campaignForm,
                        )
                            .then(response => {
                                this.$refs.campaignModal.toggle();

                                this.$refs.datagrid.get();

                                this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });
                            })
                            .catch(error => {
                                setErrors(error.response.data?.errors);
                            })
                            .finally(() => this.isStoring = false);
                    },

                    /**
                     * Get the particular campaign record, so that we can use for edit.
                     *
                     * @param {Object} record
                     */
                    edit(record) {
                        this.$axios.get(`{{ route('admin.settings.marketing.campaigns.edit', '') }}/${record.id}`)
                            .then(response => {
                                this.campaign = response.data.data;

                                this.toggleModal();
                            })
                            .catch(error => {});
                    },
                },
            });
        </script>
    @endPushOnce
</x-admin::layouts>
