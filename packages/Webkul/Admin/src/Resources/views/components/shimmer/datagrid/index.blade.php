@props(['isMultiRow' => false])

<div>
    <x-admin::shimmer.datagrid.toolbar />

    <div class="flex">
        <div class="w-full">
            <div class="table-responsive box-shadow grid w-full overflow-hidden rounded border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                <x-admin::shimmer.datagrid.table.head :isMultiRow="$isMultiRow" />

                <x-admin::shimmer.datagrid.table.body :isMultiRow="$isMultiRow" />
            </div>
        </div>
    </div>
</div>
