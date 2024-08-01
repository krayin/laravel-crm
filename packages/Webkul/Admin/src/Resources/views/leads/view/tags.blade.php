{!! view_render_event('admin.leads.view.tags.before', ['lead' => $lead]) !!}

<div class="flex items-center gap-1">
    <span class="rounded-md bg-rose-100 px-3 py-1.5 text-xs font-medium text-rose-700">Urgent</span>
    <span class="rounded-md bg-sky-100 px-3 py-1.5 text-xs font-medium text-sky-600">Hot Lead</span>

    <!-- Add Button -->
    <button class="icon-add rounded-md p-1 text-xl transition-all hover:bg-gray-200"></button>
</div>

{!! view_render_event('admin.leads.view.tags.after', ['lead' => $lead]) !!}