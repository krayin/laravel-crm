{!! view_render_event('admin.products.view.inventory.before', ['product' => $product]) !!}

<!-- Product Inventories Component -->
<v-product-inventories></v-product-inventories>

{!! view_render_event('admin.products.view.inventory.after', ['product' => $product]) !!}

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-product-inventories-template"
    >
        <div class="p-4">
            <div class="flex flex-col gap-4">
                {!! view_render_event('admin.products.view.inventory.table.before', ['product' => $product]) !!}

                <div class="block w-full overflow-x-auto">
                    <x-admin::table>
                        <!-- Table Head -->
                        <x-admin::table.thead>
                            <x-admin::table.thead.tr>
                                <x-admin::table.th>
                                    @lang('admin::app.products.view.inventory.source')
                                </x-admin::table.th>

                                <x-admin::table.th >
                                    @lang('admin::app.products.view.inventory.in-stock')
                                </x-admin::table.th>

                                <x-admin::table.th>
                                    @lang('admin::app.products.view.inventory.allocated')
                                </x-admin::table.th>

                                <x-admin::table.th>
                                    @lang('admin::app.products.view.inventory.on-hand')
                                </x-admin::table.th>

                                <x-admin::table.th>
                                    @lang('admin::app.products.view.inventory.actions')
                                </x-admin::table.th>
                            </x-admin::table.thead.tr>
                        </x-admin::table.thead>

                        <!-- Table Body -->
                        <x-admin::table.tbody class="align-top">
                            <template v-for="warehouse in productWarehouses">
                                <x-admin::table.tbody.tr class="hover:bg-gray-50 dark:hover:bg-gray-950">
                                    <x-admin::table.td
                                        class="truncate font-bold dark:text-white"
                                        ::title="warehouse.name"
                                    >
                                        @{{ warehouse.name }}
                                    </x-admin::table.td>

                                    <x-admin::table.td class="dark:text-white">
                                        @{{ warehouse.in_stock }}
                                    </x-admin::table.td>

                                    <x-admin::table.td class="dark:text-white">
                                        @{{ warehouse.allocated }}
                                    </x-admin::table.td>

                                    <x-admin::table.td class="dark:text-white">
                                        @{{ warehouse.on_hand }}
                                    </x-admin::table.td>

                                    <x-admin::table.td>
                                        <div
                                            @click="selectWarehouse(warehouse)"
                                            class="cursor-pointer text-brandColor"
                                        >
                                            @lang('admin::app.products.view.inventory.assign')
                                        </div>
                                    </x-admin::table.td>
                                </x-admin::table.tbody.tr>

                                <template v-for="location in warehouse.locations">
                                    <x-admin::table.tbody.tr class="border-b border-gray-200 hover:bg-gray-50 dark:border-gray-800 dark:hover:bg-gray-950">
                                        <x-admin::table.td class="dark:text-white">
                                            @{{ location.name }}
                                        </x-admin::table.td>

                                        <x-admin::table.td class="dark:text-white">
                                            @{{ location.in_stock }}
                                        </x-admin::table.td>

                                        <x-admin::table.td class="dark:text-white">
                                            @{{ location.allocated }}
                                        </x-admin::table.td>

                                        <x-admin::table.td class="dark:text-white">
                                            @{{ location.on_hand }}
                                        </x-admin::table.td>

                                        <x-admin::table.td></x-admin::table.td>
                                    </x-admin::table.tbody.tr>
                                </template>
                            </template>
                        </x-admin::table.tbody>
                    </x-admin::table>
                </div>

                {!! view_render_event('admin.products.view.inventory.table.after', ['product' => $product]) !!}

                {!! view_render_event('admin.products.view.inventory.source.before', ['product' => $product]) !!}

                <!-- Add Source dropdown -->
                <div v-if="notAddedWarehouses.length">
                    <x-admin::dropdown
                        position="bottom-right"
                        class="!static"
                    >
                        <x-slot:toggle>
                            <button
                                type="button"
                                class="flex max-w-max items-center gap-2 text-brandColor"
                            >
                                <i class="icon-add text-md !text-brandColor"></i>

                                @lang('admin::app.products.view.inventory.add-source')
                            </button>
                        </x-slot>

                        <x-slot:menu class="!top-[30px] max-h-[200px] overflow-auto">
                            {!! view_render_event('admin.products.view.inventory.source.menu.item.before', ['product' => $product]) !!}

                            <x-admin::dropdown.menu.item
                                v-for="warehouse in notAddedWarehouses"
                                @click="addWarehouse(warehouse)"
                            >
                                @{{ warehouse.name }}

                            </x-admin::dropdown.menu.item>

                            {!! view_render_event('admin.products.view.inventory.source.menu.item.after', ['product' => $product]) !!}
                        </x-slot>
                    </x-admin::dropdown>
                </div>

                {!! view_render_event('admin.products.view.inventory.source.after', ['product' => $product]) !!}
            </div>

            {!! view_render_event('admin.products.view.inventory.form_controls.before', ['product' => $product]) !!}

            <!-- Drawer for Add Location -->
            <x-admin::form
                v-slot="{ meta, errors, handleSubmit }"
                as="div"
            >
                <form
                    @submit="handleSubmit($event, onSubmit)"
                    ref="locationForm"
                >
                    {!! view_render_event('admin.products.view.inventory.form_controls.drawer.before', ['product' => $product]) !!}

                    <!-- Edit Drawer -->
                    <x-admin::drawer
                        ref="assignLocationDrawer"
                        width="500px"
                        class="text-left"
                    >
                        <!-- Drawer Header -->
                        <x-slot:header>
                            {!! view_render_event('admin.products.view.inventory.form_controls.drawer.header.before', ['product' => $product]) !!}

                            <div class="flex items-center justify-between">
                                <p class="text-xl font-medium dark:text-white">
                                    @{{ selectedWarehouse.name }}
                                </p>

                                <button
                                    type="submit"
                                    class="primary-button ltr:mr-11 rtl:ml-11"
                                >
                                     @lang('admin::app.products.view.inventory.save')
                                </button>
                            </div>

                            {!! view_render_event('admin.products.view.inventory.form_controls.drawer.header.after', ['product' => $product]) !!}
                        </x-slot>

                        <!-- Drawer Content -->
                        <x-slot:content>
                            {!! view_render_event('admin.products.view.inventory.form_controls.drawer.content.before', ['product' => $product]) !!}

                            <v-warehouse-location-inventories
                                :warehouse="selectedWarehouse"
                            ></v-warehouse-location-inventories>

                            {!! view_render_event('admin.products.view.inventory.form_controls.drawer.content.after', ['product' => $product]) !!}
                        </x-slot>
                    </x-admin::drawer>

                    {!! view_render_event('admin.products.view.inventory.form_controls.drawer.after', ['product' => $product]) !!}
                </form>
            </x-admin::form>

            {!! view_render_event('admin.products.view.inventory.form_controls.after', ['product' => $product]) !!}
        </div>
    </script>

    <script
        type="text/x-template"
        id="v-warehouse-location-inventories-template"
    >
        <div class="flex flex-col gap-2">
            <!-- Add location header -->
            <div class="block w-full overflow-x-auto">
                <x-admin::table class="!min-w-[600px]">
                    <x-admin::table.thead>
                        <x-admin::table.thead.tr>
                            <x-admin::table.th class="!w-56">
                                @lang('admin::app.products.view.inventory.location')
                            </x-admin::table.th>

                            <x-admin::table.th class="!w-[150px]">
                                @lang('admin::app.products.view.inventory.in-stock')
                            </x-admin::table.th>

                            <x-admin::table.th class="!w-[150px]">
                                @lang('admin::app.products.view.inventory.allocated')
                            </x-admin::table.th>

                            <x-admin::table.th></x-admin::table.th>
                        </x-admin::table.thead.tr>
                    </x-admin::table.thead>

                    <x-admin::table.tbody class="align-top">
                        <v-warehouse-location-inventory-item
                            v-for='(location, index) in warehouseLocations'
                            :location="location"
                            :warehouse="warehouse"
                            :key="index"
                            :index="index"
                            @onRemove="removeLocation($event)"
                        ></v-warehouse-location-inventory-item>
                    </x-admin::table.tbody>
                </x-admin::table>
            </div>

            <!-- Add more button for location -->
            <button
                type="button"
                class="flex max-w-max items-center gap-2 text-brandColor"
                @click="addLocation"
            >
                <i class="icon-add text-md !text-brandColor"></i>

                @lang('admin::app.products.view.inventory.add-more')
            </button>
        </div>
    </script>

    <script
        type="text/x-template"
        id="v-warehouse-location-inventory-item-template"
    >
        <!-- Input fields for add locations -->
        <x-admin::table.tbody.tr>
            <x-admin::table.td class="!px-2">
                <x-admin::form.control-group>
                    <x-admin::lookup
                        ::src="src"
                        ::name="`${inputName('warehouse_location_id')}`"
                        ::params="params"
                        v-model="location['id']"
                        rules="required"
                        :placeholder="trans('admin::app.products.view.inventory.location')"
                        :label="trans('admin::app.products.view.inventory.location')"
                        @on-selected="add"
                        ::value="{ id: location.id, name: location.name }"
                    />

                    <input
                        type="hidden"
                        :name="'inventories[inventory_' + index + '][warehouse_id]'"
                        v-model="warehouse.id"
                    />

                    <x-admin::form.control-group.error ::name="`${inputName('warehouse_location_id')}`"/>
                </x-admin::form.control-group>
            </x-admin::table.td>

            <x-admin::table.td class="!px-2">
                <x-admin::form.control-group.control
                    type="number"
                    ::name="'inventories[inventory_' + index + '][in_stock]'"
                    v-model="location.in_stock"
                    rules="required|numeric|min_value:0"
                    :label="trans('admin::app.products.view.inventory.in-stock')"
                    :placeholder="trans('admin::app.products.view.inventory.in-stock')"
                />

                <x-admin::form.control-group.error ::name="'inventories[inventory_' + index + '][in_stock]'"/>
            </x-admin::table.td>

            <x-admin::table.td class="!px-2">
                <x-admin::form.control-group.control
                    type="number"
                    ::name="'inventories[inventory_' + index + '][allocated]'"
                    v-model="location.allocated"
                    ::rules="`required|numeric|min_value:0|max_value:${location.in_stock}`"
                    :label="trans('admin::app.products.view.inventory.allocated')"
                    :placeholder="trans('admin::app.products.view.inventory.allocated')"
                />

                <x-admin::form.control-group.error ::name="'inventories[inventory_' + index + '][allocated]'"/>
            </x-admin::table.td>

            <x-admin::table.td class="!px-2 !py-[22px]">
                <i
                    @click="remove"
                    class="icon-delete cursor-pointer text-2xl"
                ></i>
            </x-admin::table.td>
        </x-admin::table.tbody.tr>
    </script>

    <script type="module">
        app.component('v-product-inventories', {
            template: '#v-product-inventories-template',

            data() {
                return {
                    warehouses: [],

                    productWarehouses: [],

                    selectedWarehouse: null,
                };
            },

            computed: {
                notAddedWarehouses () {
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
                getAllWarehouses() {
                    this.$axios.get("{{ route('admin.settings.warehouses.search') }}")
                        .then(response => {
                            this.warehouses = response.data;
                        })
                        .catch(error => {
                            console.log(error);
                        });
                },

                getProductWarehouses() {
                    this.$axios.get("{{ route('admin.products.warehouses', $product->id) }}")
                        .then(response => {
                            this.productWarehouses = response.data;
                        })
                        .catch(error => {
                            console.log(error);
                        });
                },

                addWarehouse(warehouse) {
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

                selectWarehouse(warehouse) {
                    this.selectedWarehouse = warehouse;

                    setTimeout(() => {
                        this.$refs.assignLocationDrawer.open();
                    }, 0);
                },

                onSubmit(params, { setErrors }) {
                    let formData = new FormData(this.$refs.locationForm);

                    this.$axios.post("{{ route('admin.products.inventories.store', ['id' => $product->id, 'warehouseId' => 'warehouseId']) }}".replace('warehouseId', this.selectedWarehouse.id), formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    }).then(response => {
                        this.getAllWarehouses();

                        this.getProductWarehouses();

                        this.$refs.assignLocationDrawer.close();

                        this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });
                    })
                    .catch(error => {
                        setErrors(error.response.data.errors);

                        this.$emitter.emit('add-flash', { type: 'error', message: error.response.data.message });
                    });
                },
            },
        });

        app.component('v-warehouse-location-inventories', {
            template: '#v-warehouse-location-inventories-template',

            props: ['warehouse'],

            data() {
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
                addLocation() {
                    this.warehouseLocations.push({
                        id: null,
                        name: '',
                        in_stock: 0,
                        allocated: 0,
                        on_hand: 0,
                    })
                },

                removeLocation(inventory) {
                    const index = this.warehouseLocations.indexOf(inventory);

                    if (index !== -1) {
                        this.warehouseLocations.splice(index, 1);
                    }
                },
            },
        });

        app.component('v-warehouse-location-inventory-item', {
            template: '#v-warehouse-location-inventory-item-template',

            props: ['index', 'warehouse', 'location'],

            data() {
                return {
                    isSearching: false,
                }
            },

            computed: {
                src() {
                    return '{{ route('admin.settings.locations.search') }}';
                },

                params() {
                    return {
                        search: 'warehouse_id:' + this.warehouse.id + ';name:' + this.location.name,
                        searchFields: 'warehouse_id:=;name:like',
                        searchJoin: 'and'
                    };
                },
            },

            methods: {
                inputName(type) {
                  return 'inventories[inventory_' + this.index + ']['+ type +']';
                },

                /**
                 * Add the product.
                 *
                 * @param {Object} result
                 *
                 * @return {void}
                 */
                 add(result) {
                    this.location.id = result.id;

                    this.location.warehouse_id = result.warehouse_id;

                    this.location.name = result.name;
                },

                remove() {
                    this.$emit('onRemove', this.location);
                },
            },
        });
    </script>
@endPushOnce
