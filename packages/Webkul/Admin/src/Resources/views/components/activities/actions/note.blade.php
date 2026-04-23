@props([
    'entity' => null,
    'entityControlName' => null,
])

<!-- Note Button -->
<div>
    {!! view_render_event('admin.components.activities.actions.note.create_btn.before') !!}

    <button
        class="flex h-[74px] w-[84px] flex-col items-center justify-center gap-1 rounded-lg border border-transparent bg-orange-200 font-medium text-orange-800 transition-all hover:border-orange-400"
        @click="$refs.noteActionComponent.openModal('mail')"
    >
        <span class="icon-note text-2xl dark:!text-orange-800"></span>

        @lang('admin::app.components.activities.actions.note.btn')
    </button>

    {!! view_render_event('admin.components.activities.actions.note.create_btn.after') !!}

    {!! view_render_event('admin.components.activities.actions.note.before') !!}

    <!-- Note Action Vue Component -->
    <v-note-activity
        ref="noteActionComponent"
        :entity="{{ json_encode($entity) }}"
        entity-control-name="{{ $entityControlName }}"
    ></v-note-activity>

    {!! view_render_event('admin.components.activities.actions.note.after') !!}
</div>

@pushOnce('scripts')
    <script type="text/x-template" id="v-note-activity-template">
        <Teleport to="body">
            {!! view_render_event('admin.components.activities.actions.note.form_controls.before') !!}

            <x-admin::form
                v-slot="{ meta, errors, handleSubmit }"
                as="div"
                ref="modalForm"
            >
                <form @submit="handleSubmit($event, save)">
                    {!! view_render_event('admin.components.activities.actions.note.form_controls.modal.before') !!}

                    <x-admin::modal 
                        ref="noteActivityModal"
                        position="bottom-right"
                    >
                        <x-slot:header>
                            {!! view_render_event('admin.components.activities.actions.note.form_controls.modal.header.title.before') !!}

                            <h3 class="text-base font-semibold dark:text-white">
                                <template v-if="editingNote">@lang('admin::app.components.activities.index.edit') @lang('admin::app.components.activities.actions.note.btn')</template>
                                <template v-else>@lang('admin::app.components.activities.actions.note.title')</template>
                            </h3>

                            {!! view_render_event('admin.components.activities.actions.note.form_controls.modal.header.title.after') !!}
                        </x-slot>

                        <x-slot:content>
                            {!! view_render_event('admin.components.activities.actions.note.form_controls.modal.header.content.controls.before') !!}

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

                            <!-- Parent Activity Id -->
                            <x-admin::form.control-group.control
                                v-if="parentActivityId"
                                type="hidden"
                                name="parent_activity_id"
                                ::value="parentActivityId"
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

                            {!! view_render_event('admin.components.activities.actions.note.form_controls.modal.header.content.controls.after') !!}
                        </x-slot>

                        <x-slot:footer>
                            {!! view_render_event('admin.components.activities.actions.note.form_controls.modal.header.footer.save_button.before') !!}

                            <x-admin::button
                                class="primary-button"
                                :title="trans('admin::app.components.activities.actions.note.save-btn')"
                                ::loading="isStoring"
                                ::disabled="isStoring"
                            />

                            {!! view_render_event('admin.components.activities.actions.note.form_controls.modal.header.footer.save_button.after') !!}
                        </x-slot>
                    </x-admin::modal>

                    {!! view_render_event('admin.components.activities.actions.note.form_controls.modal.after') !!}
                </form>
            </x-admin::form>

            {!! view_render_event('admin.components.activities.actions.note.form_controls.after') !!}
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
                    parentActivityId: null,
                    editingNote: null,
                }
            },

            mounted() {
                this.$emitter.on('open-note-for-activity', ({ activityId }) => {
                    this.editingNote = null;
                    this.parentActivityId = activityId;
                    this.$refs.noteActivityModal.open();
                    this.$nextTick(() => this.$refs.modalForm.resetForm());
                });

                this.$emitter.on('open-edit-note', (note) => {
                    this.editingNote = note;
                    this.parentActivityId = null;
                    this.$refs.noteActivityModal.open();
                    this.$nextTick(() => this.$refs.modalForm.setFieldValue('comment', note.comment));
                });
            },

            methods: {
                openModal(type) {
                    this.editingNote = null;
                    this.parentActivityId = null;
                    this.$refs.noteActivityModal.open();
                    this.$nextTick(() => this.$refs.modalForm.resetForm());
                },

                save(params) {
                    this.isStoring = true;

                    const isEditing = !! this.editingNote;

                    const request = isEditing
                        ? this.$axios.put("{{ route('admin.activities.update', 'replaceId') }}".replace('replaceId', this.editingNote.id), { comment: params.comment, type: 'note' })
                        : this.$axios.post("{{ route('admin.activities.store') }}", params);

                    request
                        .then(response => {
                            this.isStoring = false;

                            this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });

                            if (isEditing) {
                                this.$emitter.emit('on-note-updated', response.data.data);
                            } else {
                                this.$emitter.emit('on-activity-added', response.data.data);
                            }

                            this.editingNote = null;
                            this.parentActivityId = null;

                            this.$refs.noteActivityModal.close();
                        })
                        .catch(error => {
                            this.isStoring = false;

                            if (error.response.status == 422) {
                                setErrors(error.response.data.errors);
                            } else {
                                this.$emitter.emit('add-flash', { type: 'error', message: error.response.data.message });

                                this.editingNote = null;
                                this.parentActivityId = null;

                                this.$refs.noteActivityModal.close();
                            }
                        });
                },
            },
        });
    </script>
@endPushOnce