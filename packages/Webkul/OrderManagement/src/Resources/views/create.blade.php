@extends('order_management::layouts.master')

@section('page_title')
Create Order
@endsection

@section('content')

{{-- create a 2 button  --}}

<div class="flex items-center gap-x-2.5">
    <div class="flex items-center gap-x-2.5">
        {!! view_render_event('admin.orders.create.save_button.before') !!}

        <!-- Create button for Order -->
        @if (bouncer()->hasPermission('order_management.orders.create'))
            <button
                type="submit"
                class="primary-button"
            >
                Create Order
            </button>
        @endif

        {!! view_render_event('admin.orders.create.save_button.after') !!}
    </div>
</div>


@endsection


@push('styles')

@endpush
