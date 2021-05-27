@php($selectedOption = core()->getConfigData($name) ?? '')

<input
    value="0"
    type="hidden"
    name="{{ $fieldName }}"
/>

<label class="switch">
    <input
        type="checkbox"
        class="control"
        name="{{ $fieldName }}"
        {{ $selectedOption ? 'checked' : '' }}
    />
    <span class="slider round"></span>
</label>