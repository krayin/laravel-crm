{!! view_render_event('admin.leads.view.products.before', ['lead' => $lead]) !!}

<v-lead-products></v-lead-products>

{!! view_render_event('admin.leads.view.products.after', ['lead' => $lead]) !!}

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-lead-products-template"
    >
        <div v-if="products.length" class="flex flex-col gap-4 p-3">
            {!! view_render_event('admin.leads.view.products.table.before', ['lead' => $lead]) !!}

            <!-- Table -->
            <x-admin::table>
                {!! view_render_event('admin.leads.view.products.table.table_head.before', ['lead' => $lead]) !!}

                <!-- Table Head -->
                <x-admin::table.thead>
                    <x-admin::table.thead.tr>
                        <x-admin::table.th>
                            @lang('admin::app.leads.view.products.product-name')
                        </x-admin::table.th>

                        <x-admin::table.th class="ltr:text-left rtl:text-right">
                            @lang('admin::app.leads.view.products.quantity')
                        </x-admin::table.th>

                        <x-admin::table.th class="ltr:text-left rtl:text-right">
                            @lang('admin::app.leads.view.products.price')
                        </x-admin::table.th>

                        <x-admin::table.th class="ltr:text-left rtl:text-right">
                            @lang('admin::app.leads.view.products.amount')
                        </x-admin::table.th>

                        <x-admin::table.th class="ltr:text-right rtl:text-left">
                            @lang('admin::app.leads.view.products.action')
                        </x-admin::table.th>
                    </x-admin::table.thead.tr>
                </x-admin::table.thead>

                {!! view_render_event('admin.leads.view.products.table.table_head.after', ['lead' => $lead]) !!}

                {!! view_render_event('admin.leads.view.products.table.table_body.before', ['lead' => $lead]) !!}

                <!-- Table Body -->
                <x-admin::table.tbody>
                    <!-- Product Item Vue Component -->
                    <v-product-item
                        v-for='(product, index) in products'
                        :product="product"
                        :key="index"
                        :index="index"
                        @onRemoveProduct="removeProduct($event)"
                    ></v-product-item>
                </x-admin::table.tbody>
                {!! view_render_event('admin.leads.view.products.table.table_body.after', ['lead' => $lead]) !!}
            </x-admin::table>

            {!! view_render_event('admin.leads.view.products.table.after', ['lead' => $lead]) !!}

            {!! view_render_event('admin.leads.view.products.table.add_more.before', ['lead' => $lead]) !!}

            <!-- Add New Product Item -->
            <div>
                <button
                    type="button"
                    class="flex max-w-max items-center gap-2 text-brandColor"
                    @click="addProduct"
                >
                    <i class="icon-add text-md !text-brandColor"></i>

                    @lang('admin::app.leads.view.products.add-more')
                </button>
            </div>

            {!! view_render_event('admin.leads.view.products.table.add_more.after', ['lead' => $lead]) !!}
        </div>

        <div v-else>
            <div class="grid justify-center justify-items-center gap-3.5 py-12">
                <img
                    class="dark:mix-blend-exclusion dark:invert"
                    src="{{ vite()->asset('images/empty-placeholders/products.svg') }}"
                >

                <div class="flex flex-col items-center gap-2">
                    <p class="text-xl font-semibold dark:text-white">
                        @lang('admin::app.leads.view.products.empty-title')
                    </p>

                    <p class="text-gray-400 dark:text-gray-400">
                        @lang('admin::app.leads.view.products.empty-info')
                    </p>
                </div>

                <div
                    class="secondary-button"
                    @click="addProduct"
                >
                     @lang('admin::app.leads.view.products.add-product')
                </div>
            </div>
        </div>
    </script>

    <script
        type="text/x-template"
        id="v-product-item-template"
    >
        <x-admin::table.tbody.tr class="border-b border-gray-200 align-top dark:border-gray-800">
            <!-- Product Name -->
            <x-admin::table.td class="!px-4">
                <v-form v-slot="{ errors }">
                    <x-admin::form.control-group class="!mb-0">
                        <x-admin::lookup 
                            ::src="src"
                            name="name"
                            ::params="params"
                            :placeholder="trans('admin::app.leads.view.products.product-name')"
                            @on-selected="(product) => addProduct(product)"
                            ::value="{ id: product.product_id, name: product.name }"
                        />

                        <x-admin::form.control-group.control
                            type="hidden"
                            name="product_id"
                            v-model="product.product_id"
                            rules="required"
                            :label="trans('admin::app.leads.view.products.product-name')"
                            :placeholder="trans('admin::app.leads.view.products.product-name')"
                            ::url="url(product)"
                        />
                
                        <x-admin::form.control-group.error ::name="`${inputName}[product_id]`" />
                    </x-admin::form.control-group>
                </v-form>
            </x-admin::table.td>

            <!-- Product Quantity -->
            <x-admin::table.td class="!px-4 ltr:text-right rtl:text-left">
                <v-form v-slot="{ errors }">
                    <x-admin::form.control-group class="!mb-0">
                        <x-admin::form.control-group.control
                            type="inline"
                            ::name="'quantity'"
                            ::value="product.quantity"
                            rules="required|decimal:4"
                            :label="trans('admin::app.leads.view.products.quantity')"
                            :placeholder="trans('admin::app.leads.view.products.quantity')"
                            @on-change="(event) => product.quantity = event.value"
                            ::url="url(product)"
                            ::params="{product_id: product.product_id}"
                            position="left"
                            ::errors="errors"
                        />
                    </x-admin::form.control-group>
                </v-form>
            </x-admin::table.td>

            <!-- Price -->
            <x-admin::table.td class="!px-4 ltr:text-right rtl:text-left">
                <v-form v-slot="{ errors }">
                    <x-admin::form.control-group class="!mb-0">
                        <x-admin::form.control-group.control
                            type="inline"
                            ::name="'price'"
                            ::value="product.price"
                            rules="required|decimal:4"
                            :label="trans('admin::app.leads.view.products.price')"
                            :placeholder="trans('admin::app.leads.view.products.price')"
                            @on-change="(event) => product.price = event.value"
                            ::url="url(product)"
                            ::params="{product_id: product.product_id}"
                            position="left"
                            ::value-label="$admin.formatPrice(product.price)"
                            ::errors="errors"
                        />
                    </x-admin::form.control-group>
                </v-form>
            </x-admin::table.td>

            <!-- Total -->
            <x-admin::table.td class="!px-4 ltr:text-right rtl:text-left">
                <v-form v-slot="{ errors }">
                    <x-admin::form.control-group class="!mb-0">
                        <x-admin::form.control-group.control
                            type="inline"
                            ::name="'amount'"
                            ::value="product.price * product.quantity"
                            rules="required|decimal:4"
                            :label="trans('admin::app.leads.view.products.total')"
                            :placeholder="trans('admin::app.leads.view.products.total')"
                            ::allowEdit="false"
                            ::url="url(product)"
                            position="left"
                            ::value-label="$admin.formatPrice(product.price * product.quantity)"
                            ::errors="errors"
                        />
                    </x-admin::form.control-group>
                </v-form>
            </x-admin::table.td>

            <!-- Action -->
            <x-admin::table.td class="ltr:text-right rtl:text-left">
                <template v-if="product.is_new">
                    <x-admin::form.control-group class="!mb-0">
                        <div class="flex items-center justify-center gap-4">
                            <i
                                @click="attachProduct(product)"
                                class="icon-enter cursor-pointer text-2xl text-black"
                            ></i>

                            <i
                                @click="removeProduct"
                                class="icon-cross-large cursor-pointer text-2xl text-black"
                            ></i>
                        </div>
                    </x-admin::form.control-group>
                </template>

                <template v-else>
                    <x-admin::form.control-group class="!mb-0">
                        <i
                            @click="removeProduct"
                            class="icon-delete cursor-pointer text-2xl"
                        ></i>
                    </x-admin::form.control-group>
                </template>
            </x-admin::table.td>
        </x-admin::table.tbody.tr>
    </script>

    <script type="module">
        app.component('v-lead-products', {
            template: '#v-lead-products-template',

            props: ['data'],

            data: function () {
                return {
                    products: @json($lead->products),
                }
            },

            methods: {
                addProduct() {
                    this.products.push({
                        is_new: true,
                        id: null,
                        product_id: null,
                        name: '',
                        quantity: 0,
                        price: 0,
                        amount: null,
                    })
                },

                removeProduct (product) {
                    const index = this.products.indexOf(product);
                    this.products.splice(index, 1);
                },
            },
        });

        app.component('v-product-item', {
            template: '#v-product-item-template',

            props: ['index', 'product'],

            data() {
                return {
                    products: [],
                }
            },

            computed: {
                inputName() {
                    if (this.product.id) {
                        return "products[" + this.product.id + "]";
                    }

                    return "products[product_" + this.index + "]";
                },

                src() {
                    return '{{ route('admin.products.search') }}';
                },

                params() {
                    return {
                        params: {
                            query: this.product.name
                        },
                    };
                },
            },

            methods: {
                /**
                 * Add the product.
                 *
                 * @param {Object} result
                 *
                 * @return {void}
                 */
                addProduct(result) {
                    this.product.product_id = result.id;

                    this.product.name = result.name;

                    this.product.price = result.price;

                    this.product.quantity = result.quantity ?? 0;
                },

                /**
                 * Attach Product.
                 *
                 * @return {void}
                 */
                attachProduct(product) {
                    this.$axios.post('{{ route('admin.leads.product.add', $lead->id) }}', {
                        _method: 'PUT',
                        ...product,
                    })
                        .then(response => {
                            this.product.is_new = false;

                            this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });
                        })
                        .catch(error => {});
                },

                /**
                 * Remove the product.
                 *
                 * @return {void}
                 */
                removeProduct() {
                    this.$emitter.emit('open-confirm-modal', {
                        agree: () => {
                            this.$axios.post('{{ route('admin.leads.product.remove', $lead->id) }}', {
                                _method: 'DELETE',
                                product_id: this.product.product_id,
                            })
                                .then(response => {
                                    this.$emit('onRemoveProduct', this.product);

                                    this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });
                                })
                                .catch(error => {});
                        },
                    });
                },

                /**
                 * Get the product URL.
                 *
                 * @param {Object} product
                 *
                 * @return {String}
                 */
                url(product) {
                    if (product.is_new) {
                        return;
                    }

                    return '{{ route('admin.leads.product.add', $lead->id) }}';
                }
            }
        });
    </script>
@endPushOnce
