@extends('admin::layouts.master')

@section('page_title')
    {{ $warehouse->name }}
@stop

@section('content-wrapper')
    <div class="content full-page">
        {!! view_render_event('admin.settings.warehouses.view.header.before', ['warehouse' => $warehouse]) !!}

        <div class="page-header">
            {{ Breadcrumbs::render('settings.warehouses.view', $warehouse) }}

            <div class="page-title">
                <h1>
                    {{ $warehouse->name }}
                </h1>
            </div>

            <div class="page-action">
            </div>
        </div>

        {!! view_render_event('admin.settings.warehouses.view.content.after', ['warehouse' => $warehouse]) !!}

        <div class="page-content warehouse-view">
            {!! view_render_event('admin.settings.warehouses.view.informations.before', ['warehouse' => $warehouse]) !!}

            <div class="panel">
                <div class="panel-header">
                    {{ __('admin::app.settings.warehouses.information') }}
                </div>

                <div class="panel-body">
                    <div class="custom-attribute-view">
                        <h3>
                            {{ __('admin::app.settings.warehouses.general-information') }}
                        </h3>

                        @include('admin::common.custom-attributes.view', [
                            'customAttributes' => app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                                'entity_type' => 'warehouses',
                                ['code', 'NOTIN', ['contact_name', 'contact_emails', 'contact_numbers', 'contact_address']]
                            ])->sortBy('sort_order'),
                            'entity'           => $warehouse,
                        ])
                    </div>

                    <div class="custom-attribute-view">
                        <h3>
                            {{ __('admin::app.settings.warehouses.contact-information') }}
                        </h3>

                        @include('admin::common.custom-attributes.view', [
                            'customAttributes' => app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                                'entity_type' => 'warehouses',
                                ['code', 'IN', ['contact_name', 'contact_emails', 'contact_numbers', 'contact_address']]
                            ])->sortBy('sort_order'),
                            'entity'           => $warehouse,
                        ])
                    </div>
                </div>
            </div>

            {!! view_render_event('admin.settings.warehouses.view.informations.after', ['warehouse' => $warehouse]) !!}


            {!! view_render_event('admin.settings.warehouses.view.locations.after', ['warehouse' => $warehouse]) !!}
            
            <div class="panel warehouse-locations">
                <button class="btn btn-md btn-primary" @click="$root.openModal('addLocationModal')">
                    {{ __('admin::app.settings.warehouses.add-location') }}
                </button>

                <div class="panel-body">
                    <table-component data-src="{{ route('admin.settings.locations.index', ['warehouse_id' => $warehouse->id]) }}"><table-component>
                </div>
            </div>

            {!! view_render_event('admin.settings.warehouses.view.locations.after', ['warehouse' => $warehouse]) !!}

            <v-add-location></v-add-location>

            {!! view_render_event('admin.settings.warehouses.view.content.after', ['warehouse' => $warehouse]) !!}
        </div>
    </div>
@stop

@push('scripts')
    <script type="text/x-template" id="v-add-location-template">
        <form
            action="{{ route('admin.settings.warehouses.create', $warehouse->id) }}"
            method="post"
            @submit.prevent="onSubmit"
            enctype="multipart/form-data"
        >
            <modal id="addLocationModal" :is-open="$root.modalIds.addLocationModal">
                <h3 slot="header-title">
                    {{ __('admin::app.settings.warehouses.add-location') }}
                </h3>

                <div slot="header-actions">
                    <button
                        class="btn btn-sm btn-secondary-outline"
                        @click="$root.closeModal('addLocationModal')"
                    >
                        {{ __('admin::app.settings.warehouses.cancel') }}
                    </button>

                    <button class="btn btn-sm btn-primary">{{ __('admin::app.leads.save-btn-title') }}</button>
                </div>

                <div slot="body">
                    {!! view_render_event('admin.settings.warehouses.view.locations.create.form_controls.before', ['warehouse' => $warehouse]) !!}

                    <input type="hidden" name="warehouse_id" value="{{ $warehouse->id }}" />

                    @include('admin::common.custom-attributes.edit', [
                        'customAttributes' => app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                            'entity_type' => 'locations',
                            ['code', '<>', 'warehouse_id']
                        ]),
                    ])

                    {!! view_render_event('admin.settings.warehouses.view.locations.create.form_controls.after', ['warehouse' => $warehouse]) !!}
                </div>
            </modal>
        </form>
    </script>

    <script>
        Vue.component('v-add-location', {
            template: '#v-add-location-template',

            inject: ['$validator'],

            methods: {
                onSubmit: function(e) {
                    var self = this;

                    let params = new FormData(e.target);

                    this.$validator.validateAll().then((result) => {
                        if (result) {
                            self.$http.post(`{{ route('admin.settings.locations.store') }}`, params).then(response => {
                                EventBus.$emit('refresh_table_data', {
                                    usePrevious: true
                                });

                                self.$root.closeModal('addLocationModal');
                            })
                            .catch(error => {
                                window.serverErrors = error.response.data.errors;

                                self.$root.addServerErrors();
                            });
                        }
                    });
                }
            }
        });
    </script>
@endpush