{!! view_render_event('admin.leads.view.actions.mail.before', ['lead' => $lead]) !!}

<!-- Mail Button -->
<div class="">
    <button
        class="flex h-[74px] w-[84px] flex-col items-center justify-center gap-1 rounded-lg bg-green-200 text-green-900"
        @click="$refs.leadMailActionComponent.openModal('mail')"
    >
        <span class="icon-mail text-2xl"></span>

        @lang('admin::app.leads.view.activities.actions.mail.btn')
    </button>

    <!-- Lead Mail Activity Action Vue Component -->
    <v-lead-mail-activity ref="leadMailActionComponent"></v-lead-mail-activity>
</div>

{!! view_render_event('admin.leads.view.actions.mail.after', ['lead' => $lead]) !!}

@pushOnce('scripts')
    <script type="text/x-template" id="v-lead-mail-activity-template">
        <x-admin::form
            v-slot="{ meta, errors, handleSubmit }"
            as="div"
            ref="modalForm"
        >
            <form @submit="handleSubmit($event, updateOrCreate)">
                <x-admin::modal ref="mailActivityModal" position="bottom-right">
                    <x-slot:header>
                        <h3 class="text-base font-semibold">
                            @lang('admin::app.leads.view.activities.actions.mail.title')
                        </h3>
                    </x-slot>

                    <x-slot:content>
                        <!-- Emails -->
                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label class="required">
                                @lang('admin::app.leads.view.activities.actions.mail.to')
                            </x-admin::form.control-group.label>

                            <x-admin::form.control-group.control
                                type="tags"
                                id="to"
                                name="to"
                                rules="required"
                                :label="trans('admin::app.leads.view.activities.actions.mail.to')"
                                :placeholder="trans('admin::app.leads.view.activities.actions.mail.to')"
                            />

                            <x-admin::form.control-group.error control-name="to" />
                        </x-admin::form.control-group>

                        <!-- Subject -->
                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label class="required">
                                @lang('admin::app.leads.view.activities.actions.mail.subject')
                            </x-admin::form.control-group.label>

                            <x-admin::form.control-group.control
                                type="text"
                                id="subject"
                                name="subject"
                                rules="required"
                                :label="trans('admin::app.leads.view.activities.actions.mail.subject')"
                                :placeholder="trans('admin::app.leads.view.activities.actions.mail.subject')"
                            />

                            <x-admin::form.control-group.error control-name="subject" />
                        </x-admin::form.control-group>

                        <!-- Content -->
                        <x-admin::form.control-group class="!mb-0">
                            <x-admin::form.control-group.control
                                type="textarea"
                                id="reply"
                                name="reply"
                                rules="required"
                                tinymce="true"
                                :label="trans('admin::app.leads.view.activities.actions.mail.message')"
                            />

                            <x-admin::form.control-group.error control-name="reply" />
                        </x-admin::form.control-group>
                    </x-slot>

                    <x-slot:footer>
                        <div class="flex w-full items-center justify-between">
                            <span class="icon-attachmetent cursor-pointer rounded-md p-1 text-2xl transition-all hover:bg-gray-200"></span>

                            <button
                                type="submit"
                                class="primary-button"
                            >
                                <span class="icon-sent text-xl"></span>
                                
                                @lang('admin::app.leads.view.activities.actions.mail.send-btn')
                            </button>
                        </div>
                    </x-slot>
                </x-admin::modal>
            </form>
        </x-admin::form>
    </script>

    <script type="module">
        app.component('v-lead-mail-activity', {
            template: '#v-lead-mail-activity-template',

            data: function () {
                return {
                }
            },

            methods: {
                openModal(type) {
                    this.$refs.mailActivityModal.open();
                },
            },
        });
    </script>
@endPushOnce