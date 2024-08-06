{!! view_render_event('admin.contacts.persons.view.actions.file.before', ['person' => $person]) !!}

<!-- File Button -->
<div class="">
    <button
        class="flex h-[74px] w-[84px] flex-col items-center justify-center gap-1 rounded-lg bg-cyan-200 text-cyan-900"
        @click="$refs.leadFileActionComponent.openModal('mail')"
    >
        <span class="icon-note text-2xl"></span>

        @lang('admin::app.contacts.persons.view.activities.actions.file.btn')
    </button>

    <!-- Lead Note Action Vue Component -->
    <v-lead-file-activity ref="leadFileActionComponent"></v-lead-file-activity>
</div>

{!! view_render_event('admin.contacts.persons.view.actions.file.after', ['person' => $person]) !!}

@pushOnce('scripts')
    <script type="text/x-template" id="v-lead-file-activity-template">
        <x-admin::form
            v-slot="{ meta, errors, handleSubmit }"
            as="div"
            ref="modalForm"
        >
            <form @submit="handleSubmit($event, save)">
                <x-admin::modal ref="mailActivityModal" position="bottom-right">
                    <x-slot:header>
                        <h3 class="text-base font-semibold">
                            @lang('admin::app.contacts.persons.view.activities.actions.file.title')
                        </h3>
                    </x-slot>

                    <x-slot:content>
                        <!-- Activity Type -->
                        <x-admin::form.control-group.control
                            type="hidden"
                            name="type"
                            value="file"
                        />
                        
                        <!-- Lead Id -->
                        <x-admin::form.control-group.control
                            type="hidden"
                            name="lead_id"
                            :value="$person->id"
                        />

                        <!-- Title -->
                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label>
                                @lang('admin::app.contacts.persons.view.activities.actions.file.title-control')
                            </x-admin::form.control-group.label>
                            
                            <x-admin::form.control-group.control
                                type="text"
                                name="title"
                            />
                        </x-admin::form.control-group>

                        <!-- Description -->
                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label>
                                @lang('admin::app.contacts.persons.view.activities.actions.file.description')
                            </x-admin::form.control-group.label>
                            
                            <x-admin::form.control-group.control
                                type="textarea"
                                name="comment"
                            />
                        </x-admin::form.control-group>
                        
                        <!-- File Name -->
                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label>
                                @lang('admin::app.contacts.persons.view.activities.actions.file.name')
                            </x-admin::form.control-group.label>

                            <x-admin::form.control-group.control
                                type="text"
                                name="name"
                            />
                        </x-admin::form.control-group>

                        <!-- File -->
                        <x-admin::form.control-group class="!mb-0">
                            <x-admin::form.control-group.label class="required">
                                @lang('admin::app.contacts.persons.view.activities.actions.file.file')
                            </x-admin::form.control-group.label>
                            
                            <x-admin::form.control-group.control
                                type="file"
                                id="file"
                                name="file"
                                rules="required"
                                :label="trans('admin::app.contacts.persons.view.activities.actions.file.file')"
                            />

                            <x-admin::form.control-group.error control-name="file" />
                        </x-admin::form.control-group>
                    </x-slot>

                    <x-slot:footer>
                        <x-admin::button
                            class="primary-button"
                            :title="trans('admin::app.contacts.persons.view.activities.actions.file.save-btn')"
                            ::loading="isStoring"
                            ::disabled="isStoring"
                        />
                    </x-slot>
                </x-admin::modal>
            </form>
        </x-admin::form>
    </script>

    <script type="module">
        app.component('v-lead-file-activity', {
            template: '#v-lead-file-activity-template',

            data: function () {
                return {
                    isStoring: false,
                }
            },

            methods: {
                openModal(type) {
                    this.$refs.mailActivityModal.open();
                },

                save(params, { setErrors }) {
                    this.isStoring = true;

                    let self = this;

                    this.$axios.post("{{ route('admin.activities.store', $person->id) }}", params, {
                            headers: {
                                'Content-Type': 'multipart/form-data',
                            }
                        })
                        .then (function(response) {
                            self.isStoring = false;

                            self.$emitter.emit('add-flash', { type: 'success', message: response.data.message });

                            self.$emitter.emit('on-activity-added', response.data.data);

                            self.$refs.mailActivityModal.close();
                        })
                        .catch (function (error) {
                            self.isStoring = false;

                            if (error.response.status == 422) {
                                setErrors(error.response.data.errors);
                            } else {
                                self.$emitter.emit('add-flash', { type: 'error', message: error.response.data.message });

                                self.$refs.mailActivityModal.close();
                            }
                        });
                },
            },
        });
    </script>
@endPushOnce