
<x-admin::layouts>
    <!-- Page Title -->
    <x-slot:title>
        @lang('admin::app.contacts.organizations.create.title')
    </x-slot>

    {!! view_render_event('krayin.admin.organizations.create.form.before') !!}

    <x-admin::form
        :action="route('admin.contacts.organizations.store')"
        method="POST"
    >
        <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
            <div class="flex flex-col gap-2">
                <div class="flex cursor-pointer items-center">
                    <x-admin::breadcrumbs name="contacts.organizations.create" />
                </div>

                <div class="text-xl font-bold dark:text-gray-300">
                    @lang('admin::app.contacts.organizations.create.title')
                </div>
            </div>

            <div class="flex items-center gap-x-2.5">
                <!-- Create button for person -->
                <div class="flex items-center gap-x-2.5">
                    <button
                        type="submit"
                        class="primary-button"
                    >
                        @lang('admin::app.contacts.organizations.create.save-btn')
                    </button>
                </div>
            </div>
        </div>

        <div class="mt-3.5 flex gap-2.5 max-xl:flex-wrap">
            <!-- Left sub-component -->
            <div class="flex flex-1 flex-col gap-2 max-xl:flex-auto">
                <div class="box-shadow rounded bg-white p-4 dark:bg-gray-900">
                    {!! view_render_event('admin.contacts.organizations.create.form_controls.before') !!}

                    <x-admin::attributes
                        :custom-attributes="app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                            'entity_type' => 'organizations',
                        ])"
                    />

                    {!! view_render_event('admin.contacts.organizations.edit.form_controls.after') !!}
                </div>
            </div>
        </div>
    </x-admin::form>

    {!! view_render_event('krayin.admin.organizations.create.form.after') !!}

</x-admin::layouts>
