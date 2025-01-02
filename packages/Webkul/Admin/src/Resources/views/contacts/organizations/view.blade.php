<x-admin::layouts>
    <x-slot:title>
        @lang('admin::app.contacts.organizations.view.title', ['name' => $organization->name])
    </x-slot>

    <!-- Content -->
    <div class="flex gap-4">
        <!-- Left Panel -->
        {!! view_render_event('admin.contact.organizations.view.left.before', ['organization' => $organization]) !!}

        <div class="[&>div:last-child]:border-b-0 sticky top-[73px] flex min-w-[394px] max-w-[394px] flex-col self-start rounded-lg border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
            <!-- organization Information -->
            <div class="flex w-full flex-col gap-2 border-b border-gray-200 p-4 dark:border-gray-800">
                <!-- Breadcrumbs -->
                <div class="flex items-center justify-between">
                    <x-admin::breadcrumbs
                        name="contacts.organizations.view"
                        :entity="$organization"
                    />
                </div>

                <!-- Title -->
                <div class="mb-4 flex flex-col gap-0.5">
                    {!! view_render_event('admin.contact.organizations.view.title.before', ['organization' => $organization]) !!}

                    <h3 class="text-lg font-bold dark:text-white">
                        {{ $organization->name }}
                    </h3>

                    <p class="dark:text-white">
                        {{ $organization->job_title }}
                    </p>

                    {!! view_render_event('admin.contact.organizations.view.title.after', ['organization' => $organization]) !!}
                </div>
                
                <!-- Activity Actions -->
                <div class="flex flex-wrap gap-2">
                    {!! view_render_event('admin.contact.organizations.view.actions.before', ['organization' => $organization]) !!}

                    <!-- Mail Activity Action -->
                    <x-admin::activities.actions.mail
                        :entity="$organization"
                        entity-control-name="organization_id"
                    />

                    <!-- File Activity Action -->
                    <x-admin::activities.actions.file
                        :entity="$organization"
                        entity-control-name="organization_id"
                    />

                    <!-- Note Activity Action -->
                    <x-admin::activities.actions.note
                        :entity="$organization"
                        entity-control-name="organization_id"
                    />

                    <!-- Activity Action -->
                    <x-admin::activities.actions.activity
                        :entity="$organization"
                        entity-control-name="organization_id"
                    />

                    {!! view_render_event('admin.contact.organizations.view.actions.after', ['organization' => $organization]) !!}
                </div>
            </div>

            <!-- organization Attributes -->
            @include ('admin::contacts.organizations.view.attributes')
        </div>

        {!! view_render_event('admin.contact.organizations.view.left.after', ['organization' => $organization]) !!}

        <!-- Right Panel -->
        <div class="flex w-full flex-col gap-4 rounded-lg">
            {!! view_render_event('admin.contact.organizations.view.right.before', ['organization' => $organization]) !!}

            <!-- Stages Navigation -->
            <x-admin::activities :endpoint="route('admin.contacts.organizations.activities.index', $organization->id)" />

            {!! view_render_event('admin.contact.organizations.view.right.after', ['organization' => $organization]) !!}
        </div>
    </div>
</x-admin::layouts>
