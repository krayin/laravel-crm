<!-- Note Button -->
<div class="">
    <button
        class="flex h-[74px] w-[84px] flex-col items-center justify-center gap-1 rounded-lg bg-orange-200 text-orange-800"
        @click="$refs.leadNoteActionComponent.openModal('mail')"
    >
        <span class="icon-note text-2xl"></span>

        @lang('admin::app.leads.view.activities.actions.note.btn')
    </button>

    <!-- Lead Note Action Vue Component -->
    <v-lead-note-activity ref="leadNoteActionComponent"></v-lead-note-activity>
</div>

@pushOnce('scripts')
    <script type="text/x-template" id="v-lead-note-activity-tempalte">
        <x-admin::form
            v-slot="{ meta, errors, handleSubmit }"
            as="div"
            ref="modalForm"
        >
            <form @submit="handleSubmit($event, updateOrCreate)">
                <x-admin::modal ref="mailActivityModal" position="bottom-right">
                    <x-slot:header>
                        <h3 class="text-base font-semibold">
                            @lang('admin::app.leads.view.activities.actions.note.title')
                        </h3>
                    </x-slot>

                    <x-slot:content>
                        <!-- Content -->
                        <x-admin::form.control-group>
                            <x-admin::form.control-group.control
                                type="textarea"
                                id="comment"
                                name="comment"
                                rules="required"
                                :label="trans('admin::app.leads.view.activities.actions.note.note')"
                            />

                            <x-admin::form.control-group.error control-name="comment" />
                        </x-admin::form.control-group>
                    </x-slot>

                    <x-slot:footer>
                        <button
                            type="submit"
                            class="primary-button"
                        >
                            @lang('admin::app.leads.view.activities.actions.note.save-btn')
                        </button>
                    </x-slot>
                </x-admin::modal>
            </form>
        </x-admin::form>
    </script>

    <script type="module">
        app.component('v-lead-note-activity', {
            template: '#v-lead-note-activity-tempalte',

            data: function () {
                return {
                }
            },

            methods: {
                openModal(type) {
                    this.$refs.mailActivityModal.open();
                }
            }
        });
    </script>
@endPushOnce