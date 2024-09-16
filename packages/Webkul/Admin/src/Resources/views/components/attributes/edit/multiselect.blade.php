@php
    $options = $attribute->lookup_type
        ? app('Webkul\Attribute\Repositories\AttributeRepository')->getLookUpOptions($attribute->lookup_type)
        : $attribute->options()->orderBy('sort_order')->get();

    $selectedOption = old($attribute->code) ?: $value;
@endphp

<v-field
    type="select"
    id="{{ $attribute->code }}"
    name="{{ $attribute->code }}[]"
    rules="{{ $validations }}"
    label="{{ $attribute->name }}"
    placeholder="{{ $attribute->name }}"
    multiple
>
    <select
        name="{{ $attribute->code }}[]"
        class="flex min-h-[39px] w-full rounded-md border px-3 py-2 text-sm text-gray-600 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400"
        multiple
    >
        @foreach ($options as $option)
            <option
                value="{{ $option->id }}"
                {{ in_array($option->id, is_array($selectedOption) ? $selectedOption : explode(',', $selectedOption)) ? 'selected' : ''}}
            >
                {{ $option->name }}
            </option>
        @endforeach
    </select>
</v-field>