@props([
    'entity'            => null,
    'entityControlName' => null,
])

<!-- Note Button -->
<div class="">
    <button
        class="flex h-[74px] w-[84px] flex-col items-center justify-center gap-1 rounded-lg border border-transparent bg-orange-200 font-medium text-orange-800 transition-all hover:border-orange-400"
        @click="$refs.noteActionComponent.openModal('mail')"
    >
        <span class="icon-note text-2xl dark:!text-orange-800"></span>

        @lang('admin::app.components.activities.actions.note.btn')
    </button>

    <!-- Note Action Vue Component -->
    <v-note-activity
        ref="noteActionComponent"
        :entity="{{ json_encode($entity) }}"
        entity-control-name="{{ $entityControlName }}"
    ></v-note-activity>
</div>

@pushOnce('scripts')
    <script type="text/x-template" id="v-note-activity-template">
        <Teleport to="body">
            <x-admin::form
                v-slot="{ meta, errors, handleSubmit }"
                as="div"
                ref="modalForm"
            >
                <form @submit="handleSubmit($event, save)">
                    <x-admin::modal ref="noteActivityModal" position="bottom-right">
                        <x-slot:header>
                            <h3 class="text-base font-semibold dark:text-white">
                                @lang('admin::app.components.activities.actions.note.title')
                            </h3>
                        </x-slot>

                        <x-slot:content>
                            <!-- Activity Type -->
                            <x-admin::form.control-group.control
                                type="hidden"
                                name="type"
                                value="note"
                            />
                            
                            <!-- Id -->
                            <x-admin::form.control-group.control
                                type="hidden"
                                ::name="entityControlName"
                                ::value="entity.id"
                            />

                            <!-- Comment -->
                            <x-admin::form.control-group class="!mb-0">
                                <x-admin::form.control-group.label class="required">
                                    @lang('admin::app.components.activities.actions.note.comment')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="textarea"
                                    name="comment"
                                    rules="required"
                                    :label="trans('admin::app.components.activities.actions.note.comment')"
                                />

                                <x-admin::form.control-group.error control-name="comment" />
                            </x-admin::form.control-group>
                        </x-slot>

                        <x-slot:footer>
                            <x-admin::button
                                class="primary-button"
                                :title="trans('admin::app.components.activities.actions.note.save-btn')"
                                ::loading="isStoring"
                                ::disabled="isStoring"
                            />
                        </x-slot>
                    </x-admin::modal>
                </form>
            </x-admin::form>
        </Teleport>
    </script>

    <script type="module">
        app.component('v-note-activity', {
            template: '#v-note-activity-template',

            props: {
                entity: {
                    type: Object,
                    required: true,
                    default: () => {}
                },

                entityControlName: {
                    type: String,
                    required: true,
                    default: ''
                }
            },

            data: function () {
                return {
                    isStoring: false,
                }
            },

            methods: {
                openModal(type) {
                    this.$refs.noteActivityModal.open();
                },

                save(params) {
                    this.isStoring = true;

                    this.$axios.post("{{ route('admin.activities.store') }}", params)
                        .then (response => {
                            this.isStoring = false;

                            this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });

                            this.$emitter.emit('on-activity-added', response.data.data);

                            this.$refs.noteActivityModal.close();
                        })
                        .catch (error => {
                            this.isStoring = false;

                            if (error.response.status == 422) {
                                setErrors(error.response.data.errors);
                            } else {
                                this.$emitter.emit('add-flash', { type: 'error', message: error.response.data.message });

                                this.$refs.noteActivityModal.close();
                            }
                        });
                },
            },
        });
    </script>
@endPushOnce