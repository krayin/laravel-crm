<x-admin::layouts>
    <x-slot:title>
        @lang ($warehouse->name)
    </x-slot>

    <div class="flex gap-4">
        <!-- Left Panel -->
        {!! view_render_event('admin.settings.warehouses.view.left.before', ['warehouse' => $warehouse]) !!}

        <div class="[&>div:last-child]:border-b-0 sticky top-[73px] flex min-w-[394px] max-w-[394px] flex-col self-start rounded-lg border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
            <!-- Product Information -->
            <div class="flex w-full flex-col gap-2 border-b border-gray-200 p-4 dark:border-gray-800">
                <!-- Breadcrums -->
                <div class="flex items-center justify-between">
                    <x-admin::breadcrumbs
                        name="settings.warehouses.view"
                        :entity="$warehouse"
                    />
                </div>

                {!! view_render_event('admin.settings.warehouses.view.left.tags.before', ['warehouse' => $warehouse]) !!}

                <!-- Tags -->
                <x-admin::tags
                    :attach-endpoint="route('admin.settings.warehouses.tags.attach', $warehouse->id)"
                    :detach-endpoint="route('admin.settings.warehouses.tags.detach', $warehouse->id)"
                    :added-tags="$warehouse->tags"
                />

                {!! view_render_event('admin.settings.warehouses.view.left.tags.after', ['warehouse' => $warehouse]) !!}

                {!! view_render_event('admin.settings.warehouses.view.left.title.before', ['warehouse' => $warehouse]) !!}

                <!-- Title -->
                <h3 class="text-lg font-bold dark:text-white">
                    {{ $warehouse->name }}
                </h3>

                {!! view_render_event('admin.settings.warehouses.view.left.title.after', ['warehouse' => $warehouse]) !!}

                {!! view_render_event('admin.settings.warehouses.view.left.actions.before', ['warehouse' => $warehouse]) !!}

                <!-- Activity Actions -->
                <div class="flex flex-wrap gap-2">
                    {!! view_render_event('admin.settings.warehouses.view.left.actions.file.before', ['warehouse' => $warehouse]) !!}

                    <!-- File Activity Action -->
                    <x-admin::activities.actions.file
                        :entity="$warehouse"
                        entity-control-name="warehouse_id"
                    />

                    {!! view_render_event('admin.settings.warehouses.view.left.actions.file.after', ['warehouse' => $warehouse]) !!}

                    {!! view_render_event('admin.settings.warehouses.view.left.actions.note.before', ['warehouse' => $warehouse]) !!}

                    <!-- Note Activity Action -->
                    <x-admin::activities.actions.note
                        :entity="$warehouse"
                        entity-control-name="warehouse_id"
                    />

                    {!! view_render_event('admin.settings.warehouses.view.left.actions.note.after', ['warehouse' => $warehouse]) !!}

                    {!! view_render_event('admin.settings.warehouses.view.left.actions.activity.before', ['warehouse' => $warehouse]) !!}

                    <!-- Activity Action -->
                    <x-admin::activities.actions.activity
                        :entity="$warehouse"
                        entity-control-name="warehouse_id"
                    />

                    {!! view_render_event('admin.settings.warehouses.view.left.actions.activity.after', ['warehouse' => $warehouse]) !!}
                </div>

                {!! view_render_event('admin.settings.warehouses.view.left.actions.after', ['warehouse' => $warehouse]) !!}
            </div>
            
            <!-- General Information -->
            @include ('admin::settings.warehouses.view.general-information')

            <!-- Contact Information -->
            @include ('admin::settings.warehouses.view.contact-information')
        </div>

        {!! view_render_event('admin.settings.warehouses.view.left.after', ['warehouse' => $warehouse]) !!}

        {!! view_render_event('admin.settings.warehouses.view.right.before', ['warehouse' => $warehouse]) !!}
        
        <!-- Right Panel -->
        <div class="flex w-full flex-col gap-4 rounded-lg">
            {!! view_render_event('admin.settings.warehouses.view.right.attributes.before', ['warehouse' => $warehouse]) !!}

            <!-- Activity Navigation -->
            <x-admin::activities
                :endpoint="route('admin.settings.warehouse.activities.index', $warehouse->id)" 
                :types="[
                    ['name' => 'all', 'label' => trans('admin::app.settings.warehouses.view.all')],
                    ['name' => 'note', 'label' => trans('admin::app.settings.warehouses.view.notes')],
                    ['name' => 'file', 'label' => trans('admin::app.settings.warehouses.view.files')],
                    ['name' => 'system', 'label' => trans('admin::app.settings.warehouses.view.change-logs')],
                ]"
                :extra-types="[
                    ['name' => 'location', 'label' => trans('admin::app.settings.warehouses.view.location')],
                ]"
            >
                <x-slot:location>
                    @include ('admin::settings.warehouses.view.locations')
                </x-slot>
            </x-admin::activities>

            {!! view_render_event('admin.settings.warehouses.view.right.attributes.after', ['warehouse' => $warehouse]) !!}
        </div>

        {!! view_render_event('admin.warehouse.view.right.after', ['warehouse' => $warehouse]) !!}
    </div>    
</x-admin::layouts>