{!! view_render_event('admin.contacts.organization.view.attributes.before', ['organization' => $organization]) !!}

<div class="flex w-full flex-col gap-4 border-b border-gray-200 p-4 dark:border-gray-800">
    <h4 class="font-semibold dark:text-white">
        @lang('admin::app.contacts.persons.view.about-organization')
    </h4>

    {!! view_render_event('admin.contacts.organization.view.attributes.form_controls.before', ['organization' => $organization]) !!}

    <x-admin::form
        v-slot="{ meta, errors, handleSubmit }"
        as="div"
        ref="modalForm"
    >
        <form @submit="handleSubmit($event, () => {})">
            {!! view_render_event('admin.contacts.organization.view.attributes.form_controls.attributes_view.before', ['organization' => $organization]) !!}

            <x-admin::attributes.view
                :custom-attributes="app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                    'entity_type' => 'organizations',
                    ['code', 'NOTIN', ['name', 'jon_title']]
                ])"
                :entity="$organization"
                :url="route('admin.contacts.organizations.update', $organization->id)"
                :allow-edit="true"
            />

            {!! view_render_event('admin.contacts.organization.view.attributes.form_controls.attributes_view.after', ['organization' => $organization]) !!}
        </form>
    </x-admin::form>

    {!! view_render_event('admin.contacts.organization.view.attributes.form_controls.after', ['organization' => $organization]) !!}
</div>

{!! view_render_event('admin.contacts.organization.view.attributes.before', ['organization' => $organization]) !!}
