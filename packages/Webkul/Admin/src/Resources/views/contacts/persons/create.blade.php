<x-admin::layouts>
    <!--Page title -->
    <x-slot:title>
        @lang('admin::app.contacts.persons.create.title')
    </x-slot>

    <!--Create Page Form -->
    <x-admin::form
        :action="route('admin.contacts.persons.store')"
        enctype="multipart/form-data"
    >
        <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
            <div class="flex flex-col gap-2">
                <div class="flex cursor-pointer items-center">
                    <x-admin::breadcrumbs name="contacts.persons.create" />
                </div>

                <div class="text-xl font-bold dark:text-gray-300">
                    @lang('admin::app.contacts.persons.create.title')
                </div>
            </div>

            <div class="flex items-center gap-x-2.5">
                <!-- Create button for Person -->
                <div class="flex items-center gap-x-2.5">
                    <button
                        type="submit"
                        class="primary-button"
                    >
                        @lang('admin::app.contacts.persons.create.save-btn')
                    </button>
                </div>
            </div>
        </div>
        
        <div class="mt-3.5 flex gap-2.5 max-xl:flex-wrap">
            <div class="flex flex-1 flex-col gap-2 max-xl:flex-auto">
                <div class="box-shadow rounded bg-white p-4 dark:bg-gray-900">
                    {!! view_render_event('bagisto.admin.cms.pages.create.form_controls.before') !!}

                    @include('admin::common.custom-attributes.edit', [
                        'customAttributes' => app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                            'entity_type' => 'persons',
                        ]),
                    ])

                    {!! view_render_event('bagisto.admin.cms.pages.create.form_controls.after') !!}
                </div>
            </div>
        </div>


    </x-admin::form>
</x-admin::layouts>
