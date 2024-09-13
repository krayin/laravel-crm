{!! view_render_event('admin.contacts.persons.view.attributes.before', ['person' => $person]) !!}

<div class="flex w-full flex-col gap-4 border-b border-gray-200 p-4 dark:border-gray-800">
    <h4 class="font-semibold dark:text-white">
        @lang('admin::app.contacts.persons.view.about-person')
    </h4>

    {!! view_render_event('admin.contacts.persons.view.attributes.form_controls.before', ['person' => $person]) !!}

    <x-admin::form
        v-slot="{ meta, errors, handleSubmit }"
        as="div"
        ref="modalForm"
    >
        <form @submit="handleSubmit($event, () => {})">
            {!! view_render_event('admin.contacts.persons.view.attributes.form_controls.attributes_view.before', ['person' => $person]) !!}

            <x-admin::attributes.view
                :custom-attributes="app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                    'entity_type' => 'persons',
                    ['code', 'NOTIN', ['name', 'jon_title']]
                ])"
                :entity="$person"
                :url="route('admin.contacts.persons.update', $person->id)"
                :allow-edit="true"
            />

            {!! view_render_event('admin.contacts.persons.view.attributes.form_controls.attributes_view.after', ['person' => $person]) !!}
        </form>
    </x-admin::form>

    {!! view_render_event('admin.contacts.persons.view.attributes.form_controls.after', ['person' => $person]) !!}
</div>

{!! view_render_event('admin.contacts.persons.view.attributes.before', ['person' => $person]) !!}
