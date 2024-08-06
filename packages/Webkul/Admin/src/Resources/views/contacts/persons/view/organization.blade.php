{!! view_render_event('admin.contacts.persons.view.organization.before', ['person' => $person]) !!}

<div class="flex w-full flex-col gap-4 border-b border-gray-200 p-4">
    <h4 class="flex items-center justify-between font-semibold">
        About Organization

        <button class="icon-edit rounded-md p-1 text-2xl transition-all hover:bg-gray-100"></button>
    </h4>

    <div class="flex gap-2">
        <!-- Organization Initials -->
        <div class="flex h-9 w-9 items-center justify-center rounded-full bg-green-200 text-xs font-medium">
            {{ collect(explode(' ', $person->organization->name))->map(fn($word) => strtoupper(substr($word, 0, 1)))->implode('') }}
        </div>

        <!-- Organization Details -->
        <div class="flex flex-col gap-1">
            <span class="font-semibold text-brandColor">
                {{ $person->organization->name }}
            </span>

            @if ($person->organization->address)
                <div class="flex flex-col gap-0.5">
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

{!! view_render_event('admin.contacts.persons.view.organization.after', ['person' => $person]) !!}