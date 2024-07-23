
<x-admin::layouts>
    <!-- Page Title -->
    <x-slot:title>
        @lang('Create Email Template')
    </x-slot>

    {!! view_render_event('krayin.admin.email_template.edit.form.before') !!}

    <x-admin::form :action="route('admin.settings.workflows.store')">
        <div class="flex items-center justify-between">
            <p class="text-xl font-bold text-gray-800 dark:text-white">
                @lang('Create Email Template')
            </p>

            <div class="flex items-center gap-x-2.5">
                {!! view_render_event('krayin.admin.email_template.edit.back_button.before') !!}

                <!-- Back Button -->
                <a
                    href="{{ route('admin.settings.workflows.index') }}"
                    class="transparent-button hover:bg-gray-200 dark:text-white dark:hover:bg-gray-800"
                >
                    @lang('Back')
                </a>

                {!! view_render_event('krayin.admin.email_template.edit.back_button.after') !!}

                {!! view_render_event('krayin.admin.email_template.edit.back_button.before') !!}

                <!-- Save Button -->
                <button
                    type="submit"
                    class="primary-button"
                >
                    @lang('Save Email Template')
                </button>
 
                {!! view_render_event('krayin.admin.email_template.edit.back_button.after') !!}
            </div>
        </div>

        <v-email-template></v-email-template>
    </x-admin::form>

    {!! view_render_event('krayin.admin.email_template.edit.form.after') !!}

    @pushOnce('scripts')
        <script
            type="text/x-template"
            id="v-email-template-template"
        >
            <div class="mt-3.5 flex gap-2.5 max-xl:flex-wrap">
                <!-- Left sub-component -->
                <div class="flex flex-1 flex-col gap-2 max-xl:flex-auto">
                    <!-- Events -->
                    <div class="box-shadow rounded bg-white p-4 dark:bg-gray-900">
                        <div class="mb-8 flex items-center justify-between gap-4">
                            <p class="text-base font-semibold text-gray-800 dark:text-white">
                                @lang('Email Template')
                            </p>
                        </div>

                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label>
                                @lang('Subject')
                            </x-admin::form.control-group.label>

                            <div class="flex">
                                <x-admin::form.control-group.control
                                    type="text"
                                    name="subject"
                                    id="subject"
                                    class="rounded-r-none"
                                    :value="old('subject')"
                                    :label="trans('Subject')"
                                    :placeholder="trans('Subject')"
                                    v-model="subject"
                                    @focusout="saveCursorPosition"
                                />

                                <x-admin::form.control-group.control
                                    type="select"
                                    name="placeholder"
                                    id="placeholder"
                                    class="!w-1/3 rounded-l-none"
                                    :label="trans('Subject Placeholder')"
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

                            <x-admin::form.control-group.error control-name="description" />
                        </x-admin::form.control-group>

                        <!-- Event Name -->
                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label class="required">
                                @lang('Content')
                            </x-admin::form.control-group.label>

                            <x-admin::form.control-group.control
                                type="textarea"
                                id="content"
                                name="content"
                                rules="required"
                                :tinymce="true"
                                :placeholders="json_encode($placeholders)"
                                :label="trans('Content')"
                                :placeholder="trans('Content')"
                            />

                            <x-admin::form.control-group.error control-name="content" />
                        </x-admin::form.control-group>
                    </div>
                </div>

                <!-- Right sub-component -->
                <div class="flex w-[360px] max-w-full flex-col gap-2 max-sm:w-full">
                    {!! view_render_event('krayin.admin.activities.edit.accordion.general.before') !!}

                    <x-admin::accordion>
                        <x-slot:header>
                            <div class="flex items-center justify-between">
                                <p class="p-2.5 text-base font-semibold text-gray-800 dark:text-white">
                                    @lang('General')
                                </p>
                            </div>
                        </x-slot>

                        <x-slot:content>
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label class="required">
                                    @lang('Name')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="text"
                                    name="name"
                                    id="name"
                                    :value="old('name')"
                                    rules="required"
                                    :label="trans('Name')"
                                    :placeholder="trans('Name')"
                                />
                                <x-admin::form.control-group.error control-name="name" />
                            </x-admin::form.control-group>
                        </x-slot>
                    </x-admin::accordion>

                    {!! view_render_event('krayin.admin.activities.edit.accordion.general.after') !!}
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
                    saveCursorPosition(event) {
                        this.cursorPosition = event.target.selectionStart;
                    },

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
