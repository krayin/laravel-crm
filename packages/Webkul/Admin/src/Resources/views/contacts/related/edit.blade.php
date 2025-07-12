
<x-admin::layouts>
    <!-- Page Title -->
    <x-slot:title>
        @lang('admin::app.contacts.relatedContact.edit.title')
    </x-slot>

    @php

    $types=[
        ['val'=>'','label'=>'-- Select Type --'],
        ['val'=>'Manager','label'=>'Manager'],
        ['val'=>'Partner','label'=>'Partner'],
        ['val'=>'Family Visa','label'=>'Family Visa'],
        ['val'=>'Local Agent','label'=>'Local Agent'],
    ];



@endphp
    <x-admin::form
        :action="route('admin.contacts.related-contacts.update', $relatedContact->id)"
        method="PUT"
    id="related-contact-form"
    >

        <input type="hidden" name="redirect" value="true">
        <div class="flex flex-col gap-4">
            <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                <div class="flex flex-col gap-2">
                    {!! view_render_event('admin.relatedContact.edit.breadcrumbs.before') !!}

                    <!-- Breadcrumbs -->
                    <x-admin::breadcrumbs name="contacts.relatedContact.view" :entity="$relatedContact" />

                    {!! view_render_event('admin.relatedContact.edit.breadcrumbs.before') !!}

                    <div class="text-xl font-bold dark:text-gray-300">
                        @lang('admin::app.contacts.relatedContact.edit.title')
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
                            value="{{$relatedContact->name ?? ''}}"
                            class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        />
                    </div>

                    <div class="p-2 m-2">
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                        <select name="type" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                         @foreach($types as $type)
                           <option {{  $type['val'] === $relatedContact->type ? 'selected' : '' }}   value="{{$type['val']}}">{{$type['label']}}</option>
                         @endforeach
                        </select>
                    </div>

                    <!-- Emails Input -->
                    <div class="p-2 m-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Emails</label>
                        <div id="email-list" class="space-y-2">

                        </div>

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
                function showToast(message, type = 'success', duration = 3000) {
                    const container = document.getElementById('toast-container');
                    if (!container) return;

                    const toast = document.createElement('div');
                    toast.textContent = message;
                    toast.style.padding = '12px 20px';
                    toast.style.marginTop = '10px';
                    toast.style.borderRadius = '5px';
                    toast.style.color = '#fff';
                    toast.style.minWidth = '200px';
                    toast.style.boxShadow = '0 2px 8px rgba(0,0,0,0.2)';
                    toast.style.fontWeight = '600';
                    toast.style.opacity = '0.9';
                    toast.style.transition = 'opacity 0.3s ease';

                    if (type === 'success') {
                        toast.style.backgroundColor = '#22c55e';  // Tailwind green-500
                    } else if (type === 'error') {
                        toast.style.backgroundColor = '#ef4444';  // Tailwind red-500
                    } else {
                        toast.style.backgroundColor = '#374151';  // Tailwind gray-700
                    }

                    container.appendChild(toast);

                    setTimeout(() => {
                        toast.style.opacity = '0';
                        setTimeout(() => container.removeChild(toast), 300);
                    }, duration);
                }


                const emails = {!! $relatedContact->emails ?? '[]' !!};
                const mobiles = {!! $relatedContact->mobile_numbers ?? '[]' !!};

                function addEmail() {
                    const input = document.getElementById('email-input');
                    const value = input.value.trim();

                    if (value && !emails.includes(value)) {
                        emails.push(value);
                        updateList('email-list', emails, 'emails');
                        input.value = '';
                        updateRelatedContact()
                    }
                }

                function addMobile() {
                    const input = document.getElementById('mobile-input');
                    const value = input.value.trim();

                    if (value && !mobiles.includes(value)) {
                        mobiles.push(value);
                        updateList('mobile-list', mobiles, 'mobile_numbers');
                        input.value = '+971';
                        updateRelatedContact()
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
                    updateRelatedContact();
                }


                function updateRelatedContact() {

                    const form = document.querySelector('#related-contact-form');
                    const formData = new FormData(form);
                    const contact = {};

                    // Convert FormData entries to a plain object
                    formData.forEach((value, key) => {
                        if (contact[key]) {
                            // Convert to array if key already exists (for multi-input fields)
                            if (!Array.isArray(contact[key])) {
                                contact[key] = [contact[key]];
                            }
                            contact[key].push(value);
                        } else {
                            contact[key] = value;
                        }
                    });

                    contact["redirect"]=false;

                    fetch(`{{route('admin.contacts.related-contacts.update', $relatedContact->id)}}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                        },
                        body: JSON.stringify(contact)
                    })
                        .then(res => res.json())
                        .then(data => {

                            showToast(data.message);
                        })
                        .catch(err => {
                            console.error('Update failed', err)
                            showToast('Update failed', 'error');
                        });
                }


                document.addEventListener('DOMContentLoaded', function() {
                    updateList('mobile-list', mobiles, 'mobile_numbers');
                    updateList('email-list', emails, 'emails');


                });
            </script>
        @endPushOnce
    </x-admin::form>
    <div id="toast-container" style="position: fixed; top: 1rem; right: 1rem; z-index: 9999;"></div>

</x-admin::layouts>
