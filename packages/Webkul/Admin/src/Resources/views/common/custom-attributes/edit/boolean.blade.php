<?php $selectedOption = old($attribute->code) ?: $value ?>

<label class="switch">
    <input
        type="checkbox"
        name="{{ $attribute->code }}"
        class="control"
        id="{{ $attribute->code }}"
        value="1"
        data-vv-as="&quot;{{ $attribute->name }}&quot;"
        {{ $selectedOption ? 'checked' : ''}}
    >
    <span class="slider round"></span>
</label>