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

                    <div class="flex gap-1">
                        <button class="icon-left-arrow rtl:icon-right-arrow rounded-md p-1 text-2xl transition-all hover:bg-gray-100"></button>
                        <button class="icon-right-arrow rtl:icon-right-arrow rounded-md p-1 text-2xl transition-all hover:bg-gray-100"></button>
                    </div>
                </div>

                <!-- Tags -->
                <x-admin::tags
                    :attach-endpoint="route('admin.persons.tags.attach', $person->id)"
                    :detach-endpoint="route('admin.persons.tags.detach', $person->id)"
                    :added-tags="$person->tags"
                />

                <!-- Title -->
                <h3 class="text-lg font-bold">
                    {{ $person->title }}
                </h1>

                <!-- Activity Actions -->
                <div class="flex flex-wrap gap-2">
                    <!-- Mail Activity Action -->
                    @include ('admin::contacts.persons.view.activities.actions.mail')

                    <!-- File Activity Action -->
                    @include ('admin::contacts.persons.view.activities.actions.file')

                    <!-- Note Activity Action -->
                    @include ('admin::contacts.persons.view.activities.actions.note')

                    <!-- Activity Action -->
                    @include ('admin::contacts.persons.view.activities.actions.activity')
                </div>
            </div>
            
            <!-- Person Attributes -->
            @include ('admin::contacts.persons.view.attributes')

            <!-- Contact Person -->
            @include ('admin::contacts.persons.view.person')
        </div>

        {!! view_render_event('admin.contact.persons.view.left.after', ['person' => $person]) !!}

        {!! view_render_event('admin.contact.persons.view.right.before', ['person' => $person]) !!}
        
        <!-- Right Panel -->
        <div class="flex w-full flex-col gap-4 rounded-lg">
            <!-- Stages Navigation -->
            @include ('admin::contacts.persons.view.activities.index')
        </div>

        {!! view_render_event('admin.contact.persons.view.right.after', ['person' => $person]) !!}
    </div>   

</x-admin::layouts>