@php
    $options = $attribute->lookup_type
        ? app('Webkul\Attribute\Repositories\AttributeRepository')->getLookUpOptions($attribute->lookup_type)
        : $attribute->options()->orderBy('sort_order')->get();

    $selectedOption = old($attribute->code, $value);

    $selectedOption = is_array($selectedOption) ? $selectedOption : explode(',', $selectedOption);
@endphp

<input type="hidden" name="{{ $attribute->code }}" />

@foreach ($options as $option)
    <x-admin::form.control-group class="!mb-2 flex items-center gap-2.5">
        <x-admin::form.control-group.control
            type="checkbox"
            :id="$option->id"
            name="{{ $attribute->code }}[]"
            :value="$option->id"
            :for="$option->id"
            :label="$option->name"
            :checked="in_array($option->id, $selectedOption)"
        />

        <label
            class="cursor-pointer text-xs font-medium text-gray-600 dark:text-gray-300"
            for="{{ $option->id }}"
        >
            {{ $option->name }}
        </label>
    </x-admin::form.control-group>
@endforeach
