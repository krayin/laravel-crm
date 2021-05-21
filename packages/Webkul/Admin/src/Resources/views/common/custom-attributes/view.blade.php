@foreach ($customAttributes as $attribute)

    @if (view()->exists($typeView = 'admin::common.custom-attributes.view.' . $attribute->type))

        <div class="attribute-value-row">
            <div class="label">{{ $attribute->name }}</div>

            <div class="value">
                @include ($typeView, ['value' => isset($entity) ? $entity[$attribute->code] : null])
            </div>
        </div>

    @endif

@endforeach