@php
    $options = $attribute->lookup_type
        ? app('Webkul\Attribute\Repositories\AttributeRepository')->getLookUpEntity($attribute->lookup_type, explode(',', $value))
        : $attribute->options()->whereIn('id', explode(',', $value))->get();
@endphp

@if (count($options))
    
    @foreach ($options as $option)
        <span class="multi-value">{{ $option->name }}</span>
    @endforeach

@else
    
    {{ __('admin::app.common.not-available') }}

@endif