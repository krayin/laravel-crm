@extends('admin::layouts.master')

@section('page_title')
    {{ $lead->title }}
@stop

@section('css')
    <style>
        .modal-container .modal-header {
            border: 0;
        }

        .modal-container .modal-body {
            padding: 0;
        }

        .content-container .content .page-header {
            margin-bottom: 30px;
        }
    </style>
@stop

@section('content-wrapper')

    <div class="content full-page">

        {!! view_render_event('admin.leads.view.header.before', ['lead' => $lead]) !!}

        <div class="page-header">

            {{ Breadcrumbs::render('leads.view', $lead) }}

            <div class="page-title">
                <h1>
                    {{ $lead->title }}

                    @include('admin::leads.view.tags')
                </h1>
            </div>

            <div class="page-action">
                <button class="btn btn-primary btn-md" @click="openModal('updateLeadModal')">Edit</button>
            </div>
        </div>

        {!! view_render_event('admin.leads.view.header.after', ['lead' => $lead]) !!}


        {!! view_render_event('admin.leads.view.informations.before', ['lead' => $lead]) !!}

        <div class="page-content lead-view">

            <div class="lead-content-left">
                {!! view_render_event('admin.leads.view.informations.details.before', ['lead' => $lead]) !!}

                <div class="panel">
                    <div class="panel-header" style="padding-top: 0">
                        {{ __('admin::app.leads.details') }}

                        @if (($days = $lead->rotten_days) > 0)
                            <span class="lead-rotten-info">
                                <i class="icon alert-danger-icon"></i>
                                {{ __('admin::app.leads.rotten-info', ['days' => $days]) }}
                            </span>
                        @endif
                    </div>

                    <div class="panel-body">

                        <div class="custom-attribute-view">
                            @include('admin::common.custom-attributes.view', [
                                'customAttributes' => app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                                    'entity_type' => 'leads',
                                ]),
                                'entity'           => $lead,
                            ])

                            @if ($lead->stage->code == 'lost')
                                <div class="attribute-value-row">
                                    <div class="label">{{ __('admin::app.leads.lost-reason') }}</div>
                                    <div class="value">{{ $lead->lost_reason }}</div>
                                </div>
                            @endif
                        </div>

                    </div>
                </div>

                {!! view_render_event('admin.leads.view.informations.details.after', ['lead' => $lead]) !!}


                {!! view_render_event('admin.leads.view.informations.contact_person.before', ['lead' => $lead]) !!}

                <div class="panel">
                    <div class="panel-header">
                        {{ __('admin::app.leads.contact-person') }}
                    </div>

                    <div class="panel-body custom-attribute-view">

                        <div class="attribute-value-row">
                            <div class="label">Name</div>

                            <div class="value">
                                <a href="{{ route('admin.contacts.persons.edit', $lead->person->id) }}" target="_blank">
                                    {{ $lead->person->name }}
                                </a>
                            </div>
                        </div>

                        <div class="attribute-value-row">
                            <div class="label">Email</div>

                            <div class="value">
                                @include ('admin::common.custom-attributes.view.email', ['value' => $lead->person->emails])
                            </div>
                        </div>

                        <div class="attribute-value-row">
                            <div class="label">Contact Numbers</div>

                            <div class="value">
                                @include ('admin::common.custom-attributes.view.phone', ['value' => $lead->person->contact_numbers])
                            </div>
                        </div>

                        <div class="attribute-value-row">
                            <div class="label">Organization</div>

                            <div class="value">
                                @if ($lead->person->organization)
                                    <a href="{{ route('admin.contacts.organizations.edit', $lead->person->organization->id) }}" target="_blank">
                                        {{ $lead->person->organization->name }}
                                    </a>
                                @else
                                    {{ __('admin::app.common.not-available') }}
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {!! view_render_event('admin.leads.view.informations.contact_person.after', ['lead' => $lead]) !!}


                {!! view_render_event('admin.leads.view.informations.products.before', ['lead' => $lead]) !!}

                <div class="panel">
                    <div class="panel-header">
                        {{ __('admin::app.leads.products') }}
                    </div>

                    <div class="panel-body" style="position: relative">
                        @if ($lead->products->count())
                            <div class="lead-product-list">

                                @foreach ($lead->products as $product)

                                    <div class="lead-product">
                                        <div class="top-control-group">
                                            <div class="form-group">
                                                <label>{{ __('admin::app.leads.item') }}</label>

                                                <div class="control-faker">
                                                    {{ $product->name }}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="bottom-control-group" style="padding-right: 0;">
                                            <div class="form-group">
                                                <label>{{ __('admin::app.leads.price') }}</label>

                                                <div class="control-faker">
                                                    {{ $product->price }}
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label>{{ __('admin::app.leads.quantity') }}</label>

                                                <div class="control-faker">
                                                    {{ $product->quantity }}
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label>{{ __('admin::app.leads.amount') }}</label>

                                                <div class="control-faker">
                                                    {{ $product->price * $product->quantity }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                            </div>
                        @else
                            <div class="empty-record">
                                <img src="{{ asset('vendor/webkul/admin/assets/images/empty-table-icon.svg') }}">

                                <span>{{ __('admin::app.common.no-records-found') }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                {!! view_render_event('admin.leads.view.informations.products.after', ['lead' => $lead]) !!}
            </div>

            <div class="lead-content-right">

                @include('admin::leads.view.stage')

                @include('admin::leads.view.activity-action')

                @include('admin::leads.view.activity-list')
            </div>

        </div>

        {!! view_render_event('admin.leads.view.informations.after', ['lead' => $lead]) !!}
    </div>

    <edit-lead-form></edit-lead-form>
@stop

@push('scripts')
    <script src="{{ asset('vendor/webkul/admin/assets/js/tinyMCE/tinymce.min.js') }}"></script>

    <script type="text/x-template" id="edit-lead-form-template">
        <form action="{{ route('admin.leads.update', $lead->id) }}" method="post" @submit.prevent="onSubmit" enctype="multipart/form-data">
            <modal id="updateLeadModal" :is-open="$root.modalIds.updateLeadModal">
                <h3 slot="header-title">{{ __('admin::app.leads.edit-title') }}</h3>

                <div slot="header-actions">
                    {!! view_render_event('admin.leads.view.edit.form_buttons.before', ['lead' => $lead]) !!}

                    <button class="btn btn-sm btn-secondary-outline" @click="$root.closeModal('updateLeadModal')">{{ __('admin::app.leads.cancel') }}</button>

                    <button class="btn btn-sm btn-primary">{{ __('admin::app.leads.save-btn-title') }}</button>

                    {!! view_render_event('admin.leads.view.edit.form_buttons.after', ['lead' => $lead]) !!}
                </div>

                <div slot="body">
                    {!! view_render_event('admin.leads.view.edit.form_controls.before', ['lead' => $lead]) !!}

                    @csrf()

                    <input name="_method" type="hidden" value="PUT">
                    <input type="hidden" id="lead_pipeline_stage_id" name="lead_pipeline_stage_id" value="{{ $lead->lead_pipeline_stage_id }}" />
                    <tabs>
                        {!! view_render_event('admin.leads.view.edit.form_controls.details.before', ['lead' => $lead]) !!}

                        <tab name="{{ __('admin::app.leads.details') }}" :selected="true">
                            @include('admin::common.custom-attributes.edit', [
                                'customAttributes'  => app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                                    'entity_type' => 'leads',
                                ]),
                                'customValidations' => [
                                    'expected_close_date' => [
                                        'date_format:yyyy-MM-dd',
                                        'after:' .  \Carbon\Carbon::yesterday()->format('Y-m-d')
                                    ],
                                ],
                                'entity'            => $lead,
                            ])
                        </tab>

                        {!! view_render_event('admin.leads.view.edit.form_controls.details.after', ['lead' => $lead]) !!}


                        {!! view_render_event('admin.leads.view.edit.form_controls.contact_person.before', ['lead' => $lead]) !!}

                        <tab name="{{ __('admin::app.leads.contact-person') }}">
                            @include('admin::leads.common.contact')

                            <contact-component :data='@json(old('person') ?: $lead->person)'></contact-component>
                        </tab>

                        {!! view_render_event('admin.leads.view.edit.form_controls.contact_person.after', ['lead' => $lead]) !!}


                        {!! view_render_event('admin.leads.view.edit.form_controls.products.before', ['lead' => $lead]) !!}

                        <tab name="{{ __('admin::app.leads.products') }}">
                            @include('admin::leads.common.products')

                            <product-list :data='@json(old('products') ?: $lead->products)'></product-list>
                        </tab>

                        {!! view_render_event('admin.leads.view.edit.form_controls.products.after', ['lead' => $lead]) !!}
                    </tabs>

                    {!! view_render_event('admin.leads.view.edit.form_controls.after', ['lead' => $lead]) !!}
                </div>
            </modal>
        </form>
    </script>

    <script>
        Vue.component('edit-lead-form', {

            template: '#edit-lead-form-template',

            inject: ['$validator'],

            mounted: function() {
                if (! Array.isArray(window.serverErrors)) {
                    this.$root.openModal('updateLeadModal');

                    var self = this;

                    setTimeout(() => {
                        self.$root.addServerErrors();
                    });
                }
            },

            methods: {
                onSubmit: function(e) {
                    this.$root.onSubmit(e);
                }
            }
        });
    </script>
@endpush
