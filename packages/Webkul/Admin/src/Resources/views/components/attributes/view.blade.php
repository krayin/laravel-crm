<div class="flex flex-col gap-2">
    @foreach ($customAttributes as $attribute)
        @if (view()->exists($typeView = 'admin::components.attributes.view.' . $attribute->type))
            <div class="grid grid-cols-[1fr_2fr] gap-2">
                <div class="label">{{ $attribute->name }}</div>

                <div class="font-medium">
                    @include ($typeView, ['value' => isset($entity) ? $entity[$attribute->code] : null])
                </div>
            </div>
        @endif
    @endforeach
</div>