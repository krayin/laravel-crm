<div class="form-group" style="margin-top: 5px;">

    @php
        $options = $attribute->lookup_type
            ? app('Webkul\Attribute\Repositories\AttributeRepository')->getLookUpOptions($attribute->lookup_type)
            : $attribute->options()->orderBy('sort_order')->get();

        $selectedOption = old($attribute->code) ?: $value;
    @endphp

    <option value=""></option>

    @foreach ($options as $option)
        <span class="checkbox" style="margin-top: 5px;">
            <input
                type="checkbox"
                name="{{ $attribute->code }}[]"
                value="{{ $option->id }}"
                {{ in_array($option->id, explode(',', $selectedOption)) ? 'checked' : ''}}
            />

            <label class="checkbox-view"></label>
            {{ $option->name }}
        </span>
    @endforeach

</div>