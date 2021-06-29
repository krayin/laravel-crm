@section('table-action')
    <button class="btn btn-md btn-primary" id="add-new" @click="openModal('addLeadModal')">
        {{ __('admin::app.leads.add-title') }}
    </button>
@stop

@section('table-section')
    <table-component table-class="{{ $tableClass }}" switch-page-url="{{ route('admin.leads.index') }}"><table-component>
@show