@foreach ($webForm->attributes as $attribute)
    <x-web_form::form.control-group>
        @php
            $parentAttribute = $attribute->attribute;

            $fieldName = $parentAttribute->entity_type . '[' . $parentAttribute->code . ']';

            $validations = $parentAttribute->is_required ? 'required' : '';
        @endphp

        <div class="flex justify-between">
            <x-web_form::form.control-group.label
                :for="$fieldName"
                class="{{ $attribute->is_required ? 'required' : '' }}"
            >
                {{ $parentAttribute->name }}
            </x-web_form::form.control-group.label>
        </div>

        {{-- Text input --}}
        @if ($parentAttribute->type == 'text')
            <x-web_form::form.control-group.control
                type="text"
                :name="$fieldName"
                :id="$fieldName"
                :rules="$validations"
                :label="$parentAttribute->name"
            >
            </x-web_form::form.control-group.control>

        @elseif ($parentAttribute->type == 'email')
            <x-web_form::form.control-group.control
                type="text"
                :name="$fieldName"
                :id="$fieldName"
                :rules="$validations"
                :label="$parentAttribute->name"
            >
            </x-web_form::form.control-group.control>

        @elseif ($parentAttribute->type == 'phone')
            <x-web_form::form.control-group.control
                type="text"
                :name="$fieldName"
                :id="$fieldName"
                :rules="$validations"
                :label="$parentAttribute->name"
            >
            </x-web_form::form.control-group.control>

        {{-- Password input --}}
        @elseif ($parentAttribute->type == 'password')
            <x-web_form::form.control-group.control
                type="password"
                :name="$fieldName"
                :id="$fieldName"
                :rules="$validations"
                :label="$parentAttribute->name"
            >
            </x-web_form::form.control-group.control>

        {{-- Number input --}}
        @elseif ($parentAttribute->type == 'number')
            <x-web_form::form.control-group.control
                type="number"
                :name="$fieldName"
                :id="$fieldName"
                :rules="$validations"
                :label="$parentAttribute->name"
            >
            </x-web_form::form.control-group.control>

        {{-- Color Input --}}
        @elseif ($parentAttribute->type == 'color')
            <x-web_form::form.control-group.control
                type="color"
                :name="$fieldName"
                :id="$fieldName"
                :rules="$validations"
                :label="$parentAttribute->name"
            >
            </x-web_form::form.control-group.control>

        {{-- Textarea Input --}}
        @elseif ($parentAttribute->type == 'textarea')
            <x-web_form::form.control-group.control
                type="textarea"
                :name="$fieldName"
                :id="$fieldName"
                :rules="$validations"
                :label="$parentAttribute->name"
            >
            </x-web_form::form.control-group.control>

        {{-- Textarea Input --}}
        @elseif ($parentAttribute->type == 'editor')
            <x-web_form::form.control-group.control
                type="textarea"
                :name="$fieldName"
                :id="$fieldName"
                :rules="$validations"
                :label="$parentAttribute->name"
            >
            </x-web_form::form.control-group.control>

        {{-- Boolean/Switch input --}}
        @elseif ($parentAttribute->type == 'boolean')
            <!-- Hidden Fild for unseleted Switch button -->
            <x-web_form::form.control-group.control
                type="hidden"
                :name="$fieldName"

                value="0"
            >
            </x-web_form::form.control-group.control>

            <x-web_form::form.control-group.control
                type="switch"
                :name="$fieldName"
                :value="0"
                :id="$fieldName"
                :rules="$validations"
                :label="$parentAttribute->name"
                :checked="0"
            >
            </x-web_form::form.control-group.control>

        @elseif ($parentAttribute->type == 'image')
            <div class="flex items-center justify-center">
                <x-web_form::form.control-group.control
                    type="file"
                    :name="$fieldName"
                    :id="$fieldName"
                    :rules="$validations"
                    :label="$parentAttribute->name"
                >
                </x-web_form::form.control-group.control>
            </div>
        @elseif ($parentAttribute->type == 'file')
            <x-web_form::form.control-group.control
                type="file"
                :name="$fieldName"
                :id="$fieldName"
                :rules="$validations"
                :label="$parentAttribute->name"
            >
            </x-web_form::form.control-group.control>
        @endif

        {{-- Input field validaitons error message --}}
        <x-web_form::form.control-group.error
            :control-name="$fieldName"
        >
        </x-web_form::form.control-group.error>
    </x-web_form::form.control-group>
@endforeach
