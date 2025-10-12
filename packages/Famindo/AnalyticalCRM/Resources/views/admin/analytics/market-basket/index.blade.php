<x-admin::layouts>
    <x-slot:title>
        Market Basket (Apriori)
    </x-slot>

    <div class="flex flex-col gap-4">
        <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
            <div class="flex flex-col gap-2">
                <x-admin::breadcrumbs name="configuration" />

                <div class="text-xl font-bold dark:text-white">
                    Market Basket (Apriori)
                </div>
            </div>

            <div class="flex items-center gap-x-2.5">
                <a
                    href="{{ route('admin.analytics.market_basket.index', ['export' => 1, 'format' => 'csv']) }}"
                    class="secondary-button"
                >
                    Export All (CSV)
                </a>

                <a
                    href="{{ route('admin.analytics.market_basket.index', ['export' => 1, 'format' => 'xlsx']) }}"
                    class="secondary-button"
                >
                    Export All (XLSX)
                </a>
            </div>
        </div>

        <div class="grid gap-4 lg:grid-cols-3">
            <div class="box-shadow rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900 lg:col-span-1">
                <div class="mb-2 text-base font-semibold">Run Analysis</div>

                <x-admin::form :action="route('admin.analytics.market_basket.run')" method="POST">
                    @csrf

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <x-admin::form.control-group.label>
                                From
                            </x-admin::form.control-group.label>
                            <x-admin::form.control-group.control type="date" name="from" />
                        </div>

                        <div>
                            <x-admin::form.control-group.label>
                                To
                            </x-admin::form.control-group.label>
                            <x-admin::form.control-group.control type="date" name="to" />
                        </div>
                    </div>

                    <div class="mt-3 grid grid-cols-3 gap-3">
                        <div>
                            <x-admin::form.control-group.label>
                                Support
                            </x-admin::form.control-group.label>
                            <x-admin::form.control-group.control type="number" name="support" step="0.01" min="0" max="1" value="0.05" />
                        </div>

                        <div>
                            <x-admin::form.control-group.label>
                                Confidence
                            </x-admin::form.control-group.label>
                            <x-admin::form.control-group.control type="number" name="confidence" step="0.01" min="0" max="1" value="0.6" />
                        </div>

                        <div>
                            <x-admin::form.control-group.label>
                                Min Items
                            </x-admin::form.control-group.label>
                            <x-admin::form.control-group.control type="number" name="min_items" min="1" value="2" />
                        </div>
                    </div>

                    <div class="mt-3 flex items-center gap-3">
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="persist" value="1" class="rounded" />
                            <span>Persist Transactions</span>
                        </label>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="primary-button">Run & Save Rules</button>
                    </div>
                </x-admin::form>
            </div>

            <div class="box-shadow rounded-lg border border-gray-200 bg-white p-0 dark:border-gray-800 dark:bg-gray-900 lg:col-span-2">
                <x-admin::datagrid :src="route('admin.analytics.market_basket.index')">
                    <x-admin::shimmer.datagrid />
                </x-admin::datagrid>
            </div>
        </div>
    </div>
</x-admin::layouts>
