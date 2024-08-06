{!! view_render_event('admin.contacts.persons.view.attributes.before', ['person' => $person]) !!}

<div class="flex w-full flex-col gap-4 border-b border-gray-200 p-4">
    <h4 class="font-semibold">
        @lang('admin::app.contacts.persons.view.about-person')
    </h4>

    <x-admin::attributes.view
        :custom-attributes="app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
            'entity_type' => 'persons',
            ['code', 'NOTIN', ['name', 'jon_title']]
        ])"
        :entity="$person"
    />
</div>

{!! view_render_event('admin.contacts.persons.view.attributes.before', ['person' => $person]) !!}