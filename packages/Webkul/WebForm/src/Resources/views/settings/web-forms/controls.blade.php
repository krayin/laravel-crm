@foreach ($webForm->attributes as $attribute)
    @php
        $parentAttribute = $attribute->attribute;

        $fieldName = $parentAttribute->entity_type . '[' . $parentAttribute->code . ']';

        $validations = $parentAttribute->is_required ? 'required' : '';
    @endphp
    
    <x-web_form::form.control-group>
        <x-web_form::form.control-group.label
            :for="$fieldName"
            class="{{ $validations }}"
            style="color: {{ $webForm->attribute_label_color }} !important;"
        >
            {{ $parentAttribute->name }}
        </x-web_form::form.control-group.label>

        @switch($parentAttribute->type)
            @case('text')
                <x-web_form::form.control-group.control
                    type="text"
                    :name="$fieldName"
                    :id="$fieldName"
                    :rules="$validations"
                    :label="$parentAttribute->name"
                />

                <x-web_form::form.control-group.error :control-name="$fieldName" />

                @break

            @case('price')
                <x-web_form::form.control-group.control
                    type="text"
                    :name="$fieldName"
                    :id="$fieldName"
                    :rules="$validations.'|numeric'"
                    :label="$parentAttribute->name"
                />

                <x-web_form::form.control-group.error :control-name="$fieldName" />

                @break

            @case('email')
                <x-web_form::form.control-group.control
                    type="email"
                    name="{{ $fieldName }}[0][value]"
                    id="{{ $fieldName }}[0][value]"
                    rules="{{ $validations }}|email"
                    :label="$parentAttribute->name"
                />

                <x-web_form::form.control-group.control
                    type="hidden"
                    name="{{ $fieldName }}[0][label]"
                    id="{{ $fieldName }}[0][label]"
                    rules="required"
                    value="work"
                />

                <x-web_form::form.control-group.error control-name="{{ $fieldName }}[0][value]" />

                @break

            @case('checkbox')
                @php
                    $options = $parentAttribute->lookup_type
                        ? app('Webkul\Attribute\Repositories\AttributeRepository')->getLookUpOptions($parentAttribute->lookup_type)
                        : $parentAttribute->options()->orderBy('sort_order')->get();
                @endphp

                @foreach ($options as $option)
                    <x-web_form::form.control-group class="!mb-2 flex select-none items-center gap-2.5">
                        <x-web_form::form.control-group.control
                            type="checkbox"
                            name="{{ $fieldName }}[]"
                            id="{{ $fieldName }}[]"
                            value="{{ $option->id }}"
                            for="{{ $fieldName }}[]"
                        />

                        <label
                            class="cursor-pointer text-xs font-medium text-gray-600 dark:text-gray-300"
                            for="{{ $fieldName }}[]"
                        >
                            @lang('web_form::app.catalog.attributes.edit.is-required')
                        </label>
                    </x-web_form::form.control-group>
                @endforeach
            
            @case('file')
            @case('image')
                <x-web_form::form.control-group.control
                    type="file"
                    :name="$fieldName"
                    :id="$fieldName"
                    :rules="$validations"
                    :label="$parentAttribute->name"
                />

                <x-web_form::form.control-group.error control-name="{{ $fieldName }}" />
                
                @break;

            @case('phone')
                <x-web_form::form.control-group.control
                    type="text"
                    name="{{ $fieldName }}[0][value]"
                    id="{{ $fieldName }}[0][value]"
                    rules="{{ $validations }}|numeric"
                    :label="$parentAttribute->name"
                />

                <x-web_form::form.control-group.control
                    type="hidden"
                    name="{{ $fieldName }}[0][label]"
                    id="{{ $fieldName }}[0][label]"
                    rules="required"
                    value="work"
                />

                <x-web_form::form.control-group.error control-name="{{ $fieldName }}[0][value]" />

                @break

            @case('date')
                <x-web_form::form.control-group.control
                    type="date"
                    :name="$fieldName"
                    :id="$fieldName"
                    :rules="$validations"
                    :label="$parentAttribute->name"
                />

                <x-web_form::form.control-group.error :control-name="$fieldName" />

                @break

            @case('datetime')
                <x-web_form::form.control-group.control
                    type="datetime"
                    :name="$fieldName"
                    :id="$fieldName"
                    :rules="$validations"
                    :label="$parentAttribute->name"
                />

                <x-web_form::form.control-group.error :control-name="$fieldName" />

                @break

            @case('select')
            @case('lookup')
                @php
                    $options = $parentAttribute->lookup_type
                        ? app('Webkul\Attribute\Repositories\AttributeRepository')->getLookUpOptions($parentAttribute->lookup_type)
                        : $parentAttribute->options()->orderBy('sort_order')->get();
                @endphp

                <x-web_form::form.control-group.control
                    type="select"
                    :name="$fieldName"
                    :id="$fieldName"
                    :rules="$validations"
                    :label="$parentAttribute->name"
                >
                    @foreach ($options as $option)
                        <option value="{{ $option->id }}">{{ $option->name }}</option>
                    @endforeach
                </x-web_form::form.control-group.control>

                <x-web_form::form.control-group.error :control-name="$fieldName" />

                @break            

            @case('multiselect')
                @php
                    $options = $parentAttribute->lookup_type
                        ? app('Webkul\Attribute\Repositories\AttributeRepository')->getLookUpOptions($parentAttribute->lookup_type)
                        : $parentAttribute->options()->orderBy('sort_order')->get();
                @endphp

                <x-web_form::form.control-group.control
                    type="select"
                    id="{{ $fieldName }}"
                    name="{{ $fieldName }}[]"
                    :rules="$validations"
                    :label="$parentAttribute->name"
                >
                    @foreach ($options as $option)
                        <option value="{{ $option->id }}">{{ $option->name }}</option>
                    @endforeach
                </x-web_form::form.control-group.control>

                <x-web_form::form.control-group.error :control-name="$fieldName" />

                @break

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
                
                @break

            @case('boolean')
                <x-web_form::form.control-group.control
                    type="select"
                    :name="$fieldName"
                    :id="$fieldName"
                    :rules="$validations"
                    :label="$parentAttribute->name"
                >
                    <option value="1">Yes</option>
                    <option value="0">No</option>
                </x-web_form::form.control-group.control>

                <x-web_form::form.control-group.error :control-name="$fieldName" />

                @break   
        @endswitch
    </x-web_form::form.control-group>
@endforeach