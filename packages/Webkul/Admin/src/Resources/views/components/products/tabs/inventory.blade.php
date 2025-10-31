@aware(['product' => null])

<div class="box-shadow rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
    <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">
        @lang('admin::app.products.create.tabs.inventory')
    </p>

    {!! view_render_event('admin.products.tabs.inventory.before', ['product' => $product]) !!}

    {{-- Pricing Section --}}
    <div class="mb-6">
        <h4 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white">
            @lang('admin::app.products.create.pricing')
        </h4>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            {{-- Sale Price --}}
            <x-admin::form.control-group class="w-full">
                <x-admin::form.control-group.label class="required">
                    @lang('admin::app.products.create.price')
                </x-admin::form.control-group.label>

                <x-admin::form.control-group.control
                    type="number"
                    name="price"
                    rules="required|numeric|min:0"
                    step="0.01"
                    :value="old('price', $product->price ?? '')"
                    :label="trans('admin::app.products.create.price')"
                    :placeholder="trans('admin::app.products.create.price_placeholder')"
                />

                <x-admin::form.control-group.error control-name="price" />
            </x-admin::form.control-group>

            {{-- Cost Price --}}
            <x-admin::form.control-group class="w-full">
                <x-admin::form.control-group.label>
                    @lang('admin::app.products.create.cost')
                </x-admin::form.control-group.label>

                <x-admin::form.control-group.control
                    type="number"
                    name="cost"
                    step="0.01"
                    :value="old('cost', $product->cost ?? '')"
                    :label="trans('admin::app.products.create.cost')"
                    :placeholder="trans('admin::app.products.create.cost_placeholder')"
                />

                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    @lang('admin::app.products.create.cost_help')
                </p>

                <x-admin::form.control-group.error control-name="cost" />
            </x-admin::form.control-group>
        </div>
    </div>

    {{-- Inventory Section --}}
    <div class="mb-6">
        <h4 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white">
            @lang('admin::app.products.create.inventory')
        </h4>

        {{-- Quantity --}}
        <x-admin::form.control-group class="w-full mb-4">
            <x-admin::form.control-group.label class="required">
                @lang('admin::app.products.create.quantity')
            </x-admin::form.control-group.label>

            <x-admin::form.control-group.control
                type="number"
                name="quantity"
                rules="required|numeric|min:0"
                :value="old('quantity', $product->quantity ?? '0')"
                :label="trans('admin::app.products.create.quantity')"
                :placeholder="trans('admin::app.products.create.quantity_placeholder')"
            />

            <x-admin::form.control-group.error control-name="quantity" />
        </x-admin::form.control-group>

        {{-- Inventory Tracking Options --}}
        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            {{-- Minimum Stock Level --}}
            <x-admin::form.control-group class="w-full">
                <x-admin::form.control-group.label>
                    @lang('admin::app.products.create.minimum_stock')
                </x-admin::form.control-group.label>

                <x-admin::form.control-group.control
                    type="number"
                    name="minimum_stock"
                    :value="old('minimum_stock', $product->minimum_stock ?? '')"
                    :label="trans('admin::app.products.create.minimum_stock')"
                    :placeholder="trans('admin::app.products.create.minimum_stock_placeholder')"
                />

                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    @lang('admin::app.products.create.minimum_stock_help')
                </p>

                <x-admin::form.control-group.error control-name="minimum_stock" />
            </x-admin::form.control-group>

            {{-- Maximum Stock Level --}}
            <x-admin::form.control-group class="w-full">
                <x-admin::form.control-group.label>
                    @lang('admin::app.products.create.maximum_stock')
                </x-admin::form.control-group.label>

                <x-admin::form.control-group.control
                    type="number"
                    name="maximum_stock"
                    :value="old('maximum_stock', $product->maximum_stock ?? '')"
                    :label="trans('admin::app.products.create.maximum_stock')"
                    :placeholder="trans('admin::app.products.create.maximum_stock_placeholder')"
                />

                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    @lang('admin::app.products.create.maximum_stock_help')
                </p>

                <x-admin::form.control-group.error control-name="maximum_stock" />
            </x-admin::form.control-group>

            {{-- Reorder Point --}}
            <x-admin::form.control-group class="w-full">
                <x-admin::form.control-group.label>
                    @lang('admin::app.products.create.reorder_point')
                </x-admin::form.control-group.label>

                <x-admin::form.control-group.control
                    type="number"
                    name="reorder_point"
                    :value="old('reorder_point', $product->reorder_point ?? '')"
                    :label="trans('admin::app.products.create.reorder_point')"
                    :placeholder="trans('admin::app.products.create.reorder_point_placeholder')"
                />

                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    @lang('admin::app.products.create.reorder_point_help')
                </p>

                <x-admin::form.control-group.error control-name="reorder_point" />
            </x-admin::form.control-group>
        </div>
    </div>

    {{-- Warehouse Locations (for existing products) --}}
    @if($product && $product->inventories)
        <div class="mb-6">
            <h4 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white">
                @lang('admin::app.products.create.warehouse_locations')
            </h4>

            <div class="overflow-x-auto">
                <table class="min-w-full border border-gray-200 dark:border-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                @lang('admin::app.products.create.warehouse')
                            </th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                @lang('admin::app.products.create.location')
                            </th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                @lang('admin::app.products.create.in_stock')
                            </th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                @lang('admin::app.products.create.allocated')
                            </th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                @lang('admin::app.products.create.available')
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-900 dark:divide-gray-700">
                        @foreach($product->inventories as $inventory)
                            <tr>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    {{ $inventory->warehouse->name ?? 'N/A' }}
                                </td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    {{ $inventory->location->name ?? 'N/A' }}
                                </td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    {{ $inventory->in_stock }}
                                </td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    {{ $inventory->allocated }}
                                </td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ ($inventory->in_stock - $inventory->allocated) > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $inventory->in_stock - $inventory->allocated }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- Shipping Information --}}
    <div class="mb-6">
        <h4 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white">
            @lang('admin::app.products.create.shipping_information')
        </h4>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            {{-- Length --}}
            <x-admin::form.control-group class="w-full">
                <x-admin::form.control-group.label>
                    @lang('admin::app.products.create.length')
                </x-admin::form.control-group.label>

                <x-admin::form.control-group.control
                    type="number"
                    name="length"
                    step="0.01"
                    :value="old('length', $product->length ?? '')"
                    :label="trans('admin::app.products.create.length')"
                    :placeholder="trans('admin::app.products.create.length_placeholder')"
                />

                <x-admin::form.control-group.error control-name="length" />
            </x-admin::form.control-group>

            {{-- Width --}}
            <x-admin::form.control-group class="w-full">
                <x-admin::form.control-group.label>
                    @lang('admin::app.products.create.width')
                </x-admin::form.control-group.label>

                <x-admin::form.control-group.control
                    type="number"
                    name="width"
                    step="0.01"
                    :value="old('width', $product->width ?? '')"
                    :label="trans('admin::app.products.create.width')"
                    :placeholder="trans('admin::app.products.create.width_placeholder')"
                />

                <x-admin::form.control-group.error control-name="width" />
            </x-admin::form.control-group>

            {{-- Height --}}
            <x-admin::form.control-group class="w-full">
                <x-admin::form.control-group.label>
                    @lang('admin::app.products.create.height')
                </x-admin::form.control-group.label>

                <x-admin::form.control-group.control
                    type="number"
                    name="height"
                    step="0.01"
                    :value="old('height', $product->height ?? '')"
                    :label="trans('admin::app.products.create.height')"
                    :placeholder="trans('admin::app.products.create.height_placeholder')"
                />

                <x-admin::form.control-group.error control-name="height" />
            </x-admin::form.control-group>
        </div>
    </div>

    {{-- Tax Information --}}
    <div class="mb-6">
        <h4 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white">
            @lang('admin::app.products.create.tax_information')
        </h4>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            {{-- Tax Class --}}
            <x-admin::form.control-group class="w-full">
                <x-admin::form.control-group.label>
                    @lang('admin::app.products.create.tax_class')
                </x-admin::form.control-group.label>

                <x-admin::form.control-group.control
                    type="select"
                    name="tax_class_id"
                    :value="old('tax_class_id', $product->tax_class_id ?? '')"
                    :label="trans('admin::app.products.create.tax_class')"
                >
                    <option value="">@lang('admin::app.products.create.select_tax_class')</option>
                    <option value="1">Standard Rate</option>
                    <option value="2">Reduced Rate</option>
                    <option value="3">Zero Rate</option>
                    <option value="4">Exempt</option>
                </x-admin::form.control-group.control>

                <x-admin::form.control-group.error control-name="tax_class_id" />
            </x-admin::form.control-group>

            {{-- Tax Exempt --}}
            <x-admin::form.control-group class="w-full flex items-center">
                <x-admin::form.control-group.control
                    type="checkbox"
                    name="is_tax_exempt"
                    value="1"
                    :checked="old('is_tax_exempt', $product->is_tax_exempt ?? false)"
                    :label="trans('admin::app.products.create.is_tax_exempt')"
                />
            </x-admin::form.control-group>
        </div>
    </div>

    {!! view_render_event('admin.products.tabs.inventory.after', ['product' => $product]) !!}
</div>