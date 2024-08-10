{!! view_render_event('admin.leads.view.person.before', ['warehouse' => $warehouse]) !!}

<div class="flex w-full flex-col gap-4 border-b border-gray-200 p-4">
    <h4 class="flex items-center justify-between font-semibold">
        @lang('admin::app.settings.warehouses.view.general-information.title')
    </h4>

    <!-- Contact Initials -->
    <x-admin::form
        v-slot="{ meta, errors, handleSubmit }"
        as="div"
        ref="modalForm"
    >
        <form @submit="handleSubmit($event, () => {})">
            <x-admin::attributes.view
                :custom-attributes="app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                    'entity_type' => 'warehouses',
                    ['code', 'NOTIN', ['contact_name', 'contact_emails', 'contact_numbers', 'contact_address']]
                ])"
                :allow-edit="true"
                :entity="$warehouse"
                :url="route('admin.settings.warehouses.update', $warehouse->id)"           
            />
        </form>
    </x-admin::form>
</div>

{!! view_render_event('admin.leads.view.person.after', ['warehouse' => $warehouse]) !!}