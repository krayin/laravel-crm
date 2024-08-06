{{-- {{ ! is_null($value) ? core()->formatBasePrice($value) : __('admin::app.common.not-available') }} --}}

<x-admin::form.control-group class="!mb-0">
    <x-admin::form.control-group.control
        type="inline"
        :name="$attribute->name"
        :value="$value"
        rules="required|decimal:4"
        ::errors="errors"
        :label="trans('admin::app.quotes.create.discount-amount')"
        :placeholder="trans('admin::app.quotes.create.discount-amount')"
        @on-change="(value) => product.discount_amount = value"
    />
</x-admin::form.control-group>