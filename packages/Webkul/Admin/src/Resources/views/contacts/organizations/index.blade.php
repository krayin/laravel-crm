@extends('ui::datagrid.table')

@section('page_title')
    {{ __('admin::app.contacts.organizations.title') }}
@stop

@section('table-header')
    {!! view_render_event('admin.contacts.organizations.index.header.before') !!}

    {{ Breadcrumbs::render('contacts.organizations') }}

    {{ __('admin::app.contacts.organizations.title') }}

    {!! view_render_event('admin.contacts.organizations.index.header.after') !!}
@stop

@php
    $tableClass = "\Webkul\Admin\DataGrids\Contact\OrganizationDataGrid";
@endphp

@section('table-action')
    <button class="btn btn-md btn-primary" @click="openModal('addOrganizationModal')">{{ __('admin::app.contacts.organizations.add-title') }}</button>
@stop

@section('meta-content')
    <form action="{{ route('admin.contacts.organizations.store') }}" method="post" @submit.prevent="onSubmit">
        <modal id="addOrganizationModal" :is-open="modalIds.addOrganizationModal">
            <h3 slot="header-title">{{ __('admin::app.contacts.organizations.add-title') }}</h3>
            
            <div slot="header-actions">
                {!! view_render_event('admin.contacts.organizations.create.form_buttons.before') !!}

                <button class="btn btn-sm btn-secondary-outline" @click="closeModal('addOrganizationModal')">{{ __('admin::app.contacts.organizations.cancel') }}</button>

                <button class="btn btn-sm btn-primary">{{ __('admin::app.contacts.organizations.save-btn-title') }}</button>

                {!! view_render_event('admin.contacts.organizations.create.form_buttons.after') !!}
            </div>

            <div slot="body">
                {!! view_render_event('admin.contacts.organizations.create.form_controls.before') !!}

                @csrf()
                
                <input type="hidden" name="quick_add" value="1"/>

                @include('admin::common.custom-attributes.edit', [
                    'customAttributes' => app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                        'entity_type' => 'organizations',
                        'quick_add'   => 1
                    ])
                ])

                {!! view_render_event('admin.contacts.organizations.create.form_controls.after') !!}
            </div>
        </modal>
    </form>
@stop
