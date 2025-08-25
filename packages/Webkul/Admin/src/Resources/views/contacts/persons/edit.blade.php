
<x-admin::layouts>
    <!-- Page Title -->
    <x-slot:title>
        @lang('admin::app.contacts.persons.edit.title')
    </x-slot>

    {!! view_render_event('admin.persons.edit.form.before') !!}

    <x-admin::form
        :action="route('admin.contacts.persons.update', $person->id)"
        method="PUT"
        enctype="multipart/form-data"
    >
        <div class="flex flex-col gap-4">
            <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                <div class="flex flex-col gap-2">
                    {!! view_render_event('admin.persons.edit.breadcrumbs.before') !!}

                    <x-admin::breadcrumbs
                        name="contacts.persons.edit"
                        :entity="$person"
                    />

                    {!! view_render_event('admin.persons.edit.breadcrumbs.after') !!}

                    <div class="text-xl font-bold dark:text-white">
                        @lang('admin::app.contacts.persons.edit.title')
                    </div>
                </div>

                <div class="flex items-center gap-x-2.5">
                    <!--  Save button for Person -->
                    <div class="flex items-center gap-x-2.5">
                        {!! view_render_event('admin.persons.edit.save_button.before') !!}

                        <button
                            type="submit"
                            class="primary-button"
                        >
                            @lang('admin::app.contacts.persons.edit.save-btn')
                        </button>

                        {!! view_render_event('admin.persons.edit.save_button.after') !!}
                    </div>
                </div>
            </div>

            <div class="box-shadow rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                {!! view_render_event('admin.contacts.persons.edit.form_controls.before') !!}

                <x-admin::attributes
                    :custom-attributes="app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                        ['code', 'NOTIN', ['organization_id']],
                        'entity_type' => 'persons',
                    ])"
                    :custom-validations="[
                        'name' => [
                            'min:2',
                            'max:100',
                        ],
                        'job_title' => [
                            'max:100',
                        ],
                    ]"
                    :entity="$person"
                />

                <v-organization></v-organization>

                {!! view_render_event('admin.contacts.persons.edit.form_controls.after') !!}
            </div>
        </div>
    </x-admin::form>

    {!! view_render_event('admin.persons.edit.form.after') !!}

    @pushOnce('scripts')
        <script
            type="text/x-template"
            id="v-organization-template"
        >
            <div>
                <x-admin::attributes
                    :custom-attributes="app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                        ['code', 'IN', ['organization_id']],
                        'entity_type' => 'persons',
                    ])"
                    :entity="$person"
                />

                <template v-if="organizationName">
                    <x-admin::form.control-group.control
                        type="hidden"
                        name="organization_name"
                        v-model="organizationName"
                    />
                </template>
            </div>
        </script>

        <script type="module">
            app.component('v-organization', {
                template: '#v-organization-template',

                data() {
                    return {
                        organizationName: null,
                    };
                },

                methods: {
                    handleLookupAdded(event) {
                        this.organizationName = event?.name || null;
                    },
                },
            });
        </script>
    @endPushOnce
</x-admin::layouts>
