<div class="switch-icons-container">
    @if (request('view_type'))
        <a href="{{ route('admin.leads.index') }}" class="icon-container">
            <i class="icon layout-column-line-icon"></i>
        </a>

        <a class="icon-container active">
            <i class="icon table-line-active-icon"></i>
        </a>
    @else
        <a  class="icon-container active">
            <i class="icon layout-column-line-active-icon"></i>
        </a>

        <a href="{{ route('admin.leads.index', ['view_type' => 'table']) }}" class="icon-container">
            <i class="icon table-line-icon"></i>
        </a>
    @endif
</div>