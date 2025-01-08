
<x-admin::layouts>
    <!-- Page Title -->
    <x-slot:title>
        @lang('consignment::app.consignments.edit.title')
    </x-slot>

    {!! view_render_event('admin.consignments.edit.form.before') !!}

    <x-admin::form
        :action="route('admin.consignment.update', $consignment->id)"
        enctype="multipart/form-data"
        method="PUT"
    >
        <div class="flex flex-col gap-4">
            <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                <div class="flex flex-col gap-2">
                    <div class="flex cursor-pointer items-center">
                        <!-- Breadcrumbs -->
                        <x-admin::breadcrumbs
                            name="consignment.edit"
                            :entity="$consignment"
                        />
                    </div>

                    <div class="text-xl font-bold dark:text-white">
                        @lang('consignment::app.consignments.edit.title')
                    </div>
                </div>

                <div class="flex items-center gap-x-2.5">
                    <div class="flex items-center gap-x-2.5">
                        {!! view_render_event('admin.consignments.edit.create_button.before', ['consignment' => $consignment]) !!}

                        <!-- Edit button for consignment -->
                        <button
                            type="submit"
                            class="primary-button"
                        >
                            @lang('consignment::app.consignments.create.save-btn')
                        </button>

                        {!! view_render_event('admin.consignments.edit.create_button.after', ['consignment' => $consignment]) !!}
                    </div>
                </div>
            </div>

            <div class="flex gap-2.5 max-xl:flex-wrap">
                <!-- Left sub-component -->
                <div class="flex flex-1 flex-col gap-2 max-xl:flex-auto">
                    <div class="box-shadow rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                        <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">
                            @lang('consignment::app.consignments.create.general')
                        </p>

                        {!! view_render_event('admin.consignments.edit.attributes.before', ['consignment' => $consignment]) !!}

                        <div class="flex gap-10">
                            <x-admin::attributes
                            :custom-attributes="app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                                'entity_type' => 'Consignment', ['code', 'IN', ['consignment_id','product_id']],
                            ])->sortBy('sort_order')":entity="$consignment"
                        />
                        </div>
                        <div class="flex gap-10">
                            <x-admin::attributes
                            :custom-attributes="app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                                'entity_type' => 'Consignment', ['code', 'IN', ['quantity','amount']],
                            ])->sortBy('sort_order')":entity="$consignment"
                        />
                        </div>
                        <div class="w-1/2 center">
                            <x-admin::attributes
                            :custom-attributes="app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                                'entity_type' => 'Consignment', ['code', 'IN', ['date']],
                            ])->sortBy('sort_order')":entity="$consignment"
                        />
                        </div>



                        {!! view_render_event('admin.consignments.edit.attributes.after', ['consignment' => $consignment]) !!}
                    </div>
                </div>


            </div>
        </div>
    </x-admin::form>

    {!! view_render_event('admin.consignments.edit.form.after') !!}
</x-admin::layouts>
