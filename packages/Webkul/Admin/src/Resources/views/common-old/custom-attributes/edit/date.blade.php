<x-admin::flat-picker.date ::allow-input="false">
    <input
        type="text"
        name="{{ $attribute->code }}"
        value="{{ old($attribute->code) ?? $value }}"
        class="flex min-h-[39px] w-full rounded-md border px-3 py-2 text-sm text-gray-600 transition-all hover:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400"
        placeholder="{{ $attribute->name }}"
    />
</x-admin::flat-picker.date>