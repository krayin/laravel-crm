
<x-admin::layouts>
    <!-- Page Title -->
    <x-slot:title>
        @lang('admin::app.settings.email-template.create.title')
    </x-slot>

    {!! view_render_event('admin.settings.email_template.create.form.before') !!}

    <x-admin::form
        :action="route('admin.settings.email_templates.store')"
        method="POST"
    >
        <div class="flex flex-col gap-4">
            <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                <div class="flex flex-col gap-2">
                    {!! view_render_event('admin.settings.email_template.create.breadcrumbs.after') !!}

                    <!-- Breadcrumbs -->
                    <x-admin::breadcrumbs name="settings.email_templates.create" />

                    {!! view_render_event('admin.settings.email_template.create.breadcrumbs.after') !!}

                    <div class="text-xl font-bold dark:text-white">
                        @lang('admin::app.settings.email-template.create.title')
                    </div>
                </div>

                <div class="flex items-center gap-x-2.5">
                    <!-- Create button for person -->
                    <div class="flex items-center gap-x-2.5">
                        {!! view_render_event('admin.settings.email_template.create.save_button.before') !!}

                        <button
                            type="submit"
                            class="primary-button"
                        >
                            @lang('admin::app.settings.email-template.create.save-btn')
                        </button>

                        {!! view_render_event('admin.settings.email_template.create.save_button.after') !!}
                    </div>
                </div>
            </div>

            <v-email-template></v-email-template>
        </div>
    </x-admin::form>

    {!! view_render_event('admin.email_template.create.form.after') !!}

    @pushOnce('scripts')
        <script
            type="text/x-template"
            id="v-email-template-template"
        >
            <div class="flex gap-2.5 max-xl:flex-wrap">
                <!-- Left sub-component -->
                <div class="flex flex-1 flex-col gap-2 max-xl:flex-auto">
                    <div class="box-shadow rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                        <div class="mb-4 flex items-center justify-between gap-4">
                            <p class="text-base font-semibold text-gray-800 dark:text-white">
                                @lang('admin::app.settings.email-template.create.email-template')
                            </p>
                        </div>

                        {!! view_render_event('admin.settings.email_template.create.subject.before') !!}

                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label class="required">
                                @lang('admin::app.settings.email-template.create.subject')
                            </x-admin::form.control-group.label>

                            <div class="flex">
                                <x-admin::form.control-group.control
                                    type="text"
                                    name="subject"
                                    id="subject"
                                    class="rounded-r-none"
                                    :value="old('subject')"
                                    rules="required"
                                    :label="trans('admin::app.settings.email-template.create.subject')"
                                    :placeholder="trans('admin::app.settings.email-template.create.subject')"
                                    v-model="subject"
                                    @focusout="saveCursorPosition"
                                />

                                <x-admin::form.control-group.control
                                    type="select"
                                    name="placeholder"
                                    id="placeholder"
                                    class="!w-1/3 rounded-l-none"
                                    :label="trans('admin::app.settings.email-template.create.subject-placeholder')"
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

                        {!! view_render_event('admin.settings.email_template.create.subject.after') !!}

                        {!! view_render_event('admin.settings.email_template.create.content.before') !!}

                        <!-- Event Name -->
                        <x-admin::form.control-group class="!mb-0">
                            <x-admin::form.control-group.label class="required">
                                @lang('admin::app.settings.email-template.create.content')
                            </x-admin::form.control-group.label>

                            <x-admin::form.control-group.control
                                type="textarea"
                                id="content"
                                name="content"
                                rules="required"
                                :tinymce="true"
                                :placeholders="json_encode($placeholders)"
                                :label="trans('admin::app.settings.email-template.create.content')"
                                :placeholder="trans('admin::app.settings.email-template.create.content')"
                            />

                            <x-admin::form.control-group.error control-name="content" />
                        </x-admin::form.control-group>

                        {!! view_render_event('admin.settings.email_template.create.content.after') !!}
                    </div>
                </div>

                <!-- Right sub-component -->
                <div class="flex w-[360px] max-w-full flex-col gap-2 max-sm:w-full">
                    {!! view_render_event('admin.settings.email_template.create.accordion.general.before') !!}

                    <x-admin::accordion>
                        <x-slot:header>
                            <div class="flex items-center justify-between">
                                <p class="p-2.5 text-base font-semibold text-gray-800 dark:text-white">
                                    @lang('admin::app.settings.email-template.create.general')
                                </p>
                            </div>
                        </x-slot>

                        <x-slot:content>
                            <x-admin::form.control-group class="!mb-0">
                                <x-admin::form.control-group.label class="required">
                                    @lang('admin::app.settings.email-template.create.name')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="text"
                                    name="name"
                                    id="name"
                                    :value="old('name')"
                                    rules="required"
                                    :label="trans('admin::app.settings.email-template.create.name')"
                                    :placeholder="trans('admin::app.settings.email-template.create.name')"
                                />
                                <x-admin::form.control-group.error control-name="name" />
                            </x-admin::form.control-group>
                        </x-slot>
                    </x-admin::accordion>

                    {!! view_render_event('admin.settings.email_template.create.accordion.general.after') !!}
                </div>
            </div>
        </script>

        <script type="module">
            app.component('v-email-template', {
                template: '#v-email-template-template',

                data() {
                    return {
                        subject: '',

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
