<div class="flex items-center gap-2">
    @if ($value)
        <a 
            href="{{ route('admin.settings.attributes.download', ['path' => $value]) }}"
            target="_blank"
        >
            <div class="w-full max-w-max cursor-pointer rounded-md p-1.5 text-gray-600 hover:bg-gray-200 active:border-gray-300 dark:text-gray-300 dark:hover:bg-gray-800">
                <i class="icon-download text-2xl"></i>
            </div>
        </a>
    @endif

    <x-admin::form.control-group.control
        type="file"
        :id="$attribute->code"
        :name="$attribute->code"
        :rules="$validations"
        :label="$attribute->name"
    />
</div>

@if ($value)
    <div class="flex cursor-pointer items-center gap-2.5">
        <x-admin::form.control-group.control
            type="checkbox"
            name="{{ $attribute->code }}[delete]"
            id="{{ $attribute->code }}[delete]"
            for="{{ $attribute->code }}[delete]"
            value="1"
        />

        <label
            class="cursor-pointer !text-gray-600 dark:!text-gray-300"
            for="{{ $attribute->code }}[delete]"
        >
            @lang('admin::app.components.attributes.edit.delete')
        </label>
    </div>
@endif