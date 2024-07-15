<x-admin::layouts>
    <x-slot:title>
        @lang('admin::app.customers.customers.index.title')
    </x-slot>

    <div class="flex items-center justify-between">
        <p class="text-xl font-bold text-gray-800 dark:text-white">
            @lang('admin::app.customers.customers.index.title')
        </p>

        <div class="flex items-center gap-x-2.5">
            <!-- Create button for person -->
            <div class="flex items-center gap-x-2.5">
                <button
                    class="primary-button"
                    @click="$refs.createCustomerComponent.openModal()"
                >
                    @lang('admin::app.customers.customers.index.create.create-btn')
                </button>
            </div>
        </div>
    </div>

    {!! view_render_event('krayin.admin.person.datagrid.index.before') !!}

    <x-admin::datagrid src="{{ route('admin.contacts.persons.index') }}" />

    {!! view_render_event('krayin.admin.person.datagrid.index.after') !!}
</x-admin::layouts>