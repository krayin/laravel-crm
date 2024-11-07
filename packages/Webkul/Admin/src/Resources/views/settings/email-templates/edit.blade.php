
<x-admin::layouts>
    <!-- Page Title -->
    <x-slot:title>
        @lang('admin::app.settings.email-template.edit.title')
    </x-slot>

    {!! view_render_event('admin.settings.email_template.edit.form.before') !!}

    <x-admin::form
        :action="route('admin.settings.email_templates.update', $emailTemplate->id)"
        method="PUT"
    >
        <div class="flex flex-col gap-4">
            <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                <div class="flex flex-col gap-2">
                    <div class="flex cursor-pointer items-center">
                        {!! view_render_event('admin.settings.email_template.edit.breadcrumbs.before') !!}

                        <!-- Breadcrumbs -->
                        <x-admin::breadcrumbs
                            name="settings.email_templates.edit"
                            :entity="$emailTemplate"
                        />

                        {!! view_render_event('admin.settings.email_template.edit.breadcrumbs.after') !!}
                    </div>

                    <div class="text-xl font-bold dark:text-white">
                        @lang('admin::app.settings.email-template.edit.title')
                    </div>
                </div>

                <div class="flex items-center gap-x-2.5">
                    <!-- Create button for person -->
                    <div class="flex items-center gap-x-2.5">
                        {!! view_render_event('admin.settings.email_template.edit.save_button.before') !!}

                        <button
                            type="submit"
                            class="primary-button"
                        >
                            @lang('Save Email Template')
                        </button>

                        {!! view_render_event('admin.settings.email_template.edit.save_button.before') !!}
                    </div>
                </div>
            </div>

            <v-email-template></v-email-template>
        </div>
    </x-admin::form>

    {!! view_render_event('admin.settings.email_template.edit.form.after') !!}

    @pushOnce('scripts')
        <script
            type="text/x-template"
            id="v-email-template-template"
        >
            <div class="flex gap-2.5 max-xl:flex-wrap">
                <!-- Left sub-component -->
                <div class="flex flex-1 flex-col gap-2 max-xl:flex-auto">
                    <div class="box-shadow rounded-lg border border-gray-200 bg-white p-4 dark:bg-gray-900 dark:border-gray-800">
                        <div class="mb-4 flex items-center justify-between gap-4">
                            <p class="text-base font-semibold text-gray-800 dark:text-white">
                                @lang('admin::app.settings.email-template.edit.email-template')
                            </p>
                        </div>

                        {!! view_render_event('admin.settings.email_template.edit.subject.before') !!}

                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label class="required">
                                @lang('admin::app.settings.email-template.edit.subject')
                            </x-admin::form.control-group.label>

                            <div class="flex">
                                <x-admin::form.control-group.control
                                    type="text"
                                    name="subject"
                                    id="subject"
                                    class="rounded-r-none"
                                    rules="required"
                                    :label="trans('admin::app.settings.email-template.edit.subject')"
                                    :placeholder="trans('admin::app.settings.email-template.edit.subject')"
                                    v-model="subject"
                                    @focusout="saveCursorPosition"
                                />

                                <x-admin::form.control-group.control
                                    type="select"
                                    name="placeholder"
                                    id="placeholder"
                                    class="!w-1/3 rounded-l-none"
                                    :label="trans('admin::app.settings.email-template.edit.subject-placeholder')"
                                    v-model="selectedPlaceholder"
                                    @change="insertPlaceholder"
                                >
                                    <optgroup
                                        v-for="entity in placeholders"
                                        :key="entity.text"
                                        :label="entity.text"
                                    >
                                        <option
                                            v-for="placeholder in entity.menu"
                                            :key="placeholder.value"
                                            :value="placeholder.value"
                                            :text="placeholder.text"
                                        ></option>
                                    </optgroup>
                                </x-admin::form.control-group.control>

                            </div>
                        </x-admin::form.control-group>

                        <x-admin::form.control-group.error control-name="subject"/>

                        {!! view_render_event('admin.settings.email_template.edit.subject.after') !!}

                        {!! view_render_event('admin.settings.email_template.edit.content.before') !!}

                        <!-- Event Name -->
                        <x-admin::form.control-group class="!mb-0">
                            <x-admin::form.control-group.label class="required">
                                @lang('admin::app.settings.email-template.edit.content')
                            </x-admin::form.control-group.label>

                            <x-admin::form.control-group.control
                                type="textarea"
                                id="content"
                                name="content"
                                rules="required"
                                :value="old('content') ?? $emailTemplate->content"
                                :tinymce="true"
                                :placeholders="json_encode($placeholders)"
                                :label="trans('admin::app.settings.email-template.edit.content')"
                                :placeholder="trans('admin::app.settings.email-template.edit.content')"
                            />

                            <x-admin::form.control-group.error control-name="content" />
                        </x-admin::form.control-group>

                        {!! view_render_event('admin.settings.email_template.edit.content.after') !!}
                    </div>
                </div>

                <!-- Right sub-component -->
                <div class="flex w-[360px] max-w-full flex-col gap-2 max-sm:w-full">
                    {!! view_render_event('admin.settings.email_template.edit.accordion.general.before') !!}

                    <x-admin::accordion>
                        <x-slot:header>
                            <div class="flex items-center justify-between">
                                <p class="p-2.5 text-base font-semibold text-gray-800 dark:text-white">
                                    @lang('admin::app.settings.email-template.edit.general')
                                </p>
                            </div>
                        </x-slot>

                        <x-slot:content>
                            <x-admin::form.control-group class="!mb-0">
                                <x-admin::form.control-group.label class="required">
                                    @lang('admin::app.settings.email-template.edit.name')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="text"
                                    name="name"
                                    id="name"
                                    :value="old('name') ?? $emailTemplate->name"
                                    rules="required"
                                    :label="trans('admin::app.settings.email-template.edit.name')"
                                    :placeholder="trans('admin::app.settings.email-template.edit.name')"
                                />
                                <x-admin::form.control-group.error control-name="name" />
                            </x-admin::form.control-group>
                        </x-slot>
                    </x-admin::accordion>

                    {!! view_render_event('admin.settings.email_template.edit.accordion.general.after') !!}
                </div>
            </div>
        </script>

        <script type="module">
            app.component('v-email-template', {
                template: '#v-email-template-template',

                data() {
                    return {
                        subject: '{{ old('subject') ?? $emailTemplate->subject }}',

                        selectedPlaceholder: '',

                        cursorPosition: 0,

                        placeholders: @json($placeholders),
                    };
                },

                methods: {
                    /**
                     * Save the cursor position when the input is focused.
                     * 
                     * @param {Event} event
                     * @returns {void}
                     */
                    saveCursorPosition(event) {
                        this.cursorPosition = event.target.selectionStart;
                    },

                    /**
                     * Insert the selected placeholder into the subject.
                     * 
                     * @returns {void}
                     */
                    insertPlaceholder() {
                        const placeholder = this.selectedPlaceholder;

                        if (this.cursorPosition >= 0) {
                            const before = this.subject.substring(0, this.cursorPosition);

                            const after = this.subject.substring(this.cursorPosition);

                            this.subject = `${before}${placeholder}${after}`;

                            this.cursorPosition += placeholder.length;
                        } else if (placeholder) {
                            this.subject += placeholder;
                        }

                        this.selectedPlaceholder = '';
                    },
                },
            });
        </script>
    @endPushOnce
</x-admin::layouts>
