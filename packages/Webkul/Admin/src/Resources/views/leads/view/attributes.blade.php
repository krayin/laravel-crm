{!! view_render_event('admin.leads.view.attributes.before', ['lead' => $lead]) !!}

<div class="flex w-full flex-col gap-4 border-b border-gray-200 p-4">
    <h4 class="font-semibold">About Lead</h4>

    <x-admin::attributes.view
        :custom-attributes="app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
            'entity_type' => 'leads',
            ['code', 'NOTIN', ['title', 'description']]
        ])"
        :entity="$lead"
    />
</div>

{!! view_render_event('admin.leads.view.attributes.before', ['lead' => $lead]) !!}