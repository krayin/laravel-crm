<x-admin::layouts>
    <!--Page title -->
    <x-slot:title>
        @lang('Create Person')
    </x-slot>

    <!--Create Page Form -->
    <x-admin::form
        :action="route('admin.contacts.persons.store')"
        enctype="multipart/form-data"
    >

        {!! view_render_event('bagisto.admin.cms.pages.create.create_form_controls.before') !!}

        <div class="flex items-center justify-between gap-4 max-sm:flex-wrap">
            <p class="text-xl font-bold text-gray-800 dark:text-white">
                @lang('Create Person')
            </p>

            <div class="flex items-center gap-x-2.5">
                <!-- Back Button -->
                <a
                    href="{{ route('admin.contacts.persons.index') }}"
                    class="transparent-button hover:bg-gray-200 dark:text-white dark:hover:bg-gray-800"
                >
                    @lang('Back')
                </a>

                <!--Save Button -->
                <button
                    type="submit"
                    class="primary-button"
                >
                    @lang('Save')
                </button>
            </div>
        </div>

        <!-- body content -->
        {!! view_render_event('bagisto.admin.cms.pages.create.create_form_controls.after') !!}

        <div class="mt-3.5 flex gap-2.5 max-xl:flex-wrap">
            <div class="flex flex-1 flex-col gap-2 max-xl:flex-auto">
                <div class="box-shadow rounded bg-white p-4 dark:bg-gray-900">
                    
                    @include('admin::common.custom-attributes.edit', [
                        'customAttributes' => app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                            'entity_type' => 'persons',
                        ]),
                    ])
                  
                </div>
            </div>
        </div>

        {!! view_render_event('bagisto.admin.cms.pages.create.create_form_controls.after') !!}

    </x-admin::form>
</x-admin::layouts>
