
<x-admin::layouts>
    <!-- Page Title -->
    <x-slot:title>
        @lang('admin::app.contacts.organizations.edit.title')
    </x-slot>

    {!! view_render_event('admin.organizations.edit.form.before') !!}

    <x-admin::form
        :action="route('admin.contacts.organizations.update', $relatedContact->id)"
        method="PUT"
    >
        @php

            if (!function_exists('formatDateWithRemaining')) {
             function formatDateWithRemaining(?string $dateStr): string{
            if (empty($dateStr)) {
                return '';
            }

            try {
                $date = new DateTime($dateStr);
            } catch (Exception $e) {
                return '-';
            }

            if ((int) $date->format('Y') <= 2000) {
                return '-';
            }

            $now = new DateTime();

            // Format: "29 Jun 2025"
            $formattedDate = $date->format('d M Y');

            $interval = $now->diff($date);
            $diffDays = (int) $interval->format('%r%a'); // signed days

            if ($diffDays > 0) {
                $remainText = "(in {$diffDays} day" . ($diffDays > 1 ? 's' : '') . ")";
            } elseif ($diffDays === 0) {
                $remainText = "(today)";
            } else {
                $remainText = "(" . abs($diffDays) . " day" . (abs($diffDays) > 1 ? 's' : '') . " ago)";
            }

            return "{$formattedDate} {$remainText}";
        }
            }


                if(!empty($relatedContact->emails)){
                    $relatedContact->emails = implode(',',json_decode($relatedContact->emails, true));
                }
                 if(!empty($relatedContact->mobile_numbers)){
                    $relatedContact->mobile_numbers = implode(',',json_decode($relatedContact->mobile_numbers, true));
                }


        @endphp
        <div class="flex flex-col gap-4">
            <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                <div class="flex flex-col gap-2">
                    {!! view_render_event('admin.organizations.edit.breadcrumbs.before', ['relatedContact' => $relatedContact]) !!}

                    <x-admin::breadcrumbs
                        name="contacts.relatedContact.view"
                        :entity="$relatedContact"
                    />

                    {!! view_render_event('admin.organizations.edit.breadcrumbs.before', ['relatedContact' => $relatedContact]) !!}

                </div>

            </div>

            <div style="    max-width: 45vw;" class="box-shadow rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">

                <div class="p-2 m-2">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        disabled
                        value="{{$relatedContact->name}}"
                        class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                </div>

                <div class="p-2 m-2">
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                    <input
                        type="text"
                        id="type"
                        name="type"
                        disabled
                        value="{{$relatedContact->type}}"
                        class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                </div>
                <div class="p-2 m-2">
                    <label for="emails" class="block text-sm font-medium text-gray-700 mb-1">Emails</label>
                    <input
                        type="text"
                        id="emails"
                        name="emails"
                        disabled
                        value="{{$relatedContact->emails}}"
                        class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                </div>
                <div class="p-2 m-2">
                    <label for="mobile_numbers" class="block text-sm font-medium text-gray-700 mb-1">Contact Numbers</label>
                    <input
                        type="text"
                        id="mobile_numbers"
                        name="mobile_numbers"
                        disabled
                        value="{{$relatedContact->mobile_numbers}}"
                        class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                </div>
                @if(formatDateWithRemaining($relatedContact->eid_expiry)!=='-')
                <div class="p-2 m-2">
                    <label for="eid_expiry" class="block text-sm font-medium text-gray-700 mb-1">EID Expiry</label>
                    <input
                        type="text"
                        id="eid_expiry"
                        name="eid_expiry"
                        disabled
                        value="{{formatDateWithRemaining($relatedContact->eid_expiry)}}"
                        class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                </div>
                @endif
                @if(!empty($relatedContact->person_id))
                <div class="p-2 m-2">
                    <label for="company" class="block text-sm font-medium text-gray-700 mb-1">Action</label>
                    <a class="primary-button" href="{{route("admin.contacts.persons.view",[$relatedContact->person_id])}}">
                        View Company
                    </a>

                </div>
                @endif



            </div>
        </div>
    </x-admin::form>

    {!! view_render_event('admin.organizations.edit.form.after') !!}
</x-admin::layouts>
