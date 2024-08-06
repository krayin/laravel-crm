{!! view_render_event('admin.contacts.persons.view.person.before', ['person' => $person]) !!}

<div class="flex w-full flex-col gap-4 border-b border-gray-200 p-4">
    <h4 class="flex items-center justify-between font-semibold">
        About Contact Person

        <button class="icon-edit rounded-md p-1 text-2xl transition-all hover:bg-gray-100"></button>
    </h4>

    <div class="flex gap-2">
        <!-- Person Initials -->
        <div class="flex h-9 w-9 items-center justify-center rounded-full bg-green-200 text-xs font-medium">
            {{ collect(explode(' ', $person->name))->map(fn($word) => strtoupper(substr($word, 0, 1)))->implode('') }}
        </div>

        <!-- Person Details -->
        <div class="flex flex-col gap-1">
            <span class="font-semibold text-brandColor">
                {{ $person->name }}
            </span>

            <span class="">
                CTO at Bagisto
            </span>
            
            @foreach ($person->emails as $email)
                <div class="flex gap-1">
                    <span class="text-brandColor">
                        {{ $email['value'] }}
                    </span>

                    <span class="text-gray-500">
                        ({{ $email['label'] }})
                    </span>
                </div>
            @endforeach
            
            @foreach ($person->contact_numbers as $contactNumber)
                <div class="flex gap-1">
                    <span class="text-brandColor">
                        {{ $contactNumber['value'] }}
                    </span>

                    <span class="text-gray-500">
                        ({{ $contactNumber['label'] }})
                    </span>
                </div>
            @endforeach
        </div>
    </div>
</div>

{!! view_render_event('admin.contacts.persons.view.person.after', ['person' => $person]) !!}