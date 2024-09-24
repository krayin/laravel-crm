
<v-locations></v-locations>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-locations-template"
    >
        <div class="flex flex-col gap-2 p-4">
            <!-- Location Table -->
            <x-admin::table>
                <x-admin::table.thead class="rounded-lg border border-gray-200 px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                    <x-admin::table.thead.tr>
                        <x-admin::table.th>
                            @lang('admin::app.settings.warehouses.view.locations.name')
                        </x-admin::table.th>

                        <x-admin::table.th>
                            @lang('admin::app.settings.warehouses.view.locations.action')
                    </x-admin::table.th>
                    </x-admin::table.thead.tr>
                </x-admin::table.thead>

                <x-admin::table.tbody >
                    <x-admin::table.tbody.tr
                        class="border border-gray-200 dark:border-gray-800 dark:bg-gray-900"
                        v-for="location in locations"
                    >
                        <x-admin::table.td class="dark:text-white">
                            @{{ location.name }}
                        </x-admin::table.td>

                        <x-admin::table.td>
                            <div class="inline-block">
                                <p
                                    @click="remove(location)"
                                    class="cursor-pointer text-brandColor"
                                >
                                    @lang('admin::app.settings.warehouses.view.locations.delete')
                                </p>
                            </div>
                        </x-admin::table.td>
                    </x-admin::table.tbody.tr>
                </x-admin::table.tbody>
            </x-admin::table>

            <!-- Add Location -->
            <button
                type="button"
                class="flex max-w-max items-center gap-2 text-brandColor"
                @click="openModal()"
            >
                <i class="icon-add text-md !text-brandColor"></i>

                @lang('admin::app.settings.warehouses.view.locations.add-location')
            </button>

            <x-admin::form
                v-slot="{ meta, errors, handleSubmit }"
                as="div"
                ref="modalForm"
            >
                <form @submit="handleSubmit($event, addLocation)">
                    {!! view_render_event('admin.settings.tags.index.form_controls.before') !!}

                    <x-admin::modal ref="locationCreateModal">
                        <!-- Modal Header -->
                        <x-slot:header>
                            <p class="text-lg font-bold text-gray-800 dark:text-white">
                                @lang('Add Location ')
                            </p>
                        </x-slot>

                        <!-- Modal Content -->
                        <x-slot:content>
                            <x-admin::form.control-group.control
                                type="hidden"
                                name="entity_type"
                                value="Warehouses"
                            />

                            <x-admin::form.control-group.control
                                type="hidden"
                                name="warehouse_id"
                                value="{{ $warehouse->id }}"
                            />

                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label class="required">
                                    @lang('admin::app.settings.warehouses.view.locations.name')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="text"
                                    id="name"
                                    name="name"
                                    value="{{ old('name') }}"
                                    rules="required"
                                    :label="trans('admin::app.settings.warehouses.view.locations.name')"
                                    :placeholder="trans('admin::app.settings.warehouses.view.locations.name')"
                                />

                                <x-admin::form.control-group.error control-name="name" />
                            </x-admin::form.control-group>
                        </x-slot>

                        <!-- Modal Footer -->
                        <x-slot:footer>
                            <!-- Save Button -->
                            <x-admin::button
                                button-type="submit"
                                class="primary-button justify-center"
                                :title="trans('admin::app.settings.warehouses.view.locations.save-btn')"
                                ::loading="isProcessing"
                                ::disabled="isProcessing"
                            />
                        </x-slot>
                    </x-admin::modal>

                    {!! view_render_event('admin.settings.tags.index.form_controls.after') !!}
                </form>
            </x-admin::form>
        </div>
    </script>

    <script type="module">
        app.component('v-locations', {
            template: '#v-locations-template',

            data() {
                return {
                    locations: [],

                    isProcessing: false,
                };
            },

            mounted() {
                this.getLocations();
            },

            methods: {
                openModal() {
                    this.$refs.locationCreateModal.toggle();
                },

                addLocation(params, { resetForm, setErrors }) {
                    this.isProcessing = true;

                    this.$axios.post('{{ route('admin.settings.locations.store') }}', params)
                        .then((response) => {
                            this.isProcessing = false;

                            this.getLocations();

                            this.$refs.locationCreateModal.toggle();

                            this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });

                            resetForm();
                        })
                        .catch((error) => {
                            this.isProcessing = false;

                            setErrors(error.response.data.errors);
                        })
                },

                getLocations() {
                    this.$axios.get("{{ route('admin.settings.locations.search') }}", {
                        params: {
                            search: 'warehouse_id: {{ $warehouse->id }}',
                        },
                    })
                        .then(response => {
                            this.locations = response.data.data;
                        })
                        .catch(error => {
                            this.$emitter.emit('add-flash', { type: 'error', message: error.response.data.message });
                        });
                },

                remove(location) {
                    this.$emitter.emit('open-confirm-modal', {
                        agree: () => {
                            this.isLoading = true;

                            this.$axios.delete("{{ route('admin.settings.locations.delete', 'locationId') }}"
                                .replace('locationId', location.id))
                                .then(response => {
                                    this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });

                                    this.isLoading = false;

                                    this.getLocations();
                                })
                                .catch(error => {
                                    this.$emitter.emit('add-flash', { type: 'error', message: error.response.data.message });

                                    this.isLoading = false;
                                });
                        }
                    });
                }
            }
        });
    </script>
@endPushOnce
