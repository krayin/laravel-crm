@php($selectedOption = core()->getConfigData($name) ?? '')

<input
    type="hidden"
    name="{{ $fieldName }}"
    value="0"
/>

<label class="switch">
    <input
        type="checkbox"
        name="{{ $fieldName }}"
        class="control"
        {{ $selectedOption ? 'checked' : '' }}
    />
    <span class="slider round"></span>
</label>