@extends('ui::datagrid.table')

@section('table-header')
    {{ __('admin::app.contacts.persons.title') }}
@stop

@php
    $tableClass = "\Webkul\Admin\DataGrids\Contact\PersonDataGrid";
@endphp

@section('table-action')
    <button class="btn btn-md btn-primary" @click="openModal('addPersonModal')">{{ __('admin::app.contacts.persons.add-title') }}</button>
@stop

@section('meta-content')
    <form action="{{ route('admin.contacts.persons.store') }}" method="post" @submit.prevent="onSubmit">
        <modal id="addPersonModal" :is-open="modalIds.addPersonModal">
            <h3 slot="header-title">{{ __('admin::app.contacts.persons.add-title') }}</h3>
            
            <div slot="header-actions">
                <button class="btn btn-sm btn-secondary-outline" @click="closeModal('addPersonModal')">{{ __('admin::app.contacts.persons.cancel') }}</button>

                <button class="btn btn-sm btn-primary">{{ __('admin::app.contacts.persons.save-btn-title') }}</button>
            </div>

            <div slot="body">
                @csrf()
                
                <input type="hidden" name="quick_add" value="1"/>

                @include('admin::common.custom-attributes.edit', [
                    'customAttributes' => app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                        'entity_type' => 'persons',
                        'quick_add'   => 1
                    ])
                ])
            </div>
        </modal>
    </form>
@stop
