{!! view_render_event('admin.leads.view.attributes.before', ['lead' => $lead]) !!}

<div class="dark: flex w-full flex-col gap-4 border-b border-gray-200 p-4 dark:border-gray-800">
    <h4 class="flex items-center justify-between font-semibold dark:text-white">
        @lang('admin::app.leads.view.attributes.title')

        @if (bouncer()->hasPermission('leads.edit'))
            <a
                href="{{ route('admin.leads.edit', $lead->id) }}"
                class="icon-edit rounded-md p-1 text-2xl transition-all hover:bg-gray-100 dark:hover:bg-gray-950"
                target="_blank"
            ></a>
        @endif
    </h4>

    <x-admin::form
        v-slot="{ meta, errors, handleSubmit }"
        as="div"
        ref="modalForm"
    >
        <form @submit="handleSubmit($event, () => {})">
            <x-admin::attributes.view
                :custom-attributes="app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                    'entity_type' => 'leads',
                    ['code', 'NOTIN', ['title', 'description', 'lead_pipeline_id', 'lead_pipeline_stage_id']]
                ])"
                :entity="$lead"
                :url="route('admin.leads.attributes.update', $lead->id)"
                :allow-edit="true"
            />
        </form>
    </x-admin::form>
</div>

{!! view_render_event('admin.leads.view.attributes.before', ['lead' => $lead]) !!}
