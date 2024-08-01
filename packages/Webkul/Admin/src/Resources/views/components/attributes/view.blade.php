<div class="flex flex-col gap-1">
    @foreach ($customAttributes as $attribute)
        @if (view()->exists($typeView = 'admin::common.custom-attributes.view.' . $attribute->type))
            <div class="grid grid-cols-[1fr_2fr]">
                <div class="">{{ $attribute->name }}</div>

                <div class="font-medium">
                    @include ($typeView, ['value' => isset($entity) ? $entity[$attribute->code] : null])
                </div>
            </div>
        @endif
    @endforeach
</div>