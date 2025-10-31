@aware(['product' => null])

<div class="box-shadow rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
    <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">
        @lang('admin::app.products.create.tabs.categorization')
    </p>

    {!! view_render_event('admin.products.tabs.categorization.before', ['product' => $product]) !!}

    {{-- Category Selection using Lookup Component --}}
    @php
        $categoryData = null;
        if ($product && $product->category_id) {
            $categoryData = [
                'id' => $product->category_id,
                'name' => $product->category->name ?? '',
                'full_name' => $product->category->full_name ?? ($product->category->name ?? '')
            ];
        }
    @endphp
    
    @include('admin::components.products.category-lookup', ['categoryData' => $categoryData])

    {{-- Custom Attributes Section --}}
    <div class="mt-6">
        <h4 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white">
            @lang('admin::app.products.create.custom_attributes')
        </h4>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            {{-- Brand --}}
            <x-admin::form.control-group class="w-full">
                <x-admin::form.control-group.label>
                    @lang('admin::app.products.create.brand')
                </x-admin::form.control-group.label>

                <x-admin::form.control-group.control
                    type="text"
                    name="brand"
                    :value="old('brand', $product->brand ?? '')"
                    :label="trans('admin::app.products.create.brand')"
                    :placeholder="trans('admin::app.products.create.brand_placeholder')"
                />

                <x-admin::form.control-group.error control-name="brand" />
            </x-admin::form.control-group>

            {{-- Model Number --}}
            <x-admin::form.control-group class="w-full">
                <x-admin::form.control-group.label>
                    @lang('admin::app.products.create.model_number')
                </x-admin::form.control-group.label>

                <x-admin::form.control-group.control
                    type="text"
                    name="model_number"
                    :value="old('model_number', $product->model_number ?? '')"
                    :label="trans('admin::app.products.create.model_number')"
                    :placeholder="trans('admin::app.products.create.model_number_placeholder')"
                />

                <x-admin::form.control-group.error control-name="model_number" />
            </x-admin::form.control-group>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            {{-- Material --}}
            <x-admin::form.control-group class="w-full">
                <x-admin::form.control-group.label>
                    @lang('admin::app.products.create.material')
                </x-admin::form.control-group.label>

                <x-admin::form.control-group.control
                    type="select"
                    name="material"
                    :value="old('material', $product->material ?? '')"
                    :label="trans('admin::app.products.create.material')"
                >
                    <option value="">@lang('admin::app.products.create.select_material')</option>
                    <option value="cotton">Cotton</option>
                    <option value="polyester">Polyester</option>
                    <option value="leather">Leather</option>
                    <option value="metal">Metal</option>
                    <option value="plastic">Plastic</option>
                    <option value="wood">Wood</option>
                    <option value="glass">Glass</option>
                    <option value="ceramic">Ceramic</option>
                </x-admin::form.control-group.control>

                <x-admin::form.control-group.error control-name="material" />
            </x-admin::form.control-group>

            {{-- Color --}}
            <x-admin::form.control-group class="w-full">
                <x-admin::form.control-group.label>
                    @lang('admin::app.products.create.color')
                </x-admin::form.control-group.label>

                <div class="flex items-center gap-2">
                    <x-admin::form.control-group.control
                        type="color"
                        name="color"
                        :value="old('color', $product->color ?? '#000000')"
                        :label="trans('admin::app.products.create.color')"
                        class="w-16 h-10"
                    />

                    <x-admin::form.control-group.control
                        type="text"
                        name="color_name"
                        :value="old('color_name', $product->color_name ?? '')"
                        :placeholder="trans('admin::app.products.create.color_name_placeholder')"
                        class="flex-1"
                    />
                </div>

                <x-admin::form.control-group.error control-name="color" />
            </x-admin::form.control-group>
        </div>

        {{-- Warranty Period --}}
        <x-admin::form.control-group class="w-full">
            <x-admin::form.control-group.label>
                @lang('admin::app.products.create.warranty_period')
            </x-admin::form.control-group.label>

            <div class="flex items-center gap-2">
                <x-admin::form.control-group.control
                    type="number"
                    name="warranty_period"
                    :value="old('warranty_period', $product->warranty_period ?? '')"
                    :label="trans('admin::app.products.create.warranty_period')"
                    :placeholder="trans('admin::app.products.create.warranty_period_placeholder')"
                    min="0"
                    class="flex-1"
                />
                <span class="text-sm text-gray-600 dark:text-gray-400">
                    @lang('admin::app.products.create.months')
                </span>
            </div>

            <x-admin::form.control-group.error control-name="warranty_period" />
        </x-admin::form.control-group>
    </div>

    {{-- Tags Section --}}
    <div class="mt-6">
        <h4 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white">
            @lang('admin::app.products.create.tags')
        </h4>

        <x-admin::form.control-group class="w-full">
            <x-admin::form.control-group.label>
                @lang('admin::app.products.create.product_tags')
            </x-admin::form.control-group.label>

            <x-admin::form.control-group.control
                type="text"
                name="tags"
                :value="old('tags', isset($product) && $product->tags ? $product->tags->pluck('name')->implode(', ') : '')"
                :label="trans('admin::app.products.create.product_tags')"
                :placeholder="trans('admin::app.products.create.tags_placeholder')"
            />

            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                @lang('admin::app.products.create.tags_help')
            </p>

            <x-admin::form.control-group.error control-name="tags" />
        </x-admin::form.control-group>
    </div>

    {{-- SEO Section --}}
    <div class="mt-6">
        <h4 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white">
            @lang('admin::app.products.create.seo_information')
        </h4>

        <div class="grid grid-cols-1 gap-4">
            {{-- Meta Title --}}
            <x-admin::form.control-group class="w-full">
                <x-admin::form.control-group.label>
                    @lang('admin::app.products.create.meta_title')
                </x-admin::form.control-group.label>

                <x-admin::form.control-group.control
                    type="text"
                    name="meta_title"
                    :value="old('meta_title', $product->meta_title ?? '')"
                    :label="trans('admin::app.products.create.meta_title')"
                    :placeholder="trans('admin::app.products.create.meta_title_placeholder')"
                    maxlength="60"
                />

                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    @lang('admin::app.products.create.meta_title_help')
                </p>

                <x-admin::form.control-group.error control-name="meta_title" />
            </x-admin::form.control-group>

            {{-- Meta Description --}}
            <x-admin::form.control-group class="w-full">
                <x-admin::form.control-group.label>
                    @lang('admin::app.products.create.meta_description')
                </x-admin::form.control-group.label>

                <x-admin::form.control-group.control
                    type="textarea"
                    name="meta_description"
                    :value="old('meta_description', $product->meta_description ?? '')"
                    :label="trans('admin::app.products.create.meta_description')"
                    :placeholder="trans('admin::app.products.create.meta_description_placeholder')"
                    rows="3"
                    maxlength="160"
                />

                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    @lang('admin::app.products.create.meta_description_help')
                </p>

                <x-admin::form.control-group.error control-name="meta_description" />
            </x-admin::form.control-group>
        </div>
    </div>

    {!! view_render_event('admin.products.tabs.categorization.after', ['product' => $product]) !!}
</div>