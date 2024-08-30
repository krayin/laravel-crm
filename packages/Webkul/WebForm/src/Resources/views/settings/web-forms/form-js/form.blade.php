<x-web_form::layouts>
    <x-slot:title>
        @lang('Form Preview')
    </x-slot>

    <div
        class="flex h-[100vh] items-center justify-center"
        style="background-color: {{ $webForm->background_color }}"
    >
        <div class="flex flex-col items-center gap-5">
            <!-- Logo -->            
            <img
                class="h-10 w-[110px]"
                src="{{ vite()->asset('images/logo.svg') }}"
                alt="krayin"
            />

            <h1 style="color: {{ $webForm->form_title_color }} !important;">{{ $webForm->title }}</h1>

            <p>{{ $webForm->description }}</p>

            <div class="box-shadow flex min-w-[476px] flex-col rounded-lg">
                <!-- Message -->
                <div 
                    id="message"
                    class="mb-4 hidden rounded-lg bg-green-50 p-4 text-sm text-green-800 dark:bg-gray-800 dark:text-green-400"
                    role="alert"
                ></div>

                <!-- Login Form -->
                <form 
                    style="background-color: {{ $webForm->form_background_color }}"
                    id="krayinWebForm"
                >
                    <div class="p-4 dark:border-gray-800">
                        @foreach ($webForm->attributes as $attribute)
                            @php
                                $parentAttribute = $attribute->attribute;

                                $fieldName = $parentAttribute->entity_type . '[' . $parentAttribute->code . ']';
                            @endphp

                            <div class="mt-4 first:mt-0 {{ $parentAttribute->type }}">
                                <label 
                                    class="mb-1.5 flex items-center gap-1 text-sm font-normal text-gray-800 dark:text-white {{ $attribute->is_required ? 'required' : '' }}"
                                    style="color: {{ $webForm->attribute_label_color }} !important;"
                                    for="{{ $attribute->code }}"
                                >
                                    {{ $attribute->name ?? $parentAttribute->name }}
                                </label>

                                @switch($parentAttribute->type)
                                    @case('text')
                                        <input
                                            type="text" 
                                            id="{{ $fieldName }}"
                                            name="{{ $fieldName }}"
                                            {{ $attribute->is_required ? 'required' : '' }}
                                            placeholder="{{ $attribute->placeholder }}"
                                            class="w-full rounded border border-gray-200 px-2.5 py-2 text-sm font-normal text-gray-800 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400"
                                        />

                                        <p 
                                            id="{{ $fieldName }}-error"
                                            class="error-message mt-1 text-xs italic text-red-600"
                                        ></p>

                                        @break;

                                    @case('date')
                                    @case('datetime')
                                        <input
                                            type="text"
                                            name="{{ $fieldName }}"
                                            id="{{ $fieldName }}"
                                            {{ $attribute->is_required ? 'required' : '' }}
                                            class="w-full rounded border border-gray-200 px-2.5 py-2 text-sm font-normal text-gray-800 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400"
                                        />

                                        <p 
                                            id="{{ $fieldName }}-error"
                                            class="error-message mt-1 text-xs italic text-red-600"
                                        ></p>

                                        @break;

                                    @case('textarea')
                                        <textarea
                                            name="{{ $fieldName }}"
                                            id="{{ $fieldName }}"
                                            {{ $attribute->is_required ? 'required' : '' }}
                                            class="w-full rounded border border-gray-200 px-2.5 py-2 text-sm font-normal text-gray-800 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400"
                                        ></textarea>

                                        <p 
                                            id="{{ $fieldName }}-error"
                                            class="error-message mt-1 text-xs italic text-red-600"
                                        ></p>

                                        @break;

                                    @case('email')
                                        <input
                                            type="email"
                                            name="{{ $fieldName }}[0][value]"
                                            placeholder="{{ $attribute->placeholder }}"
                                            id="{{ $fieldName }}"
                                            {{ $attribute->is_required ? 'required' : '' }}
                                            class="w-full rounded border border-gray-200 px-2.5 py-2 text-sm font-normal text-gray-800 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400"
                                        />

                                        <input
                                            type="hidden"
                                            name="{{ $fieldName }}[0][label]"
                                            class="control"
                                            value="work"
                                        />

                                        <p 
                                            id="{{ $fieldName }}[0][value]-error"
                                            class="error-message mt-1 text-xs italic text-red-600"
                                        ></p>

                                        @break;

                                    @case('phone')
                                        <input
                                            type="text"
                                            name="{{ $fieldName }}[0][value]"
                                            placeholder="{{ $attribute->placeholder }}"
                                            id="{{ $fieldName }}"
                                            {{ $attribute->is_required ? 'required' : '' }}
                                            class="w-full rounded border border-gray-200 px-2.5 py-2 text-sm font-normal text-gray-800 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400"
                                        />

                                        <input
                                            type="hidden"
                                            name="{{ $fieldName }}[0][label]"
                                            class="control"
                                            value="work"
                                        />

                                        <p 
                                            id="{{ $fieldName }}[0][value]-error"
                                            class="error-message mt-1 text-xs italic text-red-600"
                                        ></p>

                                        @break;

                                    @case('select')
                                    @case('lookup')
                                        <select
                                            id="{{ $fieldName }}"
                                            name="{{ $fieldName }}"
                                            {{ $attribute->is_required ? 'required' : '' }}
                                            class="w-full rounded border border-gray-200 px-2.5 py-2 text-sm font-normal text-gray-800 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400"
                                        >
                                            @php
                                                $options = $parentAttribute->lookup_type
                                                    ? app('Webkul\Attribute\Repositories\AttributeRepository')->getLookUpOptions($parentAttribute->lookup_type)
                                                    : $parentAttribute->options()->orderBy('sort_order')->get();
                                            @endphp

                                            <option value="" selected="selected" disabled="disabled">{{ __('admin::app.settings.attributes.select') }}</option>

                                            @foreach ($options as $option)
                                                <option value="{{ $option->id }}">
                                                    {{ $option->name }}
                                                </option>
                                            @endforeach

                                        </select>

                                        <p 
                                            id="{{ $fieldName }}-error"
                                            class="error-message mt-1 text-xs italic text-red-600"
                                        ></p>

                                        @break;

                                    @case('multiselect')
                                        <select
                                            id="{{ $fieldName }}"
                                            name="{{ $fieldName }}[]"
                                            multiple
                                            class="w-full rounded border border-gray-200 px-2.5 py-2 text-sm font-normal text-gray-800 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400"
                                        >

                                            @php
                                                $options = $parentAttribute->lookup_type
                                                    ? app('Webkul\Attribute\Repositories\AttributeRepository')->getLookUpOptions($parentAttribute->lookup_type)
                                                    : $parentAttribute->options()->orderBy('sort_order')->get();
                                            @endphp

                                            @foreach ($options as $option)
                                                <option value="{{ $option->id }}">
                                                    {{ $option->name }}
                                                </option>
                                            @endforeach

                                        </select>

                                        <p 
                                            id="{{ $fieldName }}[]-error"
                                            class="error-message mt-1 text-xs italic text-red-600"
                                        ></p>

                                        @break;

                                    @case('checkbox')
                                        <div class="checkbox-control">
                                            @php
                                                $options = $parentAttribute->lookup_type
                                                    ? app('Webkul\Attribute\Repositories\AttributeRepository')->getLookUpOptions($parentAttribute->lookup_type)
                                                    : $parentAttribute->options()->orderBy('sort_order')->get();
                                            @endphp

                                            @foreach ($options as $option)
                                                <span class="checkbox">
                                                    <input
                                                        type="checkbox"
                                                        name="{{ $fieldName }}[]"
                                                        value="{{ $option->id }}"
                                                    />
                                        
                                                    <label class="checkbox-view" style="display: inline;"></label>
                                                    {{ $option->name }}
                                                </span>
                                            @endforeach
                                        </div>

                                        <p 
                                            id="{{ $fieldName }}[]-error"
                                            class="error-message mt-1 text-xs italic text-red-600"
                                        ></p>
                                        
                                        @break;

                                    @case('file')
                                    @case('image')
                                        <input
                                            type="file"
                                            name="{{ $fieldName }}"
                                            id="{{ $fieldName }}"
                                            class="w-full rounded border border-gray-200 px-2.5 py-2 text-sm font-normal text-gray-800 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400"
                                        />

                                        <p 
                                            id="{{ $fieldName }}-error"
                                            class="error-message mt-1 text-xs italic text-red-600"
                                        ></p>

                                        @break;

                                    @case('boolean')
                                        <select
                                            class="w-full rounded border border-gray-200 px-2.5 py-2 text-sm font-normal text-gray-800 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400"
                                            id="{{ $fieldName }}"
                                            name="{{ $fieldName }}"
                                        >
                                            <option value="1">Yes</option>
                                            <option value="0">No</option>
                                        </select>

                                        <p 
                                            id="{{ $fieldName }}-error"
                                            class="error-message mt-1 text-xs italic text-red-600"
                                        ></p>

                                        @break;

                                    @case('address')

                                        @break;
                                @endswitch
                            </div>
                        @endforeach
                    </div>

                    <div class="flex items-center justify-center p-4">
                        <!-- Submit Button -->
                        <button
                            type="submit"
                            style="background-color: {{ $webForm->form_submit_button_color }} !important"
                            class="flex w-full cursor-pointer place-content-center items-center justify-center gap-x-1 rounded-md border border-brandColor bg-brandColor px-3 py-1.5 font-semibold text-gray-50 transition-all hover:opacity-[0.9] focus:opacity-[0.9]"
                        >
                            {{ $webForm->submit_button_label }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-web_form::layouts>