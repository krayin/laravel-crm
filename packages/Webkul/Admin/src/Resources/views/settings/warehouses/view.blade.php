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
                <div class="panel-header">
                    {{ __('admin::app.settings.warehouses.locations') }}
                </div>

                <div class="panel-body">
                    <v-locations></v-locations>
                </div>
            </div>

            {!! view_render_event('admin.settings.warehouses.view.locations.after', ['warehouse' => $warehouse]) !!}

            {!! view_render_event('admin.settings.warehouses.view.content.after', ['warehouse' => $warehouse]) !!}
        </div>
    </div>
@stop

@push('scripts')
    <script type="text/x-template" id="v-locations-template">
        <div class="table">
            <table>
                <thead>
                    <tr>
                        <th>{{ __('admin::app.settings.warehouses.name') }}</th>
                        <th>{{ __('admin::app.settings.warehouses.action') }}</th>
                    </tr>
                </thead>

                <tbody>
                    <tr v-for="location in locations">
                        <td>@{{ location.name }}</td>

                        <td>
                            <a href="#" @click.prevent="remove(location)">{{ __('admin::app.settings.warehouses.delete') }}</a>
                        </td>
                    </tr>
                </tbody>

                <tfoot>
                    <tr>
                        <td colspan="2">
                            <span @click="$root.openModal('addLocationModal')">
                                {{ __('admin::app.settings.warehouses.add-location') }}
                            </span>
                        </td>
                    </tr>
                </tfoot>
            </table>

            <form
                action="{{ route('admin.settings.warehouses.create', $warehouse->id) }}"
                method="post"
                @submit.prevent="create"
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

                        <button class="btn btn-sm btn-primary">{{ __('admin::app.settings.warehouses.save-btn') }}</button>
                    </div>

                    <div slot="body">
                        {!! view_render_event('admin.settings.warehouses.view.locations.create.form_controls.before', ['warehouse' => $warehouse]) !!}

                        <input type="hidden" name="warehouse_id" value="{{ $warehouse->id }}" />

                        <div class="form-group" :class="[errors.has('name') ? 'has-error' : '']">
                            <label class="required">
                                {{ __('admin::app.settings.warehouses.name') }}
                            </label>

                            <input
                                type="text"
                                name="name"
                                class="control"
                                value="{{ old('name') }}"
                                placeholder="{{ __('admin::app.settings.warehouses.name') }}"
                                v-validate="'required'"
                                data-vv-as="{{ __('admin::app.settings.warehouses.name') }}"
                            />

                            <span class="control-error" v-if="errors.has('name')">
                                @{{ errors.first('name') }}
                            </span>
                        </div>

                        {!! view_render_event('admin.settings.warehouses.view.locations.create.form_controls.after', ['warehouse' => $warehouse]) !!}
                    </div>
                </modal>
            </form>
        </div>
    </script>

    <script>
        Vue.component('v-locations', {
            template: '#v-locations-template',

            inject: ['$validator'],

            data: function () {
                return {
                    locations: [],
                }
            },

            mounted() {
                this.get();
            },

            methods: {
                get: function() {
                    let self = this;

                    this.$http.get("{{ route('admin.settings.locations.search') }}")
                        .then(response => {
                            self.locations = response.data;
                        })
                        .catch(error => {
                            console.log(error);
                        });
                },

                create: function(e) {
                    var self = this;

                    let params = new FormData(e.target);

                    this.$validator.validateAll().then((result) => {
                        if (result) {
                            self.$http.post(`{{ route('admin.settings.locations.store') }}`, params).then(response => {
                                self.get();

                                self.$root.closeModal('addLocationModal');
                            })
                            .catch(error => {
                                window.serverErrors = error.response.data.errors;

                                self.$root.addServerErrors();
                            });
                        }
                    });
                },

                remove(location) {
                    if (! confirm("{{ __('admin::app.settings.warehouses.confirm-delete') }}")) {
                        return;
                    }

                    let self = this;

                    this.$http.delete("{{ route('admin.settings.locations.delete', 'locationId') }}".replace('locationId', location.id))
                        .then(response => {
                            self.get();
                        })
                        .catch(error => {
                            console.log(error);
                        });
                }
            },
        });
    </script>
@endpush