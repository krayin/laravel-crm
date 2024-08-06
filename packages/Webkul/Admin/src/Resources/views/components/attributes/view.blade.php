@props([
    'customAttributes' => [],
    'entity'           => null,
    'allowEdit'        => false,
])

<div class="flex flex-col gap-2">
    @foreach ($customAttributes as $attribute)
        @if (view()->exists($typeView = 'admin::components.attributes.view.' . $attribute->type))
            <div class="grid grid-cols-[1fr_2fr] items-center gap-2">
                <div class="label">{{ $attribute->name }}</div>

                <div class="font-medium">
                    @include ($typeView, [
                        'attribute' => $attribute,
                        'value'     => isset($entity) ? $entity[$attribute->code] : null,
                        'allowEdit' => $allowEdit,
                    ])
                </div>
            </div>
        @endif
    @endforeach
</div>