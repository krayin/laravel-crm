@extends('ui::datagrid.table')

@section('page_title')
    {{ __('admin::app.products.title') }}
@stop

@section('table-header')
    {!! view_render_event('admin.products.index.header.before') !!}

    {{ Breadcrumbs::render('products') }}

    {{ __('admin::app.products.title') }}

    {!! view_render_event('admin.products.index.header.after') !!}
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
                {!! view_render_event('admin.products.create.form_buttons.before') !!}

                <button class="btn btn-sm btn-secondary-outline" @click="closeModal('addProductModal')">{{ __('admin::app.products.cancel') }}</button>

                <button type="submit" class="btn btn-sm btn-primary">{{ __('admin::app.products.save-btn-title') }}</button>

                {!! view_render_event('admin.products.create.form_buttons.after') !!}
            </div>

            <div slot="body">
                {!! view_render_event('admin.products.create.form_controls.before') !!}

                @csrf()
                
                <input type="hidden" name="quick_add" value="1"/>

                @include('admin::common.custom-attributes.edit', [
                    'customAttributes' => app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                        'entity_type' => 'products',
                        'quick_add'   => 1
                    ])
                ])

                {!! view_render_event('admin.products.create.form_controls.after') !!}
            </div>
        </modal>
    </form>
@stop