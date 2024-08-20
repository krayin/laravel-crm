@extends('admin::layouts.master')

@section('page_title')
    {{ $product->name }}
@stop

@section('content-wrapper')
    <div class="content full-page">
        {!! view_render_event('admin.products.view.header.before', ['product' => $product]) !!}

        <div class="page-header">
            {{ Breadcrumbs::render('products.view', $product) }}

            <div class="page-title">
                <h1>
                    {{ $product->name }}
                </h1>
            </div>

            <div class="page-action">
            </div>
        </div>

        {!! view_render_event('admin.products.view.content.after', ['product' => $product]) !!}

        <div class="page-content product-view">
            {!! view_render_event('admin.products.view.informations.before', ['product' => $product]) !!}

            <div class="panel">
                <div class="panel-header">
                    {{ __('admin::app.products.information') }}
                </div>

                <div class="panel-body">
                    <div class="custom-attribute-view">
                        @include('admin::common.custom-attributes.view', [
                            'customAttributes' => app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                                'entity_type' => 'products',
                            ])->sortBy('sort_order'),
                            'entity'           => $product,
                        ])
                    </div>
                </div>
            </div>

            {!! view_render_event('admin.products.view.informations.after', ['product' => $product]) !!}


            {!! view_render_event('admin.products.view.inventories.after', ['product' => $product]) !!}
            
            <div class="panel product-inventories">
                <div class="panel-header">
                    {{ __('admin::app.products.inventories') }}
                </div>

                <div class="panel-body" style="padding: 0">
                    <!-- Product Inventories Vue Component -->
                    <v-product-inventories></v-product-inventories>
                </div>
            </div>

            {!! view_render_event('admin.products.view.inventories.after', ['product' => $product]) !!}

            {!! view_render_event('admin.products.view.content.after', ['product' => $product]) !!}
        </div>
    </div>
@stop

@push('scripts')
    <script type="text/x-template" id="v-product-inventories-template">
        <div class="table">
            <table>
                <thead>
                    <tr>
                        <th>{{ __('admin::app.products.source') }}</th>
                        <th>{{ __('admin::app.products.in-stock') }}</th>
                        <th>{{ __('admin::app.products.allocated') }}</th>
                        <th>{{ __('admin::app.products.on-hand') }}</th>
                        <th>{{ __('admin::app.products.actions') }}</th>
                    </tr>
                </thead>

                <tbody>
                    <template v-for="warehouse in productWarehouses">
                        <tr style="font-weight: 500">
                            <td>@{{ warehouse.name }}</td>
                            <td>@{{ warehouse.in_stock }}</td>
                            <td>@{{ warehouse.allocated }}</td>
                            <td>@{{ warehouse.on_hand }}</td>
                            <td>
                                <a href="#" @click.prevent="selectWarehouse(warehouse)">
                                    {{ __('admin::app.products.assign') }}
                                </a>
                            </td>
                        </tr>

                        <template v-for="location in warehouse.locations">
                            <tr>
                                <td style="padding-left: 30px">@{{ location.name }}</td>
                                <td>@{{ location.in_stock }}</td>
                                <td>@{{ location.allocated }}</td>
                                <td>@{{ location.on_hand }}</td>
                                <td></td>
                            </tr>
                        </template>
                    </template>
                </tbody>

                <tfoot v-if="notAddedWarehouses.length">
                    <tr>
                        <td colspan="5">
                            <span class="dropdown-toggle">
                                {{ __('admin::app.products.add-source') }}
                            </span>

                            <div class="dropdown-list">
                                <div class="dropdown-container">
                                    <ul>
                                        <li
                                            v-for="warehouse in notAddedWarehouses"
                                            @click="addWarehouse(warehouse)"
                                        >
                                            @{{ warehouse.name }}
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tfoot>
            </table>

            <!-- Drawer Vue Js Component -->
            <form
                action="{{ route('admin.products.inventories.store', $product->id) }}"
                method="post"
                @submit.prevent="onSubmit"
                enctype="multipart/form-data"
            >
                <drawer ref="assignLocationDrawer">
                    <template v-if="selectedWarehouse">
                        <template slot="header">
                            @{{ selectedWarehouse.name }}

                            <div class="actions">
                                <button class="btn btn-sm btn-primary" type="submit">
                                    {{ __('admin::app.products.save') }}
                                </button>

                                <i class="icon close-icon" @click="$refs.assignLocationDrawer.close()"></i>
                            </div>
                        </template>

                        <template slot="content">
                            <v-warehouse-location-inventories
                                :warehouse="selectedWarehouse"
                            ></v-warehouse-location-inventories>
                        </template>
                    </template>
                </drawer>
            </form>
        </div>
    </script>

    <script type="text/x-template" id="v-warehouse-location-inventories-template">
        <div class="inventory-item-list">
            <div class="table">
                <table style="box-shadow: none">
                    <thead>
                        <tr>
                            <th class="name">
                                <div class="form-group">
                                    <label class="required">
                                        {{ __('admin::app.products.location') }}
                                    </label>
                                </div>
                            </th>
                            
                            <th class="in_stock">
                                <div class="form-group">
                                    <label class="required">
                                        {{ __('admin::app.products.in-stock') }}
                                    </label>
                                </div>
                            </th>

                            <th class="allocated">
                                <div class="form-group">
                                    <label class="required">
                                        {{ __('admin::app.products.allocated') }}                                    
                                    </label>
                                </div>
                            </th>

                            <th style="width: 30px" class="actions"></th>
                        </tr>
                    </thead>

                    <tbody>
                        <v-warehouse-location-inventory-item
                            v-for='(location, index) in warehouseLocations'
                            :location="location"
                            :warehouse="warehouse"
                            :key="index"
                            :index="index"
                            @onRemove="removeLocation($event)"
                        ></v-warehouse-location-inventory-item>
                    </tbody>
                </table>

                <a class="add-more-link" href @click.prevent="addLocation">+ {{ __('admin::app.common.add_more') }}</a>
            </div>
        </div>
    </script>

    <script type="text/x-template" id="v-warehouse-location-inventory-item-template">
        <tr>
            <td>
                <div class="form-group" :class="[errors.has('inventories[inventory_' + index + '][warehouse_location_id]') ? 'has-error' : '']">
                    <input
                        type="text"
                        :name="'inventories[inventory_' + index + '][warehouse_location_id]'"
                        class="control"
                        v-model="location['name']"
                        autocomplete="off"
                        v-validate="'required'"
                        data-vv-as="&quot;{{ __('admin::app.quotes.name') }}&quot;"
                        v-on:keyup="search"
                        placeholder="{{ __('admin::app.products.search') }}"
                        
                    />

                    <input
                        type="hidden"
                        :name="'inventories[inventory_' + index + '][warehouse_location_id]'"
                        v-model="location.id"
                        v-validate="'required'"
                        data-vv-as="&quot;{{ __('admin::app.products.name') }}&quot;"
                    />

                    <input
                        type="hidden"
                        :name="'inventories[inventory_' + index + '][warehouse_id]'"
                        v-model="warehouse.id"
                    />

                    <div class="lookup-results" v-if="state == ''">
                        <ul>
                            <li v-for='location in searchedLocations' @click="add(location)">
                                <span>@{{ location.name }}</span>
                            </li>
                        </ul>

                        <template v-if="! searchedLocations.length && location['name'].length && ! isSearching">
                            <ul>
                                <li>
                                    <span>{{ __('admin::app.common.no-result-found') }}</span>
                                </li>
                            </ul>
                        </template>
                    </div>

                    <i class="icon loader-active-icon" v-if="isSearching"></i>

                    <span class="control-error" v-if="errors.has('inventories[inventory_' + index + '][warehouse_location_id]')">
                        @{{ errors.first('inventories[inventory_' + index + '][warehouse_location_id]') }}
                    </span>
                </div>
            </td>

            <td>
                <div class="form-group" :class="[errors.has('inventories[inventory_' + index + '][in_stock]') ? 'has-error' : '']">
                    <input
                        type="text"
                        :name="'inventories[inventory_' + index + '][in_stock]'"
                        class="control"
                        v-model="location.in_stock"
                        v-validate="'required|numeric|min_value:0'"
                        data-vv-as="&quot;{{ __('admin::app.products.in-stock') }}&quot;"
                    />

                    <span class="control-error" v-if="errors.has('inventories[inventory_' + index + '][in_stock]')">
                        @{{ errors.first('inventories[inventory_' + index + '][in_stock]') }}
                    </span>
                </div>
            </td>

            <td>
                <div class="form-group" :class="[errors.has('inventories[inventory_' + index + '][allocated]') ? 'has-error' : '']">
                    <input
                        type="text"
                        :name="'inventories[inventory_' + index + '][allocated]'"
                        class="control"
                        v-model="location.allocated"
                        v-validate="'required|numeric|min_value:0'"
                        data-vv-as="&quot;{{ __('admin::app.products.allocated') }}&quot;"
                    />

                    <span class="control-error" v-if="errors.has('inventories[inventory_' + index + '][allocated]')">
                        @{{ errors.first('inventories[inventory_' + index + '][allocated]') }}
                    </span>
                </div>
            </td>

            <td class="actions">
                <i class="icon trash-icon" style="margin-top: 20px;" @click="remove"></i>
            </td>
        </tr>
    </script>

    <script>
        Vue.component('v-product-inventories', {
            template: '#v-product-inventories-template',

            inject: ['$validator'],

            data: function () {
                return {
                    warehouses: [],

                    productWarehouses: [],

                    selectedWarehouse: null,
                }
            },

            computed: {
                notAddedWarehouses: function() {
                    return this.warehouses.filter(warehouse => {
                        return ! this.productWarehouses.find(productWarehouse => productWarehouse.id == warehouse.id);
                    });
                }
            },

            mounted() {
                this.getAllWarehouses();

                this.getProductWarehouses();
            },

            methods: {
                getAllWarehouses: function() {
                    this.$http.get("{{ route('admin.settings.warehouses.search') }}")
                        .then(response => {
                            this.warehouses = response.data;
                        })
                        .catch(error => {
                            console.log(error);
                        });
                },

                getProductWarehouses: function() {
                    this.$http.get("{{ route('admin.products.warehouses', $product->id) }}")
                        .then(response => {
                            this.productWarehouses = response.data;
                        })
                        .catch(error => {
                            console.log(error);
                        });
                },

                addWarehouse: function(warehouse) {
                    warehouse = {
                        id: warehouse.id,
                        name: warehouse.name,
                        in_stock: 0,
                        allocated: 0,
                        on_hand: 0,
                        locations: []
                    };

                    this.productWarehouses.push(warehouse);

                    this.selectWarehouse(warehouse);

                    this.$refs.assignLocationDrawer.open();
                },

                selectWarehouse: function(warehouse) {
                    this.selectedWarehouse = null;

                    setTimeout(() => {
                        this.selectedWarehouse = warehouse;
                        
                        this.$refs.assignLocationDrawer.open();
                    }, 0);
                },

                onSubmit: function(e) {
                    var self = this;

                    let params = new FormData(e.target);

                    this.$validator.validateAll().then((result) => {
                        if (result) {
                            self.$http.post("{{ route('admin.products.inventories.store', ['id' => $product->id, 'warehouseId' => 'warehouseId']) }}".replace('warehouseId', this.selectedWarehouse.id), params).then(response => {
                                this.getAllWarehouses();

                                this.getProductWarehouses();

                                this.$refs.assignLocationDrawer.close();

                                window.flashMessages = [{'type': 'success', 'message': response.data.message}];

                                self.$root.addFlashMessages();
                            })
                            .catch(error => {
                                window.serverErrors = error.response.data.errors;

                                self.$root.addServerErrors();
                            });
                        }
                    });
                },
            },
        });

        Vue.component('v-warehouse-location-inventories', {
            template: '#v-warehouse-location-inventories-template',

            props: ['warehouse'],

            inject: ['$validator'],

            data: function () {
                return {
                    warehouseLocations: [],
                }
            },
            
            created() {
                if (this.warehouse.locations.length) {
                    this.warehouseLocations = JSON.parse(JSON.stringify(this.warehouse.locations));
                }
            },

            methods: {
                addLocation: function() {
                    this.warehouseLocations.push({
                        'id': null,
                        'name': '',
                        'in_stock': 0,
                        'allocated': 0,
                        'on_hand': 0,
                    });
                },

                removeLocation: function(inventory) {
                    const index = this.warehouseLocations.indexOf(inventory);

                    Vue.delete(this.warehouseLocations, index);
                },
            },
        });

        Vue.component('v-warehouse-location-inventory-item', {
            template: '#v-warehouse-location-inventory-item-template',

            props: ['index', 'warehouse', 'location'],

            inject: ['$validator'],

            data: function () {
                return {
                    isSearching: false,

                    state: this.location['id'] ? 'old' : '',

                    searchedLocations: [],
                }
            },

            methods: {
                search: debounce(function () {
                    this.state = '';

                    this.location['id'] = null;

                    this.isSearching = true;

                    if (this.location['name'].length < 2) {
                        this.searchedLocations = [];

                        this.isSearching = false;

                        return;
                    }

                    var self = this;

                    this.$http.get("{{ route('admin.settings.locations.search') }}", {
                            params: {
                                search: 'warehouse_id:' + this.warehouse.id + ';name:' + this.location['name'],
                                searchFields: 'warehouse_id:=;name:like',
                                searchJoin: 'and'
                            }
                        })
                        .then (function(response) {
                            self.$parent.warehouseLocations.forEach(function(addedLocation) {
                                response.data = response.data.filter(function(location) {
                                    return location.id !== addedLocation.id;
                                });
                            });

                            self.searchedLocations = response.data;

                            self.isSearching = false;
                        })
                        .catch (function (error) {
                            self.isSearching = false;
                        })
                }, 500),

                add: function(result) {
                    this.state = 'old';

                    Vue.set(this.location, 'id', result.id);
                    Vue.set(this.location, 'warehouse_id', result.warehouse_id);
                    Vue.set(this.location, 'name', result.name);
                },

                remove: function () {
                    this.$emit('onRemove', this.location);
                },
            },
        });
    </script>
@endpush