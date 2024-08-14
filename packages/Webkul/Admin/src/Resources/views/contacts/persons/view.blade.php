<x-admin::layouts>
    <x-slot:title>
        @lang('admin::app.contacts.persons.view.title', ['name' => $person->name])
    </x-slot>

    <!-- Content -->
    <div class="flex gap-4">
        <!-- Left Panel -->
        {!! view_render_event('admin.contact.persons.view.left.before', ['person' => $person]) !!}

        <div class="flex min-w-[394px] max-w-[394px] flex-col self-start rounded-lg border border-gray-200 bg-white">
            <!-- Person Information -->
            <div class="flex w-full flex-col gap-2 border-b border-gray-200 p-4">
                <!-- Breadcrums -->
                <div class="flex items-center justify-between">
                    <x-admin::breadcrumbs name="contacts.persons" />
                </div>

                <!-- Tags -->
                <x-admin::tags
                    :attach-endpoint="route('admin.persons.tags.attach', $person->id)"
                    :detach-endpoint="route('admin.persons.tags.detach', $person->id)"
                    :added-tags="$person->tags"
                />

                <!-- Title -->
                <div class="mb-4 flex flex-col gap-0.5">
                    <h3 class="text-lg font-bold">
                        {{ $person->name }}
                    </h3>

                    <p>
                        {{ $person->job_title }}
                    </p>
                </div>

                <!-- Activity Actions -->
                <div class="flex flex-wrap gap-2">
                    <!-- Mail Activity Action -->
                    <x-admin::activities.actions.mail
                        :entity="$person"
                        entity-control-name="person_id"
                    />

                    <!-- File Activity Action -->
                    <x-admin::activities.actions.file
                        :entity="$person"
                        entity-control-name="person_id"
                    />

                    <!-- Note Activity Action -->
                    <x-admin::activities.actions.note
                        :entity="$person"
                        entity-control-name="person_id"
                    />

                    <!-- Activity Action -->
                    <x-admin::activities.actions.activity
                        :entity="$person"
                        entity-control-name="person_id"
                    />
                </div>
            </div>
            
            <!-- Person Attributes -->
            @include ('admin::contacts.persons.view.attributes')

            <!-- Contact Organization -->
            @include ('admin::contacts.persons.view.organization')
        </div>

        {!! view_render_event('admin.contact.persons.view.left.after', ['person' => $person]) !!}

        {!! view_render_event('admin.contact.persons.view.right.before', ['person' => $person]) !!}
        
        <!-- Right Panel -->
        <div class="flex w-full flex-col gap-4 rounded-lg">
            <!-- Stages Navigation -->
            <x-admin::activities :endpoint="route('admin.persons.activities.index', $person->id)" />
        </div>

        {!! view_render_event('admin.contact.persons.view.right.after', ['person' => $person]) !!}
    </div>
</x-admin::layouts>