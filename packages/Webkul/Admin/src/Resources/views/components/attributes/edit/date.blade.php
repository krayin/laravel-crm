<x-admin::form.control-group.control
    type="date"
    :id="$attribute->code"
    :name="$attribute->code"
    :value="old($attribute->code) ?? $value"
    :rules="$validations.'|regex:^\d{4}-\d{2}-\d{2}$'"
    :label="$attribute->name"
/>
