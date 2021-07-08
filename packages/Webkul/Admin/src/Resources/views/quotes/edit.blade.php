@extends('admin::layouts.master')

@section('page_title')
    {{ __('admin::app.quotes.edit-title') }}
@stop

@section('content-wrapper')
    <div class="content full-page adjacent-center">
        <div class="page-header">

            {{ Breadcrumbs::render('quotes.edit', $quote) }}

            <div class="page-title">
                <h1>{{ __('admin::app.quotes.edit-title') }}</h1>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.quotes.update', $quote->id) }}" @submit.prevent="onSubmit">

            <div class="page-content">
                <div class="form-container">

                    <div class="panel">
                        <div class="panel-header">
                            <button type="submit" class="btn btn-md btn-primary">
                                {{ __('admin::app.quotes.save-btn-title') }}
                            </button>

                            <a href="{{ route('admin.quotes.index') }}">{{ __('admin::app.quotes.back') }}</a>
                        </div>
        
                        <div class="panel-body">
                            @csrf()

                            <input name="_method" type="hidden" value="PUT">

                            <input type="hidden" name="lead_id" value="{{ request('id') }}"/>

                            <accordian :title="'{{ __('admin::app.quotes.quote-information') }}'" :active="true">
                                <div slot="body">

                                    @include('admin::common.custom-attributes.edit', [
                                        'customAttributes' => app('Webkul\Attribute\Repositories\AttributeRepository')
                                            ->scopeQuery(function($query){
                                                return $query
                                                    ->where('entity_type', 'quotes')
                                                    ->whereIn('code', [
                                                        'user_id',
                                                        'subject',
                                                        'description',
                                                        'expired_at',
                                                        'person_id',
                                                    ]);
                                            })->get(),
                                        'entity'           => $quote,
                                    ])

                                </div>
                            </accordian>

                            <accordian :title="'{{ __('admin::app.quotes.address-information') }}'" :active="true">
                                <div slot="body">

                                    @include('admin::common.custom-attributes.edit', [
                                        'customAttributes' => app('Webkul\Attribute\Repositories\AttributeRepository')
                                        ->scopeQuery(function($query){
                                            return $query
                                                ->where('entity_type', 'quotes')
                                                ->whereIn('code', [
                                                    'billing_address',
                                                    'shipping_address',
                                                ]);
                                        })->get(),
                                        'entity'           => $quote,
                                    ])

                                </div>
                            </accordian>

                            <accordian :title="'{{ __('admin::app.quotes.quote-items') }}'" :active="true">
                                <div slot="body">

                                    <quote-item-list :data='@json($quote->items)'></quote-item-list>

                                </div>
                            </accordian>
                        </div>
                    </div>

                </div>

            </div>

        </form>

    </div>
@stop

@push('scripts')
    <script type="text/x-template" id="quote-item-list-template">
        <div class="quote-item-list">
            <div class="table">
                <table>

                    <thead>
                        <tr>
                            <th class="name">{{ __('admin::app.quotes.name') }}</th>
                            <th class="quantity">{{ __('admin::app.quotes.quantity') }}</th>
                            <th class="price">
                                {{ __('admin::app.quotes.price') }}
                                <span class="currency-code">({{ core()->currencySymbol(config('app.currency')) }})</span>
                            </th>
                            <th class="amount">
                                {{ __('admin::app.quotes.amount') }}
                                <span class="currency-code">({{ core()->currencySymbol(config('app.currency')) }})</span>
                            </th>
                            <th class="discount">
                                {{ __('admin::app.quotes.discount') }}
                                <span class="currency-code">({{ core()->currencySymbol(config('app.currency')) }})</span>
                            </th>
                            <th class="tax">
                                {{ __('admin::app.quotes.tax') }}
                                <span class="currency-code">({{ core()->currencySymbol(config('app.currency')) }})</span>
                            </th>
                            <th class="total">
                                {{ __('admin::app.quotes.total') }}
                                <span class="currency-code">({{ core()->currencySymbol(config('app.currency')) }})</span>
                            </th>
                            <th class="actions"></th>
                        </tr>
                    </thead>

                    <tbody>

                        <quote-item
                            v-for='(product, index) in products'
                            :product="product"
                            :key="index"
                            :index="index"
                            @onRemoveProduct="removeProduct($event)"
                        ></quote-item>

                    </tbody>

                </table>

                <a class="add-more-link" href @click.prevent="addProduct">+ {{ __('admin::app.common.add_more') }}</a>
            </div>
            
            <div class="quote-summary">
                <table>
                    <tr>
                        <td>
                            {{ __('admin::app.quotes.sub-total') }}
                            <span class="currency-code">({{ core()->currencySymbol(config('app.currency')) }})</span>
                        </td>
                        <td>-</td>
                        <td>
                            <div class="form-group">
                                <input type="text" name="sub_total" class="control" :value="subTotal" readonly>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            {{ __('admin::app.quotes.discount') }}
                            <span class="currency-code">({{ core()->currencySymbol(config('app.currency')) }})</span>
                        </td>
                        <td>-</td>
                        <td>
                            <div class="form-group">
                                <input type="text" name="discount_amount" class="control" v-model="discountAmount" readonly>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            {{ __('admin::app.quotes.tax') }}
                            <span class="currency-code">({{ core()->currencySymbol(config('app.currency')) }})</span>
                        </td>
                        <td>-</td>
                        <td>
                            <div class="form-group">
                                <input type="text" name="tax_amount" class="control" :value="taxAmount" readonly>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            {{ __('admin::app.quotes.adjustment') }}
                            <span class="currency-code">({{ core()->currencySymbol(config('app.currency')) }})</span>
                        </td>
                        <td>-</td>
                        <td>
                            <div class="form-group">
                                <input type="text" name="adjustment_amount" class="control" v-model="adjustmentAmount">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            {{ __('admin::app.quotes.grand-total') }}
                            <span class="currency-code">({{ core()->currencySymbol(config('app.currency')) }})</span>
                        </td>
                        <td>-</td>
                        <td>
                            <div class="form-group">
                                <input type="text" name="grand_total" class="control" :value="grandTotal" readonly>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </script>

    <script type="text/x-template" id="quote-item-template">
        <tr>
            <td>
                <div class="form-group" :class="[errors.has(inputName + '[product_id]') ? 'has-error' : '']">
                    <input type="text" v-validate="'required'" :name="[inputName + '[product_id]']" v-model="product['name']" v-on:keyup="search" class="control" data-vv-as="&quot;{{ __('admin::app.quotes.name') }}&quot;" autocomplete="off"/>

                    <input type="hidden" v-validate="'required'" :name="[inputName + '[product_id]']" data-vv-as="&quot;{{ __('admin::app.quotes.name') }}&quot;" v-model="product.product_id"/>

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
            </td>

            <td>
                <div class="form-group" :class="[errors.has(inputName + '[quantity]') ? 'has-error' : '']">
                    <input type="text" v-validate="'required'" :name="[inputName + '[quantity]']" v-model="product.quantity" class="control" data-vv-as="&quot;{{ __('admin::app.quotes.quantity') }}&quot;"/>

                    <span class="control-error" v-if="errors.has(inputName + '[quantity]')">@{{ errors.first(inputName + '[quantity]') }}</span>
                </div>
            </td>

            <td>
                <div class="form-group" :class="[errors.has(inputName + '[price]') ? 'has-error' : '']">
                    <input type="text" v-validate="'required'" :name="[inputName + '[price]']" v-model="product.price" class="control" data-vv-as="&quot;{{ __('admin::app.quotes.price') }}&quot;"/>

                    <span class="control-error" v-if="errors.has(inputName + '[price]')">@{{ errors.first(inputName + '[price]') }}</span>
                </div>
            </td>

            <td>
                <div class="form-group" :class="[errors.has(inputName + '[price]') ? 'has-error' : '']">
                    <input type="text" :name="[inputName + '[total]']" v-model="product.price * product.quantity" class="control" readonly/>
                </div>
            </td>

            <td>
                <div class="form-group" :class="[errors.has(inputName + '[discount_amount]') ? 'has-error' : '']">
                    <input type="text" v-validate="'required'" :name="[inputName + '[discount_amount]']" v-model="product.discount_amount" class="control" data-vv-as="&quot;{{ __('admin::app.quotes.discount') }}&quot;"/>

                    <span class="control-error" v-if="errors.has(inputName + '[discount_amount]')">@{{ errors.first(inputName + '[discount_amount]') }}</span>
                </div>
            </td>

            <td>
                <div class="form-group" :class="[errors.has(inputName + '[tax_amount]') ? 'has-error' : '']">
                    <input type="text" v-validate="'required'" :name="[inputName + '[tax_amount]']" v-model="product.tax_amount" class="control" data-vv-as="&quot;{{ __('admin::app.quotes.tax') }}&quot;"/>

                    <span class="control-error" v-if="errors.has(inputName + '[tax_amount]')">@{{ errors.first(inputName + '[tax_amount]') }}</span>
                </div>
            </td>

            <td>
                <div class="form-group" :class="[errors.has(inputName + '[price]') ? 'has-error' : '']">
                    <input type="text" :value="parseInt(product.price * product.quantity) + parseInt(product.tax_amount) - parseInt(product.discount_amount)" class="control" readonly/>
                </div>
            </td>

            <td class="actions">
                <i class="icon trash-icon" @click="removeProduct"></i>
            </td>
        </tr>
    </script>

    <script>
        Vue.component('quote-item-list', {

            template: '#quote-item-list-template',

            props: ['data'],

            inject: ['$validator'],

            data: function () {
                return {
                    adjustmentAmount: 0,

                    products: this.data ? this.data : [{
                        'id': null,
                        'product_id': null,
                        'name': '',
                        'quantity': 0,
                        'price': 0,
                        'discount_amount': 0,
                        'tax_amount': 0,
                    }],
                }
            },

            computed: {
                subTotal:  function() {
                    var total = 0;

                    this.products.forEach(product => {
                        total += parseInt(product.price * product.quantity);
                    });

                    return total;
                },

                discountAmount: function() {
                    var total = 0;

                    this.products.forEach(product => {
                        total += parseInt(product.discount_amount);
                    });

                    return total;
                },

                taxAmount: function() {
                    var total = 0;

                    this.products.forEach(product => {
                        total += parseInt(product.tax_amount);
                    });

                    return total;
                },

                grandTotal: function() {
                    var total = 0;

                    this.products.forEach(product => {
                        total += parseInt(product.price * product.quantity) + parseInt(product.tax_amount) - parseInt(product.discount_amount) + parseInt(this.adjustmentAmount);
                    });

                    return total;
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
                        'discount_amount': null,
                        'tax_amount': null,
                    })
                }, 

                removeProduct: function(product) {
                    if (this.products.length == 1) {
                        this.products = [{
                            'id': null,
                            'product_id': null,
                            'name': '',
                            'quantity': null,
                            'price': null,
                            'discount_amount': null,
                            'tax_amount': null,
                        }];
                    } else {
                        const index = this.products.indexOf(product);

                        Vue.delete(this.products, index);
                    }
                }
            }
        });

        Vue.component('quote-item', {

            template: '#quote-item-template',

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
                        return "items[" + this.product.id + "]";
                    }

                    return "items[item_" + this.index + "]";
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

                    Vue.set(this.product, 'product_id', result.id);
                    Vue.set(this.product, 'name', result.name);
                    Vue.set(this.product, 'price', result.price);
                    Vue.set(this.product, 'quantity', result.quantity);
                    Vue.set(this.product, 'discount_amount', 0);
                    Vue.set(this.product, 'tax_amount', 0);
                },

                removeProduct: function () {
                    this.$emit('onRemoveProduct', this.product);
                }
            }
        });
    </script>
@endpush