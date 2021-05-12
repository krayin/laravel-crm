@extends('ui::datagrid.table')

@section('table-header')
    {{ __('admin::app.products.title') }}
@stop

@php
    $tableClass = "\Webkul\Admin\DataGrids\Product\ProductDataGrid";
@endphp

@section('table-action')
    <button class="btn btn-md btn-primary" @click="openModal('addProductModal')">{{ __('admin::app.products.add-title') }}</button>
@stop

@section('meta-content')
    <form action="{{ route('admin.products.store') }}" method="POST" @submit.prevent="onSubmit">
        <modal id="addProductModal" :is-open="modalIds.addProductModal">
            <h3 slot="header-title">{{ __('admin::app.products.add-title') }}</h3>
            
            <div slot="header-actions">
                <button class="btn btn-sm btn-secondary-outline" @click="closeModal('addProductModal')">{{ __('admin::app.products.cancel') }}</button>

                <button type="submit" class="btn btn-sm btn-primary">{{ __('admin::app.products.save-btn-title') }}</button>
            </div>

            <div slot="body">
                @csrf()
                
                <input type="hidden" name="quick_add" value="1"/>

                @include('admin::common.custom-attribute-controls', [
                    'customAttributes' => app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                        'entity_type' => 'products',
                        'quick_add'   => 1
                    ])
                ])
            </div>
        </modal>
    </form>
@stop