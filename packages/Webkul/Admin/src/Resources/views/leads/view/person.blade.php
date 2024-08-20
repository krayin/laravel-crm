{!! view_render_event('admin.leads.view.person.before', ['lead' => $lead]) !!}

<div class="flex w-full flex-col gap-4 border-b border-gray-200 p-4 dark:border-gray-800">
    <h4 class="flex items-center justify-between font-semibold dark:text-white">
        @lang('admin::app.leads.view.persons.title')

        @if (bouncer()->hasPermission('contacts.persons.edit'))
            <a
                href="{{ route('admin.contacts.persons.edit', $lead->person->id) }}"
                class="icon-edit rounded-md p-1 text-2xl transition-all hover:bg-gray-100 dark:hover:bg-gray-950"
                target="_blank"
            ></a>
        @endif
    </h4>

    <div class="flex gap-2">
        <!-- Person Initials -->
        <div class="flex h-9 w-9 items-center justify-center rounded-full bg-green-200 text-xs font-medium">
            {{ collect(explode(' ', $lead->person->name))->map(fn($word) => strtoupper(substr($word, 0, 1)))->implode('') }}
        </div>

        <!-- Person Details -->
        <div class="flex flex-col gap-1">
            <a
                href="{{ route('admin.contacts.persons.view', $lead->person->id) }}"
                class="font-semibold text-brandColor"
                target="_blank"
            >
                {{ $lead->person->name }}
            </a>

            @if ($lead->person->job_title)
                <span class="dark:text-white">
                    @if ($lead->person->organization)
                        @lang('admin::app.leads.view.persons.job-title', [
                            'job_title'    => $lead->person->job_title,
                            'organization' => $lead->person->organization->name
                        ])
                    @else
                        {{ $lead->person->job_title }}
                    @endif
                </span>
            @endif
            
            @foreach ($lead->person->emails as $email)
                <div class="flex gap-1">
                    <span class="text-brandColor">
                        {{ $email['value'] }}
                    </span>

                    <span class="text-gray-500 dark:text-gray-300">
                        ({{ $email['label'] }})
                    </span>
                </div>
            @endforeach
            
            @foreach ($lead->person->contact_numbers as $contactNumber)
                <div class="flex gap-1">
                    <span class="text-brandColor">
                        {{ $contactNumber['value'] }}
                    </span>

                    <span class="text-gray-500 dark:text-gray-300">
                        ({{ $contactNumber['label'] }})
                    </span>
                </div>
            @endforeach
        </div>
    </div>
</div>

{!! view_render_event('admin.leads.view.person.after', ['lead' => $lead]) !!}