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
    type="file"
    class="control"
    id="{{ $fieldName }}"
    name="{{ $fieldName }}"
    v-validate="'{{ $validations }}'"
    data-vv-as="&quot;{{ trans($field['title']) }}&quot;"
    value="{{ old($name) ?: core()->getConfigData($name) }}"
/>

@if ($result)
    <div class="form-group">
        <span class="checkbox">
            <input
                value="1"
                type="checkbox"
                id="{{ $fieldName }}[delete]"
                name="{{ $fieldName }}[delete]"
            />

            <label class="checkbox-view" for="delete"></label>
                {{ __('admin::app.configuration.delete') }}
        </span>
    </div>
@endif