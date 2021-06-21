@push('scripts')
    <script type="text/x-template" id="product-list-template">
        <div class="lead-product-list">
            <product-item
                v-for='(product, index) in products'
                :product="product"
                :key="index"
                :index="index"
                @onRemoveProduct="removeProduct($event)">
            </product-item>

            <a class="add-more-link" href @click.prevent="addProduct">+ add more</a>
        </div>
    </script>

    <script type="text/x-template" id="product-item-template">
        <div class="lead-product">
            <div class="top-control-group">
                <div class="form-group" :class="[errors.has(inputName + '[product_id]') ? 'has-error' : '']">
                    <label for="email" class="required">{{ __('admin::app.leads.item') }}</label>

                    <input type="text" v-validate="'required'" :name="[inputName + '[product_id]']" v-model="product['name']" v-on:keyup="search" class="control" data-vv-as="&quot;{{ __('admin::app.leads.item') }}&quot;"/>

                    <input type="hidden" v-validate="'required'" :name="[inputName + '[product_id]']" data-vv-as="&quot;{{ __('admin::app.leads.item') }}&quot;" v-model="product.product_id"/>

                    <div class="lookup-results" v-if="state == ''">
                        <ul>
                            <li v-for='(product, index) in products' @click="addProduct(product)">
                                <span>@{{ product.name }}</span>
                            </li>

                            <li v-if="! products.length && product['name'].length && ! is_searching">
                                <span>{{ __('admin::app.common.no-result-found') }}</span>
                            </li>
                        </ul>
                    </div>

                    <i class="icon loader-active-icon" v-if="is_searching"></i>

                    <span class="control-error" v-if="errors.has(inputName + '[product_id]')">@{{ errors.first(inputName + '[product_id]') }}</span>
                </div>
            </div>

            <div class="bottom-control-group">
                <div class="form-group" :class="[errors.has(inputName + '[price]') ? 'has-error' : '']">
                    <label for="email" class="required">{{ __('admin::app.leads.price') }}</label>

                    <input type="text" v-validate="'required'" :name="[inputName + '[price]']" v-model="product.price" class="control" data-vv-as="&quot;{{ __('admin::app.leads.price') }}&quot;"/>

                    <span class="control-error" v-if="errors.has(inputName + '[price]')">@{{ errors.first(inputName + '[price]') }}</span>
                </div>

                <div class="form-group" :class="[errors.has(inputName + '[quantity]') ? 'has-error' : '']">
                    <label for="email" class="required">{{ __('admin::app.leads.quantity') }}</label>

                    <input type="text" v-validate="'required'" :name="[inputName + '[quantity]']" v-model="product.quantity" class="control" data-vv-as="&quot;{{ __('admin::app.leads.quantity') }}&quot;"/>

                    <span class="control-error" v-if="errors.has(inputName + '[quantity]')">@{{ errors.first(inputName + '[quantity]') }}</span>
                </div>

                <div class="form-group" :class="[errors.has(inputName + '[amount]') ? 'has-error' : '']">
                    <label for="email" class="required">{{ __('admin::app.leads.amount') }}</label>

                    <input type="text" v-validate="'required'" :name="[inputName + '[amount]']" v-model="product.price * product.quantity" class="control" data-vv-as="&quot;{{ __('admin::app.leads.amount') }}&quot;" disabled/>

                    <span class="control-error" v-if="errors.has(inputName + '[amount]')">@{{ errors.first(inputName + '[amount]') }}</span>
                </div>

                <i class="icon trash-icon" @click="removeProduct"></i>
            </div>
        </div>
    </script>

    <script>
        Vue.component('product-list', {

            template: '#product-list-template',

            props: ['data'],

            inject: ['$validator'],

            data: function () {
                return {
                    products: this.data ? this.data : [],
                }
            },

            methods: {
                addProduct: function() {
                    this.products.push({
                        'id': null,
                        'product_id': null,
                        'name': '',
                        'quantity': null,
                        'price': null,
                        'amount': null,
                    })
                }, 

                removeProduct: function(product) {
                    const index = this.products.indexOf(product);

                    Vue.delete(this.products, index);
                }
            }
        });

        Vue.component('product-item', {

            template: '#product-item-template',

            props: ['index', 'product'],

            inject: ['$validator'],

            data: function () {
                return {
                    is_searching: false,

                    state: this.product['product_id'] ? 'old' : '',

                    products: [],
                }
            },

            computed: {
                inputName: function () {
                    if (this.product.id) {
                        return "products[" + this.product.id + "]";
                    }

                    return "products[product_" + this.index + "]";
                }
            },

            methods: {
                search: debounce(function () {
                    this.state = '';

                    this.product['product_id'] = null;

                    this.is_searching = true;

                    if (this.product['name'].length < 2) {
                        this.products = [];

                        this.is_searching = false;

                        return;
                    }

                    var self = this;
                    
                    this.$http.get("{{ route('admin.products.search') }}", {params: {query: this.product['name']}})
                        .then (function(response) {
                            self.products = response.data;

                            self.is_searching = false;
                        })
                        .catch (function (error) {
                            self.is_searching = false;
                        })
                }, 500),

                addProduct: function(result) {
                    this.state = 'old';

                    Vue.set(this.product, 'product_id', result.id)
                    Vue.set(this.product, 'name', result.name)
                    Vue.set(this.product, 'price', result.price)
                    Vue.set(this.product, 'quantity', result.quantity)
                },

                removeProduct: function () {
                    this.$emit('onRemoveProduct', this.product)
                }
            }
        });
    </script>
@endpush