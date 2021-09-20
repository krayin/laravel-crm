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
    name="{{ $fieldName }}"
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
                name="{{ $fieldName }}[delete]"
                type="checkbox"
                id="{{ $fieldName }}[delete]"
                value="1"
            />

            <label class="checkbox-view" for="delete"></label>
                {{ __('admin::app.configuration.delete') }}
        </span>
    </div>
@endif