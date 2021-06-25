@section('css')
    <style>
        .table-header h1 {
            padding-bottom: 15px;
        }

        .modal-container .modal-header {
            border: 0;
        }

        .modal-container .modal-body {
            padding: 0;
        }
    </style>
@stop

@section('table-action')
    <button class="btn btn-md btn-primary" id="add-new" @click="openModal('addLeadModal')">
        {{ __('admin::app.leads.add-title') }}
    </button>

    <div class="float-right">
        <a class="icon-container" href="{{ route('admin.leads.index') }}">
            <i class="icon layout-column-line-icon"></i>
        </a>

        <a class="icon-container active">
            <i class="icon table-line-active-icon"></i>
        </a>
    </div>
@stop