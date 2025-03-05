@if (isset($attribute))
    <v-price-component
        :attribute="{{ json_encode($attribute) }}"
        :validations="'{{ $validations }}'"
        :value="{{ json_encode(old($attribute->code) ?? $value) }}"
    >
    </v-price-component>
@endif

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-price-component-template"
    >
        <x-admin::form.control-group.control
            type="text"
            ::id="attribute.code"
            ::value="value"
            ::name="attribute.code"
            ::rules="getValidation"
            ::label="attribute.name"
        />
    </script>

    <script type="module">
        app.component('v-price-component', {
            template: '#v-price-component-template',

            props: ['validations', 'attribute', 'value'],

            computed: {
                getValidation() {
                    return {
                        decimal: true,
                        min_value: 0,
                        ...((this.validations === 'required' || this.validations === 'required|decimal') ? { required: true } : {}),
                    };
                },
            },
        });
    </script>
@endPushOnce