
<x-admin::layouts>
    <!-- Page Title -->
    <x-slot:title>
        @lang('admin::app.contacts.relatedContact.create.title')
    </x-slot>

    {!! view_render_event('admin.relatedContact.create.form.before') !!}

    <x-admin::form
        :action="route('admin.contacts.related-contacts.store')"
        method="POST"
    >
        <input type="hidden" name="redirect" value="true">
        <div class="flex flex-col gap-4">
            <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                <div class="flex flex-col gap-2">
                    {!! view_render_event('admin.relatedContact.create.breadcrumbs.before') !!}

                    <!-- Breadcrumbs -->
                    <x-admin::breadcrumbs name="contacts.relatedContact.create" />

                    {!! view_render_event('admin.relatedContact.create.breadcrumbs.before') !!}

                    <div class="text-xl font-bold dark:text-gray-300">
                        @lang('admin::app.contacts.relatedContact.create.title')
                    </div>
                </div>

                <div class="flex items-center gap-x-2.5">
                    <div class="flex items-center gap-x-2.5">
                        {!! view_render_event('admin.relatedContact.create.save_buttons.before') !!}

                        <!-- Create button for person -->
                        <button
                            type="submit"
                            class="primary-button"
                        >
                            @lang('admin::app.contacts.relatedContact.create.save-btn')
                        </button>

                        {!! view_render_event('admin.relatedContact.create.save_buttons.before') !!}
                    </div>
                </div>
            </div>

            <div class="box-shadow rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                {!! view_render_event('admin.contacts.relatedContact.create.form_controls.before') !!}

                <div style="    max-width: 45vw;" class="box-shadow rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">

                    <div class="p-2 m-2">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            value=""
                            class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        />
                    </div>

                    <div class="p-2 m-2">
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                        <select name="type"                             class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Select Type --</option>
                            <option value="Manager" >Manager</option>
                            <option value="Partner" >Partner</option>
                            <option value="Family Visa" >Family Visa</option>
                            <option value="Local Agent" >Local Agent</option>
                        </select>
                    </div>

                    <!-- Emails Input -->
                    <div class="p-2 m-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Emails</label>
                        <div id="email-list" class="space-y-2"></div>

                        <div class="flex space-x-2 mt-2">
                            <input type="email" name="email" id="email-input" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" placeholder="Enter email" />
                            <button type="button" onclick="addEmail()" class="add-tag-btn flex h-9 w-9 items-center justify-center rounded-full bg-brandColor text-white">  <i class="icon-add text-2xl"></i></button>
                        </div>

                        <input type="hidden" name="emails" id="emails-hidden">
                    </div>

                    <!-- Mobile Numbers Input -->
                    <div class="p-2 m-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Contact Numbers</label>
                        <div id="mobile-list" class="space-y-2"></div>

                        <div class="flex space-x-2 mt-2">
                            <input type="text" name="mobile_number" id="mobile-input" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" value="+971" />
                            <button type="button" onclick="addMobile()" class="add-tag-btn flex h-9 w-9 items-center justify-center rounded-full bg-brandColor text-white">  <i class="icon-add text-2xl"></i></button>
                        </div>

                        <input type="hidden" name="mobile_numbers" id="mobiles-hidden">
                    </div>


                    <div class="p-2 m-2">
                            <label for="eid_expiry" class="block text-sm font-medium text-gray-700 mb-1">EID Expiry</label>
                            <input
                                type="date"
                                id="eid_expiry"
                                name="eid_expiry"
                                value=""
                                class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            />
                        </div>


                </div>

                {!! view_render_event('admin.contacts.relatedContact.edit.form_controls.after') !!}
            </div>
        </div>

        @pushOnce('scripts')
            <script>
                const emails = [];
                const mobiles = [];

                function addEmail() {
                    const input = document.getElementById('email-input');
                    const value = input.value.trim();

                    if (value && !emails.includes(value)) {
                        emails.push(value);
                        updateList('email-list', emails, 'emails');
                        input.value = '';
                    }
                }

                function addMobile() {
                    const input = document.getElementById('mobile-input');
                    const value = input.value.trim();

                    if (value && !mobiles.includes(value)) {
                        mobiles.push(value);
                        updateList('mobile-list', mobiles, 'mobile_numbers');
                        input.value = '+971';
                    }
                }

                function updateList(containerId, values, field) {
                    const container = document.getElementById(containerId);
                    container.innerHTML = values.map((v, i) => `
            <div class="flex justify-between items-center border p-2 rounded bg-gray-100">
                <span>${v}</span>
                <button type="button" onclick="removeItem('${field}', ${i})" class="text-red-600 font-bold">x</button>
            </div>
        `).join('');

                    document.getElementById(field === 'emails' ? 'emails-hidden' : 'mobiles-hidden').value = JSON.stringify(values);
                }

                function removeItem(field, index) {
                    if (field === 'emails') {
                        emails.splice(index, 1);
                        updateList('email-list', emails, 'emails');
                    } else {
                        mobiles.splice(index, 1);
                        updateList('mobile-list', mobiles, 'mobile_numbers');
                    }
                }
            </script>
        @endPushOnce




    </x-admin::form>

    {!! view_render_event('admin.relatedContact.create.form.after') !!}
</x-admin::layouts>
