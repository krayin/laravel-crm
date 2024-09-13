{!! view_render_event('admin.leads.view.person.before', ['warehouse' => $warehouse]) !!}

<div class="flex w-full flex-col gap-4 border-b border-gray-200 p-4 dark:border-gray-800">
    <h4 class="flex items-center justify-between font-semibold dark:text-white">
        @lang('admin::app.settings.warehouses.view.contact-information.title')
    </h4>

    <!-- General Initials -->
    <x-admin::form
        v-slot="{ meta, errors, handleSubmit }"
        as="div"
        ref="modalForm"
    >
        <form @submit="handleSubmit($event, () => {})">
            {!! view_render_event('admin.leads.view.person.attributes.view.before', ['warehouse' => $warehouse]) !!}

            <x-admin::attributes.view
                :custom-attributes="app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                    'entity_type' => 'warehouses',
                    ['code', 'IN', ['contact_name', 'contact_emails', 'contact_numbers', 'contact_address']]
                ])"
                :entity="$warehouse"
                :url="route('admin.settings.warehouses.update', $warehouse->id)"           
                :allow-edit="true"
            />

            {!! view_render_event('admin.leads.view.person.attributes.view.after', ['warehouse' => $warehouse]) !!}
        </form>
    </x-admin::form>
</div>

{!! view_render_event('admin.leads.view.person.after', ['warehouse' => $warehouse]) !!}