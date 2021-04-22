<select v-validate="'{{$validations}}'" class="control" id="{{ $attribute->code }}" name="{{ $attribute->code }}" data-vv-as="&quot;{{ $attribute->name }}&quot;">

    <?php $selectedOption = old($attribute->code) ?: $value ?>

    @foreach ($attribute->options()->orderBy('sort_order')->get() as $option)
        <option value="{{ $option->id }}" {{ $option->id == $selectedOption ? 'selected' : ''}}>
            {{ $option->name }}
        </option>
    @endforeach

</select>