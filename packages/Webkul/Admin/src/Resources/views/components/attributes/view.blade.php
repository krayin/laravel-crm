@props([
    'customAttributes' => [],
    'entity' => null,
    'allowEdit' => false,
    'url' => null,
])

<div class="flex flex-col gap-1">
    @foreach ($customAttributes as $attribute)
        @if (view()->exists($typeView = 'admin::components.attributes.view.' . $attribute->type))
            @php
                $attributeLabel = \Lang::has('admin::app.attributes.' . $attribute->code)
                    ? __('admin::app.attributes.' . $attribute->code)
                    : $attribute->name;
            @endphp

            <div class="grid grid-cols-[1fr_2fr] items-center gap-1">
                <div class="label dark:text-white">{{ $attributeLabel }}</div>

                <div class="font-medium dark:text-white">
                    @include ($typeView, [
                        'attribute' => $attribute,
                        'value' => isset($entity) ? $entity[$attribute->code] : null,
                        'allowEdit' => $allowEdit,
                        'url' => $url,
                        'label' => $attributeLabel,
                    ])
                </div>
            </div>
        @endif
    @endforeach
</div>