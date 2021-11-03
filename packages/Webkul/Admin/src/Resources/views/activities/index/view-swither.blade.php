<div class="switch-view-container">
    @if (request('view_type'))
        <a href="{{ route('admin.activities.index') }}" class="icon-container">
            <i class="icon table-line-icon"></i>
        </a>

        <a  class="icon-container active">
            <i class="icon calendar-line-active-icon"></i>
        </a>
    @else
        <a class="icon-container active">
            <i class="icon table-line-active-icon"></i>
        </a>

        <a href="{{ route('admin.activities.index', ['view_type' => 'calendar']) }}" class="icon-container">
            <i class="icon calendar-line-icon"></i>
        </a>
    @endif
</div>