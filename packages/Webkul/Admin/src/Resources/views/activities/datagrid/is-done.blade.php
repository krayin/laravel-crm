<input
    type="checkbox"
    name="is_done"
    value="{{ $row->is_done ? '0' : '1' }}"
    {{ $row->is_done ? 'checked' : '' }}
    onchange="updateStatus(event, '{{ route('admin.activities.update', $row->id) }}')"
>
