@section('table-action')
    <a href="{{ route('admin.leads.create') }}" class="btn btn-md btn-primary">{{ __('admin::app.leads.create-title') }}</a>
@stop

@section('table-section')
    <table-component table-class="{{ $tableClass }}" switch-page-url="{{ route('admin.leads.index') }}"><table-component>
@show