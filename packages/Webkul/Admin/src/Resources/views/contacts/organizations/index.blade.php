<x-admin::layouts>
    <x-slot:title>
        @lang('admin::app.customers.customers.index.title')
    </x-slot>

    <div class="flex items-center justify-between">
        <p class="text-xl font-bold text-gray-800 dark:text-white">
            Organizations
        </p>

        <div class="flex items-center gap-x-2.5">
            <!-- Create button for person -->
            <div class="flex items-center gap-x-2.5">
                <button
                    class="primary-button"
                    @click="$refs.createCustomerComponent.openModal()"
                >
                    Create
                </button>
            </div>
        </div>
    </div>

    {!! view_render_event('krayin.admin.organizations.datagrid.index.before') !!}

    <x-admin::datagrid src="{{ route('admin.contacts.organizations.index') }}" />

    {!! view_render_event('krayin.admin.organizations.datagrid.index.after') !!}
</x-admin::layouts>
