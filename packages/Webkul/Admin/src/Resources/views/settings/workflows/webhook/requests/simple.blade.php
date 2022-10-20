@push('scripts')
    <script type="text/x-template" id="simple-request-component-template">
        <div>
            <label @click="method">{{ __('admin::app.settings.workflows.lead') }}</label>
            <div v-for="lead in leads">
                <input type="checkbox" :name="['actions[' + index + '][hook][simple][]']" :value="lead.id" />
                @{{ lead.name }}
            </div>

            <label>{{ __('admin::app.settings.workflows.person') }}</label>
            <div v-for="person in persons">
                <input type="checkbox" :name="['actions[' + index + '][hook][simple][]']" :value="person.id" />
                @{{ person.name }}
            </div>

            <label>{{ __('admin::app.settings.workflows.quote') }}</label>
            <div v-for="quote in quotes">
                <input type="checkbox" :name="['actions[' + index + '][hook][simple][]']" :value="quote.id" />
                @{{ quote.name }}
            </div>
        </div>
    </script>

    <script>
        Vue.component('simple-request-component', {

            template: '#simple-request-component-template',

            inject: ['$validator'],

            data: function () {
                return {
                    'leads': @json(app('\Webkul\Workflow\Helpers\Entity\Lead')->getAttributes('leads', [])),
                    'persons': @json(app('\Webkul\Workflow\Helpers\Entity\Person')->getAttributes('persons', [])),
                    'quotes': @json(app('\Webkul\Workflow\Helpers\Entity\Quote')->getAttributes('quotes', [])),
                    index: this.$parent.index
                }
            },

            methods: {
                method: function() {
                    console.log(this.leads);
                },
            }
        });
    </script>
@endpush