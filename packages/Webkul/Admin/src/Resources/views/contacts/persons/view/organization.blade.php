{!! view_render_event('admin.contacts.persons.view.organization.before', ['person' => $person]) !!}

@if ($person?->organization)
    <div class="flex w-full flex-col gap-4 border-b border-gray-200 p-4 dark:border-gray-800">
        <h4 class="flex items-center justify-between font-semibold dark:text-white">
            About Organization

            <button class="icon-edit rounded-md p-1 text-2xl transition-all hover:bg-gray-100 dark:hover:bg-gray-950"></button>
        </h4>

        <div class="flex gap-2">
            <!-- Organization Initials -->
            <x-admin::avatar :name="$person->organization->name" />

            <!-- Organization Details -->
            <div class="flex flex-col gap-1">
                <span class="font-semibold text-brandColor">
                    {{ $person->organization->name }}
                </span>

                @if ($person->organization->address)
                    <div class="flex flex-col gap-0.5 dark:text-white">
                        <span>
                            {{ $person->organization->address['address'] }}
                        </span>
                        
                        <span>
                            {{ $person->organization->address['postcode'] . '  ' . $person->organization->address['city'] }}
                        </span>

                        <span>
                            {{ core()->state_name($person->organization->address['state']) }}
                        </span>

                        <span>
                            {{ core()->country_name($person->organization->address['country']) }}
                        </span>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endif
{!! view_render_event('admin.contacts.persons.view.organization.after', ['person' => $person]) !!}