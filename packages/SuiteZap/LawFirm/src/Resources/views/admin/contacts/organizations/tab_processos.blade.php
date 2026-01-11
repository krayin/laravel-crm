<div
    class="flex w-full flex-col gap-4 rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
    <div class="flex items-center justify-between">
        <p class="text-base font-bold text-gray-800 dark:text-white">
            {{ trans('lawfirm::app.processos.title') }}
        </p>
    </div>

    <div class="block w-full">
        @if(isset($organization) && $organization->id)
            <x-admin::datagrid :src="route('admin.contacts.organizations.processos', $organization->id)" />
        @endif
    </div>
</div>