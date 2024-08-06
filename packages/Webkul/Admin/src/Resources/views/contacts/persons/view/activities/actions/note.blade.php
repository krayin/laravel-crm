{!! view_render_event('admin.contacts.persons.view.actions.note.before', ['person' => $person]) !!}

<!-- Note Button -->
<div class="">
    <button
        class="flex h-[74px] w-[84px] flex-col items-center justify-center gap-1 rounded-lg bg-orange-200 text-orange-800"
        @click="$refs.personNoteActionCompoent.openModal('mail')"
    >
        <span class="icon-note text-2xl"></span>

        @lang('admin::app.contacts.persons.view.activities.actions.note.btn')
    </button>

    <!-- Person Note Action Vue Component -->
    <v-person-note-activity ref="personNoteActionCompoent"></v-person-note-activity>
</div>

{!! view_render_event('admin.contacts.persons.view.actions.note.after', ['person' => $person]) !!}

@pushOnce('scripts')
    <script type="text/x-template" id="v-person-note-activity-template">
        <x-admin::form
            v-slot="{ meta, errors, handleSubmit }"
            as="div"
            ref="modalForm"
        >
            <form @submit="handleSubmit($event, save)">
                <x-admin::modal ref="noteActivityModal" position="bottom-right">
                    <x-slot:header>
                        <h3 class="text-base font-semibold">
                            @lang('admin::app.contacts.persons.view.activities.actions.note.title')
                        </h3>
                    </x-slot>

                    <x-slot:content>
                        <!-- Activity Type -->
                        <x-admin::form.control-group.control
                            type="hidden"
                            name="type"
                            value="note"
                        />
                        
                        <!-- Person Id -->
                        <x-admin::form.control-group.control
                            type="hidden"
                            name="person_id"
                            :value="$person->id"
                        />

                        <!-- Comment -->
                        <x-admin::form.control-group class="!mb-0">
                            <x-admin::form.control-group.label class="required">
                                @lang('admin::app.contacts.persons.view.activities.actions.note.comment')
                            </x-admin::form.control-group.label>

                            <x-admin::form.control-group.control
                                type="textarea"
                                name="comment"
                                rules="required"
                                :label="trans('admin::app.contacts.persons.view.activities.actions.note.comment')"
                            />

                            <x-admin::form.control-group.error control-name="comment" />
                        </x-admin::form.control-group>
                    </x-slot>

                    <x-slot:footer>
                        <x-admin::button
                            class="primary-button"
                            :title="trans('admin::app.contacts.persons.view.activities.actions.note.save-btn')"
                            ::loading="isStoring"
                            ::disabled="isStoring"
                        />
                    </x-slot>
                </x-admin::modal>
            </form>
        </x-admin::form>
    </script>

    <script type="module">
        app.component('v-person-note-activity', {
            template: '#v-person-note-activity-template',

            data() {
                return {
                    isStoring: false,
                }; 
            },

            methods: {
                openModal(type) {
                    this.$refs.noteActivityModal.open();
                },

                save(params) {
                    this.isStoring = true;
                    
                    this.$axios.post("{{ route('admin.activities.store') }}", params)
                        .then ((response) => {
                            this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });

                            this.$emitter.emit('on-activity-added', response.data.data);

                            this.$refs.noteActivityModal.close();
                        })
                        .catch ((error) => {
                            if (error.response.status == 422) {
                                setErrors(error.response.data.errors);
                            } else {
                                this.$emitter.emit('add-flash', { type: 'error', message: error.response.data.message });

                                this.$refs.noteActivityModal.close();
                            }
                        }).finally(() => this.isStoring = false);
                },
            },
        });
    </script>
@endPushOnce