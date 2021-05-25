@php
    $result = core()->getConfigData($name);
    $src = explode("/", $result);
    $path = end($src);
@endphp

@if ($result)
    <a href="{{ route('admin.configuration.download', [request()->route('slug'), request()->route('slug2'), $path]) }}">
        <i class="icon sort-down-icon download"></i>
    </a>
@endif

<input
    type="file"
    class="control"
    v-validate="'{{ $validations }}'"
    data-vv-as="&quot;{{ trans($field['title']) }}&quot;"
    value="{{ old($name) ?: core()->getConfigData($name) }}"
    id="{{ $fieldName }}"
    name="{{ $fieldName }}"
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