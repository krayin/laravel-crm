@foreach ($customAttributes as $attribute)

    @php
        $validations = [];

        if ($attribute->is_required) {
            array_push($validations, 'required');
        }

        if ($attribute->type == 'price') {
            array_push($validations, 'decimal');
        }

        array_push($validations, $attribute->validation);

        $validations = implode('|', array_filter($validations));
    @endphp

    @if (view()->exists($typeView = 'admin::common.field-types.' . $attribute->type))

        <div class="form-group {{ $attribute->type }}"
            @if ($attribute->type == 'multiselect') :class="[errors.has('{{ $attribute->code }}[]') ? 'has-error' : '']"
            @else :class="[errors.has('{{ $attribute->code }}') ? 'has-error' : '']" @endif>

            <label for="{{ $attribute->code }}" {{ $attribute->is_required ? 'class=required' : '' }}>
                {{ $attribute->name }}

                @if ($attribute->type == 'price')
                    <span class="currency-code">($)</span>
                @endif

            </label>

            @include ($typeView, ['value' => isset($entity) ? $entity[$attribute->code] : null])

            <span class="control-error"
                @if ($attribute->type == 'multiselect') v-if="errors.has('{{ $attribute->code }}[]')"
                @else  v-if="errors.has('{{ $attribute->code }}')"  @endif>
                
                @if ($attribute->type == 'multiselect')
                    @{{ errors.first('{!! $attribute->code !!}[]') }}
                @else
                    @{{ errors.first('{!! $attribute->code !!}') }}
                @endif
            </span>
        </div>

    @endif

@endforeach

@push('scripts')

    <script type="text/x-template" id="email-component-template">
        <div class="emails-control">
            <div class="form-group input-group" v-for="(email, index) in emails" :class="[errors.has(attribute['code'] + '[' + index + '][value]') ? 'has-error' : '']">
                <input type="text" v-validate="validations" class="control" :name="attribute['code'] + '[' + index + '][value]'" v-model="email['value']" :data-vv-as="attribute['name']">

                <div class="input-group-append">
                    <select :name="attribute['code'] + '[' + index + '][label]'" v-model="email['label']" class="control">
                        <option value="work">{{ __('admin::app.common.work') }}</option>
                        <option value="home">{{ __('admin::app.common.home') }}</option>
                    </select>
                </div>

                <i class="icon trash-icon" @click="removeEmail(email)"></i>

                <span class="control-error" v-if="errors.has(attribute['code'] + '[' + index + '][value]')">
                    @{{ errors.first(attribute['code'] + '[' + index + '][value]') }}
                </span>
            </div>

            <a href @click.prevent="addEmail">+ add more</a>
        </div>
    </script>

    <script type="text/x-template" id="phone-component-template">
        <div class="phone-control">
            <div class="form-group input-group" v-for="(contactNumber, index) in contactNumbers" :class="[errors.has(attribute['code'] + '[' + index + '][value]') ? 'has-error' : '']">
                <input type="text" v-validate="validations" class="control" :name="attribute['code'] + '[' + index + '][value]'" v-model="contactNumber['value']" :data-vv-as="attribute['name']">

                <div class="input-group-append">
                    <select :name="attribute['code'] + '[' + index + '][label]'" v-model="contactNumber['label']" class="control">
                        <option value="work">{{ __('admin::app.common.work') }}</option>
                        <option value="home">{{ __('admin::app.common.home') }}</option>
                    </select>
                </div>

                <i class="icon trash-icon" @click="removePhone(contactNumber)"></i>

                <span class="control-error" v-if="errors.has(attribute['code'] + '[' + index + '][value]')">
                    @{{ errors.first(attribute['code'] + '[' + index + '][value]') }}
                </span>
            </div>

            <a href @click.prevent="addPhone">+ add more</a>
        </div>
    </script>

    <script type="text/x-template" id="lookup-component-template">
        <div class="lookup-control">
            <div class="form-group" style="margin-bottom: 0">
                <input type="text" v-validate="validations" :name="attribute['code']" :id="attribute['code']" class="control" v-model="search_term" v-on:keyup="search" :for="attribute['code']" :data-vv-as="attribute['name']">

                <input type="hidden" v-validate="validations" :name="attribute['code']" :data-vv-as="attribute['name']" v-model="entity_id"/>

                <div class="lookup-results" v-if="state == ''">
                    <ul>
                        <li v-for='(result, index) in results' @click="addLookUp(result)">
                            <span>@{{ result.label }}</span>
                        </li>

                        <li v-if='! results.length && search_term.length && ! is_searching'>
                            <span>{{ __('admin::app.common.no-result-found') }}</span>
                        </li>
                    </ul>
                </div>

                <i class="icon loader-active-icon" v-if="is_searching"></i>
            </div>
        </div>
    </script>

    <script>
        Vue.component('email-component', {

            template: '#email-component-template',

            props: ['validations', 'attribute', 'data'],

            inject: ['$validator'],

            data: function () {
                return {
                    emails: this.data ? this.data : [],
                }
            },

            created: function() {
                if (! this.emails || ! this.emails.length) {
                    this.emails = [{
                        'value': '',
                        'label': 'work'
                    }];
                }
            },

            methods: {
                addEmail: function() {
                    this.emails.push({
                        'value': '',
                        'label': 'work'
                    })
                },

                removeEmail: function(email) {
                    const index = this.emails.indexOf(email);

                    Vue.delete(this.emails, index);
                }
            }
        });

        Vue.component('phone-component', {

            template: '#phone-component-template',

            props: ['validations', 'attribute', 'data'],

            inject: ['$validator'],

            data: function () {
                return {
                    contactNumbers: this.data ? this.data : [],
                }
            },

            created: function() {
                if (! this.contactNumbers || ! this.contactNumbers.length) {
                    this.contactNumbers = [{
                        'value': '',
                        'label': 'work'
                    }];
                }
            },

            methods: {
                addPhone: function() {
                    this.contactNumbers.push({
                        'value': '',
                        'label': 'work'
                    })
                },

                removePhone: function(contactNumber) {
                    const index = this.contactNumbers.indexOf(contactNumber);

                    Vue.delete(this.contactNumbers, index);
                }
            }
        });

        Vue.component('lookup-component', {

            template: '#lookup-component-template',

            props: ['validations', 'attribute', 'lookupData'],

            inject: ['$validator'],

            data: function () {
                return {
                    search_term: this.lookupData ? this.lookupData['label'] : '',

                    entity_id: this.lookupData ? this.lookupData['value'] : '',

                    is_searching: false,

                    state: this.lookupData ? 'old' : '',

                    results: [],
                }
            },

            methods: {
                search: debounce(function () {
                    this.state = '';

                    this.entity_id = null;

                    this.is_searching = true;

                    if (this.search_term.length < 2) {
                        this.results = [];

                        this.is_searching = false;

                        return;
                    }

                    var self = this;
                    
                    this.$http.get("{{ route('admin.settings.attributes.lookup') }}/" + this.attribute['id'], {params: {query: this.search_term}})
                        .then (function(response) {
                            self.results = response.data;

                            self.is_searching = false;
                        })
                        .catch (function (error) {
                            self.is_searching = false;
                        })
                }, 500),

                addLookUp: function(result) {
                    this.state = 'old';

                    this.entity_id = result['value'];

                    this.search_term = result['label'];

                    this.searched_result = [];
                },

                createNew: function() {
                    this.state = 'new';

                    this.entity_id = null;
                }
            }
        });
    </script>
@endpush