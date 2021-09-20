@php
    $src = Storage::url(core()->getConfigData($name));
    $result = core()->getConfigData($name);
@endphp

@if ($result)
    <a href="{{ $src }}" target="_blank">
        <img src="{{ $src }}" class="configuration-image" />
    </a>
@endif

<input
    name="{{ $fieldName }}"
    type="file"
    class="control"
    id="{{ $fieldName }}"
    value="{{ old($name) ?: core()->getConfigData($name) }}"
    v-validate="'{{ $validations }}'"
    data-vv-as="&quot;{{ trans($field['title']) }}&quot;"
/>

@if ($result)
    <div class="form-group">
        <span class="checkbox">
            <input
                type="checkbox"
                name="{{ $fieldName }}[delete]"
                id="{{ $fieldName }}[delete]"
                value="1"
            />

            <label class="checkbox-view" for="delete"></label>
                {{ __('admin::app.configuration.delete') }}
        </span>
    </div>
@endif