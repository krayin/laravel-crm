@props([
    'title' => '',
    'showSaveButton' => true,
    'showCancelButton' => true,
    'cancelRoute' => 'admin.products.index',
    'extraActions' => [],
    'product' => null,
])

<div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
    <div class="flex flex-col gap-2">
        {!! view_render_event('admin.products.form_header.breadcrumbs.before', ['product' => $product]) !!}

        <!-- Breadcrumbs -->
        @if($product)
            <x-admin::breadcrumbs 
                name="products.edit" 
                :entity="$product" 
            />
        @else
            <x-admin::breadcrumbs name="products.create" />
        @endif

        {!! view_render_event('admin.products.form_header.breadcrumbs.after', ['product' => $product]) !!}
        
        <div class="text-xl font-bold dark:text-white">
            {{ $title ?: ($product ? trans('admin::app.products.edit.title') : trans('admin::app.products.create.title')) }}
        </div>
    </div>

    <div class="flex items-center gap-x-2.5">
        {!! view_render_event('admin.products.form_header.actions.before', ['product' => $product]) !!}

        @if($showCancelButton)
            <a 
                href="{{ route($cancelRoute) }}"
                class="secondary-button"
            >
                @lang('admin::app.products.create.cancel-btn')
            </a>
        @endif

        {{-- Extra custom actions --}}
        @foreach($extraActions as $action)
            @if(isset($action['condition']) ? $action['condition'] : true)
                <a 
                    href="{{ $action['route'] ?? '#' }}"
                    class="{{ $action['class'] ?? 'secondary-button' }}"
                    @if(isset($action['onclick'])) onclick="{{ $action['onclick'] }}" @endif
                >
                    @if(isset($action['icon']))
                        <i class="{{ $action['icon'] }}"></i>
                    @endif
                    {{ $action['label'] }}
                </a>
            @endif
        @endforeach

        @if($showSaveButton)
            <!-- Save button -->
            @if(!$product && bouncer()->hasPermission('products.create'))
                <button
                    type="submit"
                    class="primary-button"
                    form="product-form"
                >
                    @lang('admin::app.products.create.save-btn')
                </button>
            @elseif($product && bouncer()->hasPermission('products.edit'))
                <button
                    type="submit"
                    class="primary-button"
                    form="product-form"
                >
                    @lang('admin::app.products.edit.save-btn')
                </button>
            @endif
        @endif

        {!! view_render_event('admin.products.form_header.actions.after', ['product' => $product]) !!}
    </div>
</div>