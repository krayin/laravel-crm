<x-admin::layouts>
    <x-slot:title>
        @lang('admin::app.products.index.title')
    </x-slot>

    <v-products-create-update>
        <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
            <div class="flex flex-col gap-2">
                <div class="flex cursor-pointer items-center">
                    <!-- Breadcrumbs -->
                    <x-admin::breadcrumbs name="products" />
                </div>
    
                <div class="text-xl font-bold dark:text-gray-300">
                    @lang('admin::app.products.index.title')
                </div>
            </div>
    
            <div class="flex items-center gap-x-2.5">
                <!-- Create button for Sources -->
                @if (bouncer()->hasPermission('products.create'))
                    <div class="flex items-center gap-x-2.5">
                        <button
                            type="button"
                            class="primary-button"
                        >
                            @lang('admin::app.products.index.create-btn')
                        </button>
                    </div>
                @endif
            </div>
        </div>
    
        <!-- DataGrid Shimmer -->
        <x-admin::shimmer.datagrid />
    </v-products-create-update>

    @pushOnce('scripts')
        <script
            type="text/x-template"
            id="products-create-update-template"
        >
            <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                <div class="flex flex-col gap-2">
                    <div class="flex cursor-pointer items-center">
                        <!-- Breadcrumbs -->
                        <x-admin::breadcrumbs name="products" />
                    </div>
        
                    <div class="text-xl font-bold dark:text-gray-300">
                        @lang('admin::app.products.index.title')
                    </div>
                </div>
        
                <div class="flex items-center gap-x-2.5">
                    @if (bouncer()->hasPermission('products.create'))
                        <div class="flex items-center gap-x-2.5">
                            {!! view_render_event('krayin.admin.products.index.create_button.before') !!}
            
                            <!-- Create button for Products -->
                            <x-admin::button
                                button-type="button"
                                class="primary-button justify-center"
                                :title="trans('admin::app.products.index.create-btn')"
                                @click="selectedType=false; $refs.productsUpdateAndCreateModal.toggle()"
                            />
                            
                            {!! view_render_event('krayin.admin.products.index.create_button.after') !!}
                        </div>
                    @endif
                </div>
            </div>

            {!! view_render_event('krayin.admin.products.index.datagrid.before') !!}
        
            <!-- Datagrid -->
            <x-admin::datagrid
                src="{{ route('admin.products.index') }}"
                ref="datagrid"
            >
                <template #body="{
                    isLoading,
                    available,
                    applied,
                    selectAll,
                    sort,
                    performAction
                }">
                    <template v-if="isLoading">
                        <x-admin::shimmer.datagrid.table.body />
                    </template>
        
                    <template v-else>
                        <div
                            v-for="record in available.records"
                            class="row grid items-center gap-2.5 border-b px-4 py-4 text-gray-600 transition-all hover:bg-gray-50 dark:border-gray-800 dark:text-gray-300 dark:hover:bg-gray-950"
                            :style="`grid-template-columns: repeat(${gridsCount}, minmax(0, 1fr))`"
                        >
                            <!-- Mass Actions, Title and Created By -->
                            <div class="flex select-none items-center gap-16">
                                <input
                                    type="checkbox"
                                    :name="`mass_action_select_record_${record.id}`"
                                    :id="`mass_action_select_record_${record.id}`"
                                    :value="record.id"
                                    class="peer hidden"
                                    v-model="applied.massActions.indices"
                                >

                                <label
                                    class="icon-checkbox-outline peer-checked:icon-checkbox-select cursor-pointer rounded-md text-2xl text-gray-600 peer-checked:text-brandColor dark:text-gray-300"
                                    :for="`mass_action_select_record_${record.id}`"
                                ></label>
                            </div>
                            
                            <!-- Product SKU -->
                            <p>@{{ record.sku }}</p>
        
                            <!-- Product Name -->
                            <p>@{{ record.name }}</p>

                            <!-- Product Price -->
                            <p>@{{ record.price }}</p>

                            <!-- Product Quantity -->
                            <p>@{{ record.total_in_stock }}</p>

                            <!-- Product Total Allocated -->
                            <p>@{{ record.total_allocated }}</p>

                            <!-- Product Total Hand -->
                            <p>@{{ record.total_on_hand }}</p>

                            <!-- Actions -->
                            <div class="flex justify-end">
                                <a @click="performAction(record.actions.find(action => action.index === 'view'))">
                                    <span
                                        :class="record.actions.find(action => action.index === 'view')?.icon"
                                        class="cursor-pointer rounded-md p-1.5 text-2xl transition-all hover:bg-gray-200 dark:hover:bg-gray-800 max-sm:place-self-center"
                                    >
                                    </span>
                                </a>
                                
                                <a @click="selectedType=true; editModal(record.actions.find(action => action.index === 'edit')?.url)">
                                    <span
                                        :class="record.actions.find(action => action.index === 'edit')?.icon"
                                        class="cursor-pointer rounded-md p-1.5 text-2xl transition-all hover:bg-gray-200 dark:hover:bg-gray-800 max-sm:place-self-center"
                                    >
                                    </span>
                                </a>
    
                                <a @click="performAction(record.actions.find(action => action.index === 'delete'))">
                                    <span
                                        :class="record.actions.find(action => action.index === 'delete')?.icon"
                                        class="cursor-pointer rounded-md p-1.5 text-2xl transition-all hover:bg-gray-200 dark:hover:bg-gray-800 max-sm:place-self-center"
                                    >
                                    </span>
                                </a>
                            </div>
                        </div>
                    </template>
                </template>
            </x-admin::datagrid>
            {!! view_render_event('krayin.admin.products.index.datagrid.after') !!}
            
            <x-admin::form
                v-slot="{ meta, errors, handleSubmit }"
                as="div"
                ref="modalForm"
            >
                <form @submit="handleSubmit($event, updateOrCreate)">
                    {!! view_render_event('krayin.admin.products.index.form_controls.before') !!}

                    <x-admin::modal ref="productsUpdateAndCreateModal">
                        <!-- Modal Header -->
                        <x-slot:header>
                            <p class="text-lg font-bold text-gray-800 dark:text-white">
                                @{{ 
                                    selectedType
                                    ? "@lang('admin::app.products.index.edit.title')" 
                                    : "@lang('admin::app.products.index.create.title')"
                                }}
                            </p>
                        </x-slot>

                        <!-- Modal Content -->
                        <x-slot:content>
                            {!! view_render_event('krayin.admin.products.index.content.before') !!}

                            <x-admin::form.control-group.control
                                type="hidden"
                                name="id"
                            />

                            @include('admin::common.custom-attributes.edit', [
                                'customAttributes' => app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                                    'entity_type' => 'products',
                                    ['code' , '!=', 'quantity']
                                ]),
                            ])

                            {!! view_render_event('krayin.admin.products.index.content.after') !!}
                        </x-slot>

                        <!-- Modal Footer -->
                        <x-slot:footer>
                            <!-- Save Button -->
                            <x-admin::button
                                button-type="submit"
                                class="primary-button justify-center"
                                :title="trans('admin::app.products.index.create.save-btn')"
                                ::loading="isProcessing"
                                ::disabled="isProcessing"
                            />
                        </x-slot>
                    </x-admin::modal>

                    {!! view_render_event('krayin.admin.settings.sources.index.form_controls.after') !!}
                </form>
            </x-admin::form>
        </script>

        <script type="module">
            app.component('v-products-create-update', {
                template: '#products-create-update-template',
        
                data() {
                    return {
                        isProcessing: false,
                    };
                },
        
                computed: {
                    gridsCount() {
                        let count = this.$refs.datagrid.available.columns.length;

                        if (this.$refs.datagrid.available.actions.length) {
                            ++count;
                        }

                        if (this.$refs.datagrid.available.massActions.length) {
                            ++count;
                        }

                        return count;
                    },
                },

                methods: {
                    updateOrCreate(params, {resetForm, setErrors}) {
                        this.isProcessing = true;

                        this.$axios.post(params.id ? `{{ route('admin.products.update', '') }}/${params.id}` : "{{ route('admin.products.store') }}", {
                            ...params,
                            _method: params.id ? 'put' : 'post'
                        },

                        ).then(response => {
                            this.isProcessing = false;

                            this.$refs.productsUpdateAndCreateModal.toggle();

                            this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });

                            this.$refs.datagrid.get();

                            resetForm();
                        }).catch(error => {
                            this.isProcessing = false;

                            if (error.response.status === 422) {
                                setErrors(error.response.data.errors);
                            }
                        });
                    },
                    
                    editModal(url) {
                        this.$axios.get(url)
                            .then(response => {
                                this.$refs.modalForm.setValues(response.data.data);
                                
                                this.$refs.productsUpdateAndCreateModal.toggle();
                            })
                            .catch(error => {});
                    },
                },
            });
        </script>
    @endPushOnce
</x-admin::layouts>
