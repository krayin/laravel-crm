@aware(['product' => null])

<div class="box-shadow rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
    <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">
        @lang('admin::app.products.create.tabs.general')
    </p>

    {!! view_render_event('admin.products.tabs.general.before', ['product' => $product]) !!}

    {{-- Product Type Selection --}}
    <div class="mb-6">
        <x-admin::form.control-group class="w-full">
            <x-admin::form.control-group.label class="required">
                @lang('admin::app.products.create.type')
            </x-admin::form.control-group.label>

            <x-admin::form.control-group.control
                type="select"
                name="type"
                rules="required"
                :value="old('type', $product->type ?? 'product')"
                :label="trans('admin::app.products.create.type')"
                :placeholder="trans('admin::app.products.create.type')"
                id="product-type-select"
                onchange="handleProductTypeChange(this.value)"
            >
                <option value="product">@lang('admin::app.products.create.product')</option>
                <option value="service">@lang('admin::app.products.create.service')</option>
                <option value="digital">@lang('admin::app.products.create.digital')</option>
            </x-admin::form.control-group.control>

            <x-admin::form.control-group.error control-name="type" />
        </x-admin::form.control-group>
    </div>

    {{-- Basic Product Information --}}
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        {{-- Product Name --}}
        <x-admin::form.control-group class="w-full">
            <x-admin::form.control-group.label class="required">
                @lang('admin::app.products.create.name')
            </x-admin::form.control-group.label>

            <x-admin::form.control-group.control
                type="text"
                name="name"
                rules="required"
                :value="old('name', $product->name ?? '')"
                :label="trans('admin::app.products.create.name')"
                :placeholder="trans('admin::app.products.create.name')"
            />

            <x-admin::form.control-group.error control-name="name" />
        </x-admin::form.control-group>

        {{-- SKU --}}
        <x-admin::form.control-group class="w-full">
            <x-admin::form.control-group.label class="required">
                @lang('admin::app.products.create.sku')
            </x-admin::form.control-group.label>

            <x-admin::form.control-group.control
                type="text"
                name="sku"
                rules="required"
                :value="old('sku', $product->sku ?? '')"
                :label="trans('admin::app.products.create.sku')"
                :placeholder="trans('admin::app.products.create.sku')"
            />

            <x-admin::form.control-group.error control-name="sku" />
        </x-admin::form.control-group>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        {{-- Reference Code --}}
        <x-admin::form.control-group class="w-full">
            <x-admin::form.control-group.label>
                @lang('admin::app.products.create.reference')
            </x-admin::form.control-group.label>

            <x-admin::form.control-group.control
                type="text"
                name="reference"
                :value="old('reference', $product->reference ?? '')"
                :label="trans('admin::app.products.create.reference')"
                :placeholder="trans('admin::app.products.create.reference')"
            />

            <x-admin::form.control-group.error control-name="reference" />
        </x-admin::form.control-group>

        {{-- Barcode --}}
        <x-admin::form.control-group class="w-full">
            <x-admin::form.control-group.label>
                @lang('admin::app.products.create.barcode')
            </x-admin::form.control-group.label>

            <x-admin::form.control-group.control
                type="text"
                name="barcode"
                :value="old('barcode', $product->barcode ?? '')"
                :label="trans('admin::app.products.create.barcode')"
                :placeholder="trans('admin::app.products.create.barcode')"
            />

            <x-admin::form.control-group.error control-name="barcode" />
        </x-admin::form.control-group>
    </div>

    {{-- Description --}}
    <x-admin::form.control-group class="w-full">
        <x-admin::form.control-group.label>
            @lang('admin::app.products.create.description')
        </x-admin::form.control-group.label>

        <x-admin::form.control-group.control
            type="textarea"
            name="description"
            :value="old('description', $product->description ?? '')"
            :label="trans('admin::app.products.create.description')"
            :placeholder="trans('admin::app.products.create.description')"
            rows="4"
        />

        <x-admin::form.control-group.error control-name="description" />
    </x-admin::form.control-group>

    {{-- Sales and Purchase Descriptions --}}
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        {{-- Sales Description --}}
        <x-admin::form.control-group class="w-full">
            <x-admin::form.control-group.label>
                @lang('admin::app.products.create.description_sale')
            </x-admin::form.control-group.label>

            <x-admin::form.control-group.control
                type="textarea"
                name="description_sale"
                :value="old('description_sale', $product->description_sale ?? '')"
                :label="trans('admin::app.products.create.description_sale')"
                :placeholder="trans('admin::app.products.create.description_sale')"
                rows="3"
            />

            <x-admin::form.control-group.error control-name="description_sale" />
        </x-admin::form.control-group>

        {{-- Purchase Description --}}
        <x-admin::form.control-group class="w-full">
            <x-admin::form.control-group.label>
                @lang('admin::app.products.create.description_purchase')
            </x-admin::form.control-group.label>

            <x-admin::form.control-group.control
                type="textarea"
                name="description_purchase"
                :value="old('description_purchase', $product->description_purchase ?? '')"
                :label="trans('admin::app.products.create.description_purchase')"
                :placeholder="trans('admin::app.products.create.description_purchase')"
                rows="3"
            />

            <x-admin::form.control-group.error control-name="description_purchase" />
        </x-admin::form.control-group>
    </div>

    {{-- Physical Properties (only for physical products) --}}
    <div id="physical-properties" class="mt-6" style="display: block;">
        <h4 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white">
            @lang('admin::app.products.create.physical_properties')
        </h4>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            {{-- Weight --}}
            <x-admin::form.control-group class="w-full">
                <x-admin::form.control-group.label>
                    @lang('admin::app.products.create.weight')
                </x-admin::form.control-group.label>

                <x-admin::form.control-group.control
                    type="number"
                    name="weight"
                    step="0.001"
                    :value="old('weight', $product->weight ?? '')"
                    :label="trans('admin::app.products.create.weight')"
                    :placeholder="trans('admin::app.products.create.weight')"
                />

                <x-admin::form.control-group.error control-name="weight" />
            </x-admin::form.control-group>

            {{-- Volume --}}
            <x-admin::form.control-group class="w-full">
                <x-admin::form.control-group.label>
                    @lang('admin::app.products.create.volume')
                </x-admin::form.control-group.label>

                <x-admin::form.control-group.control
                    type="number"
                    name="volume"
                    step="0.001"
                    :value="old('volume', $product->volume ?? '')"
                    :label="trans('admin::app.products.create.volume')"
                    :placeholder="trans('admin::app.products.create.volume')"
                />

                <x-admin::form.control-group.error control-name="volume" />
            </x-admin::form.control-group>

            {{-- Status --}}
            <x-admin::form.control-group class="w-full">
                <x-admin::form.control-group.label class="required">
                    @lang('admin::app.products.create.status')
                </x-admin::form.control-group.label>

                <x-admin::form.control-group.control
                    type="select"
                    name="status"
                    rules="required"
                    :value="old('status', $product->status ?? 'active')"
                    :label="trans('admin::app.products.create.status')"
                >
                    <option value="active">@lang('admin::app.products.create.statuses.active')</option>
                    <option value="inactive">@lang('admin::app.products.create.statuses.inactive')</option>
                    <option value="archived">@lang('admin::app.products.create.statuses.archived')</option>
                </x-admin::form.control-group.control>

                <x-admin::form.control-group.error control-name="status" />
            </x-admin::form.control-group>
        </div>
    </div>

    {{-- Business Settings --}}
    <div class="mt-6">
        <h4 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white">
            @lang('admin::app.products.create.business_settings')
        </h4>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            {{-- Enable Sales --}}
            <x-admin::form.control-group class="w-full">
                <div class="flex items-start gap-2.5">
                    <label for="enable_sales" class="flex items-center gap-2.5 cursor-pointer">
                        <input
                            type="checkbox"
                            name="enable_sales"
                            id="enable_sales"
                            value="1"
                            class="peer hidden"
                            {{ old('enable_sales', $product->enable_sales ?? true) ? 'checked' : '' }}
                        />
                        <span class="icon-checkbox-outline peer-checked:icon-checkbox-select cursor-pointer rounded-md text-2xl text-gray-600 peer-checked:text-brandColor"></span>
                        <span class="text-sm text-gray-800 dark:text-white">
                            @lang('admin::app.products.create.enable_sales')
                        </span>
                    </label>
                </div>
                
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    @lang('admin::app.products.create.enable_sales_help')
                </p>
            </x-admin::form.control-group>

            {{-- Enable Purchase --}}
            <x-admin::form.control-group class="w-full">
                <div class="flex items-start gap-2.5">
                    <label for="enable_purchase" class="flex items-center gap-2.5 cursor-pointer">
                        <input
                            type="checkbox"
                            name="enable_purchase"
                            id="enable_purchase"
                            value="1"
                            class="peer hidden"
                            {{ old('enable_purchase', $product->enable_purchase ?? true) ? 'checked' : '' }}
                        />
                        <span class="icon-checkbox-outline peer-checked:icon-checkbox-select cursor-pointer rounded-md text-2xl text-gray-600 peer-checked:text-brandColor"></span>
                        <span class="text-sm text-gray-800 dark:text-white">
                            @lang('admin::app.products.create.enable_purchase')
                        </span>
                    </label>
                </div>
                
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    @lang('admin::app.products.create.enable_purchase_help')
                </p>
            </x-admin::form.control-group>

            {{-- Is Favorite --}}
            <x-admin::form.control-group class="w-full">
                <div class="flex items-start gap-2.5">
                    <label for="is_favorite" class="flex items-center gap-2.5 cursor-pointer">
                        <input
                            type="checkbox"
                            name="is_favorite"
                            id="is_favorite"
                            value="1"
                            class="peer hidden"
                            {{ old('is_favorite', $product->is_favorite ?? false) ? 'checked' : '' }}
                        />
                        <span class="icon-checkbox-outline peer-checked:icon-checkbox-select cursor-pointer rounded-md text-2xl text-gray-600 peer-checked:text-brandColor"></span>
                        <span class="text-sm text-gray-800 dark:text-white">
                            @lang('admin::app.products.create.is_favorite')
                        </span>
                    </label>
                </div>
                
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    @lang('admin::app.products.create.is_favorite_help')
                </p>
            </x-admin::form.control-group>
        </div>
    </div>

    {!! view_render_event('admin.products.tabs.general.after', ['product' => $product]) !!}
</div>

@pushOnce('scripts')
<script type="text/javascript">
    function handleProductTypeChange(type) {
        const physicalProps = document.getElementById('physical-properties');
        if (physicalProps) {
            physicalProps.style.display = type === 'product' ? 'block' : 'none';
        }
    }
    
    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        const typeSelect = document.getElementById('product-type-select');
        if (typeSelect) {
            handleProductTypeChange(typeSelect.value);
        }
    });
</script>
@endPushOnce