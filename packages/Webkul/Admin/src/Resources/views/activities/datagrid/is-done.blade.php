<label for="is_done_{{ $row->id }}">
    <input
        name="is_done"
        type="checkbox"
        id="is_done_{{ $row->id }}"
        value="1"
        {{ $row->is_done ? 'checked' : '' }}
        onchange="updateStatus(event, '{{ route('admin.activities.update', $row->id) }}')"

        class="peer hidden"
    >
    <span class='icon-checkbox-outline peer-checked:icon-checkbox-select cursor-pointer rounded-md text-2xl peer-checked:text-brandColor'></span>
</label>