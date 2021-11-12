<div class="form-group">
    <span class="radio" style="margin: 0">
        <input
            type="checkbox"
            name="is_done"
            id="{{ 'is_done_' . $row->id }}"
            value="{{ $row->is_done ? '0' : '1' }}"
            {{ $row->is_done ? 'checked' : '' }}
            onchange="updateStatus(event, '{{ route('admin.activities.update', $row->id) }}')"
        >
        <label for="{{ 'is_done_' . $row->id }}" class="radio-view"></label>
    </span>
</div>