@if ($value)
    <a href="{{ route('admin.settings.attributes.download', ['path' => $value]) }}" target="_blank">
        <i class="icon download-icon"></i>
    </a>
@endif

<input
    type="file"
    name="{{ $attribute->code }}"
    class="control"
    id="{{ $attribute->code }}"
    value="{{ old($attribute->code) ?: $value }}"
    v-validate="'{{$validations}}'"
    data-vv-as="&quot;{{ $attribute->name }}&quot;"
    style="padding-top: 5px;"
/>

@if ($value)
    <div class="form-group" style="margin-top: 5px;">
        <span class="checkbox">
            <input type="checkbox" id="{{ $attribute->code }}[delete]"  name="{{ $attribute->code }}[delete]" value="1">

            <label class="checkbox-view" for="delete"></label>
                {{ __('admin::app.common.delete') }}
        </span>
    </div>
@endif