@extends('ui::datagrid.table')

@section('page_title')
    {{ __('admin::app.contacts.persons.title') }}
@stop

@section('table-header')
    {!! view_render_event('admin.contacts.persons.index.persons.before') !!}

    {{ Breadcrumbs::render('contacts.persons') }}

    {{ __('admin::app.contacts.persons.title') }}

    {!! view_render_event('admin.contacts.persons.index.persons.after') !!}
@stop

@php
    $tableClass = "\Webkul\Admin\DataGrids\Contact\PersonDataGrid";
@endphp

@section('table-action')
    <button class="btn btn-md btn-primary" @click="openModal('addPersonModal')">{{ __('admin::app.contacts.persons.add-title') }}</button>
@stop

@section('meta-content')
    <form action="{{ route('admin.contacts.persons.store') }}" method="post" @submit.prevent="onSubmit" enctype="multipart/form-data">
        <modal id="addPersonModal" :is-open="modalIds.addPersonModal">
            <h3 slot="header-title">{{ __('admin::app.contacts.persons.add-title') }}</h3>
            
            <div slot="header-actions">
                {!! view_render_event('admin.contacts.persons.create.form_buttons.before') !!}

                <button class="btn btn-sm btn-secondary-outline" @click="closeModal('addPersonModal')">{{ __('admin::app.contacts.persons.cancel') }}</button>

                <button class="btn btn-sm btn-primary">{{ __('admin::app.contacts.persons.save-btn-title') }}</button>

                {!! view_render_event('admin.contacts.persons.create.form_buttons.after') !!}
            </div>

            <div slot="body">
                {!! view_render_event('admin.contacts.persons.create.form_controls.before') !!}

                @csrf()
                
                <input type="hidden" name="quick_add" value="1"/>

                @include('admin::common.custom-attributes.edit', [
                    'customAttributes' => app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                        'entity_type' => 'persons',
                        'quick_add'   => 1
                    ])
                ])

                {!! view_render_event('admin.contacts.persons.create.form_controls.after') !!}
            </div>
        </modal>
    </form>
@stop
