@if ($value)
    {{-- <a href="{{ route('admin.catalog.products.file.download', [$entity->product_id, $attribute->id] )}}" target="_blank"> --}}
        <i class="icon sort-down-icon download"></i>
    </a>
@endif

<input type="file" v-validate="'{{$validations}}'" class="control" id="{{ $attribute->code }}" name="{{ $attribute->code }}" value="{{ old($attribute->code) ?: $value }}" data-vv-as="&quot;{{ $attribute->name }}&quot;" style="padding-top: 5px;"/>

@if ($value)
    <div class="control-group" style="margin-top: 5px;">
        <span class="checkbox">
            <input type="checkbox" id="{{ $attribute->code }}[delete]"  name="{{ $attribute->code }}[delete]" value="1">

            <label class="checkbox-view" for="delete"></label>
                {{ __('admin::app.configuration.delete') }}
        </span>
    </div>
@endif