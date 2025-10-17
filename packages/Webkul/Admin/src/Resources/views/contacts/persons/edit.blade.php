<x-admin::layouts>
    <!-- Page Title -->
    <x-slot:title>
        @lang('admin::app.contacts.persons.edit.title')
    </x-slot>

    {!! view_render_event('admin.persons.edit.form.before') !!}

    <x-admin::form
        :action="route('admin.contacts.persons.update', $person->crm)"
        method="PUT"
        enctype="multipart/form-data"
    >
        <div class="flex flex-col gap-4">
            <div
                class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                <div class="flex flex-col gap-2">
                    {!! view_render_event('admin.persons.edit.breadcrumbs.before') !!}

                    <x-admin::breadcrumbs
                        name="contacts.persons.edit"
                        :entity="$person"
                    />

                    {!! view_render_event('admin.persons.edit.breadcrumbs.after') !!}

                    <div class="text-xl font-bold dark:text-white">
                        @lang('admin::app.contacts.persons.edit.title')
                    </div>
                </div>

                <div class="flex items-center gap-x-2.5">
                    <!--  Save button for Person -->
                    <div class="flex items-center gap-x-2.5">
                        {!! view_render_event('admin.persons.edit.save_button.before') !!}

                        <button
                            type="submit"
                            class="primary-button"
                        >
                            @lang('admin::app.contacts.persons.edit.save-btn')
                        </button>

                        {!! view_render_event('admin.persons.edit.save_button.after') !!}
                    </div>
                </div>
            </div>


            <div
                class="box-shadow rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                {!! view_render_event('admin.contacts.persons.edit.form_controls.before') !!}

                @php
                    $attributes = app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                    'entity_type' => 'persons',
                ]);

                 $should_remove_fields=[];

                foreach ($attributes as $attribute){
                    if($attribute->code==='person_type'){

                           $attributeValues = app('Webkul\Attribute\Repositories\AttributeValueRepository')->findWhere([
                                    'entity_type' => 'persons',
                                    'attribute_id'=>$attribute->id
                                ])->first();


                            switch($attributeValues->integer_value){
                                case 1:

                                break;
                                 case 2:

                                $should_remove_fields=['company_name_en','company_name_ar','license_no',
                                'company_issue_date','company_expiry_date','partner_2','partner_3','local_agent'
                                ];

                                break;
                                 case 3:
                 $should_remove_fields=['company_name_en','company_name_ar','license_no',
                                'company_issue_date','company_expiry_date','partner_2','partner_3','local_agent'
                                ];

                                break;

                            }

                    }
                }


                array_push($should_remove_fields,'rate');

                $rate=0;

                foreach($attributes as $attribute){
                        if($attribute->code === 'rate'){

                            $attributeValues = app('Webkul\Attribute\Repositories\AttributeValueRepository')->findWhere([
                                    'entity_type' => 'persons',
                                    'entity_id' => $person->id,
                                    'attribute_id' => $attribute->id,
                            ]);

                            if ($attributeValues->isEmpty()) {
                                // It's empty – no attribute values found
                            } else {
                                $rate = $attributeValues[0]->text_value;
                            }


                    }

                }



                //dump($attributeValues);

                $attributes = $attributes->reject(function ($attribute) use ($should_remove_fields) {
                    return in_array($attribute->code, $should_remove_fields);
                });

                $rate = intval($rate);

                @endphp

                <div class="mb-4 mb-2.5 w-full" style="display: inline-table">

                    <div class="person-rate">


                        <input type="radio" <?php echo $rate == 5 ? "checked" : ""; ?> id="star5" name="rate"
                               value="5"/>
                        <label for="star5" title="text">5 stars</label>
                        <input type="radio" <?php echo $rate == 4 ? "checked" : ""; ?> id="star4" name="rate"
                               value="4"/>
                        <label for="star4" title="text">4 stars</label>
                        <input type="radio" <?php echo $rate == 3 ? "checked" : ""; ?> id="star3" name="rate"
                               value="3"/>
                        <label for="star3" title="text">3 stars</label>
                        <input type="radio" <?php echo $rate == 2 ? "checked" : ""; ?> id="star2" name="rate"
                               value="2"/>
                        <label for="star2" title="text">2 stars</label>
                        <input type="radio" <?php echo $rate == 1 ? "checked" : ""; ?> id="star1" name="rate"
                               value="1"/>
                        <label for="star1" title="text">1 star</label>


                        <input type="radio" {{ $rate == -1 ? 'checked' : '' }} id="star0" name="rate" value="-1" />
                        <label for="star0" class="no-rating icon" title="No Rating">
                            <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 122.871 122.879" enable-background="new 0 0 122.871 122.879" xml:space="preserve"><g><path class="icon-fill"  d="M61.432,0c4.08,0,8.083,0.406,11.966,1.174c3.967,0.787,7.818,1.965,11.506,3.491 c3.75,1.553,7.313,3.466,10.643,5.69c3.389,2.263,6.514,4.827,9.328,7.641c2.814,2.815,5.383,5.941,7.645,9.331 c2.225,3.325,4.135,6.888,5.686,10.637c1.525,3.688,2.705,7.539,3.49,11.506c0.771,3.882,1.176,7.885,1.176,11.969 s-0.404,8.085-1.176,11.968c-0.785,3.967-1.965,7.818-3.49,11.506c-1.553,3.75-3.465,7.313-5.691,10.643 c-2.262,3.387-4.824,6.512-7.639,9.326c-2.816,2.814-5.941,5.381-9.332,7.646c-3.326,2.223-6.889,4.135-10.639,5.686 c-3.688,1.525-7.539,2.703-11.506,3.49c-3.883,0.77-7.887,1.174-11.966,1.174c-4.081,0-8.085-0.404-11.969-1.174 c-3.965-0.787-7.818-1.965-11.506-3.49c-3.75-1.553-7.313-3.467-10.643-5.691c-3.389-2.262-6.514-4.826-9.327-7.641l-0.185-0.199 c-2.757-2.791-5.256-5.854-7.451-9.143c-2.222-3.328-4.134-6.889-5.686-10.637c-1.525-3.688-2.705-7.539-3.491-11.506 C0.406,69.516,0,65.514,0,61.43c0-4.083,0.406-8.086,1.176-11.968c0.786-3.967,1.965-7.818,3.491-11.506 c1.543-3.74,3.458-7.301,5.692-10.644c2.245-3.356,4.81-6.475,7.645-9.308c2.809-2.822,5.935-5.388,9.326-7.653 c3.32-2.219,6.881-4.131,10.625-5.68c3.672-1.527,7.526-2.707,11.509-3.496C53.346,0.406,57.349,0,61.432,0L61.432,0z M54.706,21.567l32.494,70.994l1.15-0.99c0.473-0.426,1.023-0.953,1.646-1.576c1.904-1.904,3.605-3.965,5.063-6.145 c1.457-2.178,2.709-4.51,3.717-6.955c0.988-2.393,1.76-4.922,2.279-7.561c0.502-2.541,0.766-5.189,0.766-7.913 c0-2.725-0.264-5.373-0.766-7.914c-0.52-2.637-1.291-5.168-2.279-7.56c-1.014-2.449-2.268-4.783-3.727-6.965 c-1.457-2.175-3.152-4.232-5.053-6.134l-0.168-0.179c-1.838-1.813-3.842-3.455-5.979-4.884c-2.18-1.458-4.508-2.709-6.953-3.718 c-2.391-0.986-4.922-1.757-7.561-2.277c-2.543-0.502-5.191-0.766-7.914-0.766c-1.123,0-2.21,0.042-3.247,0.124 c-1.145,0.089-2.203,0.213-3.166,0.367L54.706,21.567L54.706,21.567z M68.156,101.291l-32.493-71l-1.146,0.986 c-0.538,0.482-1.093,1.01-1.658,1.573c-1.897,1.911-3.599,3.972-5.057,6.152c-1.456,2.177-2.708,4.509-3.717,6.954 c-0.988,2.392-1.759,4.922-2.279,7.56c-0.501,2.541-0.766,5.189-0.766,7.914c0,2.724,0.265,5.373,0.766,7.914 c0.52,2.639,1.291,5.168,2.279,7.561c1.015,2.447,2.269,4.781,3.729,6.967c1.454,2.174,3.151,4.232,5.051,6.133 c1.904,1.904,3.965,3.605,6.145,5.063c2.179,1.459,4.51,2.709,6.955,3.719c2.391,0.986,4.922,1.758,7.56,2.277 c2.544,0.502,5.192,0.766,7.914,0.766c1.125,0,2.21-0.041,3.248-0.123c1.15-0.09,2.213-0.215,3.18-0.369L68.156,101.291 L68.156,101.291z"/></g></svg>

                        </label>


                    </div>
                </div>

                <x-admin::attributes
                    :custom-attributes="$attributes"
                    :custom-validations="[
                        'name' => [
                            'min:2',
                            'max:100',
                        ],
                        'job_title' => [
                            'max:100',
                        ],
                    ]"
                    :entity="$person"
                />

                <div id="related-contacts-container">
                    <h2 class="mb-4 text-lg font-semibold text-gray-800 dark:text-white">Related Persons</h2>

                    <div id="contacts-list"></div>

                    <button
                        type="button"
                        id="add-contact-btn"
                        class="secondary-button mt-4"
                        onclick="addContactRow()"
                    >
                        + Add Related Contact
                    </button>
                </div>

                @php
                    $arrayData= $person->relatedContacts->map(function ($contact) {
                            return [
                                'id' => $contact->id,
                                'name' => $contact->name,
                                'type' => $contact->type,
                                'eid_expiry' => optional($contact->eid_expiry)->format('Y-m-d'),
                                'mobile_numbers' => json_decode($contact->mobile_numbers ?? '[]'),
                                'emails' => json_decode($contact->emails ?? '[]'),
                            ];
                        })->values();



                @endphp




                @pushOnce('scripts')
                    <style>
                        #toast-container {
                            position: fixed;
                            bottom: 1rem;
                            right: 1rem;
                            display: flex;
                            flex-direction: column-reverse; /* New toasts added at bottom */
                            gap: 0.5rem;
                            z-index: 9999;
                            max-width: 320px;
                        }
                        .toast {
                            padding: 12px 20px;
                            border-radius: 5px;
                            color: white;
                            min-width: 200px;
                            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
                            font-weight: 600;
                            opacity: 0.9;
                            transition: opacity 0.3s ease;
                            cursor: default;
                            user-select: none;
                        }
                        .toast-success {
                            background-color: #22c55e; /* Tailwind green-500 */
                        }
                        .toast-error {
                            background-color: #ef4444; /* Tailwind red-500 */
                        }

                    </style>
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


                        function createRelatedContact(contact) {
                            return fetch('/admin/contacts/related-contacts', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                                },
                                body: JSON.stringify(contact)
                            })
                                .then(res => res.json())
                                .then(data => {
                                    showToast(data.message, 'success');
                                    if (data.relatedContact && data.relatedContact.id) {
                                        contact.id = data.relatedContact.id;
                                        return contact;
                                    }
                                    return null;
                                })
                                .catch(err => {
                                    console.error('Create failed', err);
                                    showToast('Failed to create contact', 'error');
                                    return null;
                                });
                        }


                        function updateRelatedContact(contact) {
                            fetch(`/admin/contacts/related-contacts/${contact.id}`, {
                                method: 'PATCH',
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

                        function deleteRelatedContact(id) {
                            if (!confirm('Are you sure you want to delete this contact?')) return;

                            fetch(`/admin/contacts/related-contacts/${id}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                                },
                            })
                                .then(res => res.json())
                                .then(data => {
                                    showToast(data.message);
                                    // Remove from local contacts array and re-render
                                    contacts = contacts.filter(c => c.id !== id);
                                    renderContacts();
                                })
                                .catch(err => {
                                    console.error('Delete failed', err);
                                    showToast('Failed to delete contact', 'error');
                                });
                        }


                        const emptyContact = () => ({
                            id: null,
                            person_id:{{$person->id}},
                            name: '',
                            type: '',
                            eid_expiry: '',
                            mobile_numbers: [],  // keep as array internally
                            emails: []
                        });

                        //  let contacts = [];
                        let contacts = @json( $arrayData );

                        // Synchronize all inputs and tags back into contacts array before any render or add
                        function syncInputsToContacts() {
                            const list = document.getElementById('contacts-list');
                            contacts.forEach((contact, index) => {
                                const div = list.children[index];
                                if (!div) return;

                                // Sync inputs
                                const nameInput = div.querySelector(`input[name="related_contacts[${index}][name]"]`);
                                const typeSelect = div.querySelector(`select[name="related_contacts[${index}][type]"]`);
                                const dateInput = div.querySelector(`input[type="date"]`);

                                if (nameInput) contact.name = nameInput.value;
                                if (typeSelect) contact.type = typeSelect.value;
                                if (dateInput) contact.eid_expiry = dateInput.value;

                                // Sync mobile_numbers and emails from hidden inputs
                                ['mobile_numbers', 'emails'].forEach(field => {
                                    const wrapper = div.querySelector(`.tags-input-wrapper[data-type="${field}"]`);
                                    if (wrapper) {
                                        const hiddenInput = wrapper.querySelector('.hidden-json');
                                        try {
                                            contact[field] = JSON.parse(hiddenInput.value);
                                            if (!Array.isArray(contact[field])) contact[field] = [];
                                        } catch {
                                            contact[field] = [];
                                        }
                                    }
                                });
                            });
                        }

                        function initTagsInput(wrapper, index, field) {
                            const input = wrapper.querySelector('.tag-input');
                            const container = wrapper.querySelector('.tags-container');
                            const hiddenInput = wrapper.querySelector('.hidden-json');

                            // Ensure contacts[index][field] is an array
                            if (!Array.isArray(contacts[index][field])) {
                                try {
                                    contacts[index][field] = JSON.parse(contacts[index][field]);
                                    if (!Array.isArray(contacts[index][field])) contacts[index][field] = [];
                                } catch {
                                    contacts[index][field] = [];
                                }
                            }

                            function renderTags() {
                                container.innerHTML = '';
                                contacts[index][field].forEach((item, i) => {
                                    const tag = document.createElement('span');
                                    tag.className = 'inline-flex items-center bg-blue-100 text-blue-800 rounded px-2 py-1 text-sm mr-1 mb-1';
                                    tag.innerHTML = `${item} <button type="button" class="ml-1 text-red-600 remove-tag" data-i="${i}">x</button>`;
                                    container.appendChild(tag);
                                });
                                hiddenInput.value = JSON.stringify(contacts[index][field]);
                                bindRemoveButtons();
                            }

                            function bindRemoveButtons() {
                                wrapper.querySelectorAll('.remove-tag').forEach(btn => {
                                    btn.onclick = () => {
                                        const i = parseInt(btn.dataset.i);
                                        contacts[index][field].splice(i, 1);
                                        updateRelatedContact(contacts[index]);
                                        renderTags();
                                    };
                                });
                            }

                            input.addEventListener('keydown', e => {
                                if (e.key === 'Enter' && input.value.trim()) {
                                    e.preventDefault();
                                    contacts[index][field].push(input.value.trim());
                                    input.value = '';
                                    renderTags();
                                }
                            });

                            // Add button functionality
                            const addBtn = wrapper.querySelector('.add-tag-btn');
                            addBtn.onclick = () => {
                                if (input.value.trim()) {
                                    contacts[index][field].push(input.value.trim());
                                    input.value = '';
                                    renderTags();
                                    input.focus();
                                }
                            };

                            renderTags();
                        }

                        function bindAddTagButtons() {
                            document.querySelectorAll('.addPhoneNumber').forEach(btn => {
                                btn.onclick = () => {
                                    addFun(btn, 'mobile_numbers');
                                };
                            });

                            document.querySelectorAll('.addEmail').forEach(btn => {
                                btn.onclick = () => {
                                    addFun(btn, 'emails');
                                };
                            });
                        }

                        function renderContacts() {
                            const list = document.getElementById('contacts-list');
                            list.innerHTML = '';

                            contacts.forEach((contact, index) => {
                                const div = document.createElement('div');
                                div.className = 'grid grid-cols-6 gap-4 border p-3 rounded-md mb-2';

                                div.innerHTML = `
                <input type="hidden" name="related_contacts[${index}][id]" value="${contact.id ?? ''}">
                <input type="text" name="related_contacts[${index}][name]" class="input" placeholder="Name" value="${contact.name}">
                <select name="related_contacts[${index}][type]" class="input">
                    <option value="">-- Select Type --</option>
                    <option value="Manager" ${contact.type === 'Manager' ? 'selected' : ''}>Manager</option>
                    <option value="Partner" ${contact.type === 'Partner' ? 'selected' : ''}>Partner</option>
                    <option value="Family Visa" ${contact.type === 'Family Visa' ? 'selected' : ''}>Family Visa</option>
                    <option value="Local Agent" ${contact.type === 'Local Agent' ? 'selected' : ''}>Local Agent</option>
                </select>
                <input type="date" name="related_contacts[${index}][eid_expiry]" class="input" value="${contact.eid_expiry}">

                <div class="tags-input-wrapper" data-type="mobile_numbers" data-index="${index}">
                    <div class="tags-container"></div>
                    <div class="flex space-x-1 items-center mt-1">
                        <input type="text" id="mobile_numbers_${index}"  name="mobile_number_${index}" class="tag-input input flex-grow" value="+971" placeholder="Add phone" autocomplete="off" spellcheck="false" />
                        <button data-index="${index}" type="button" class="addPhoneNumber flex h-9 w-9 cursor-pointer items-center justify-center rounded-full bg-brandColor text-white add-tag-btn">
                            <i class="icon-add text-2xl"></i>
                        </button>
                    </div>
                    <input type="hidden" name="related_contacts[${index}][mobile_numbers]" class="hidden-json" value='${JSON.stringify(contact.mobile_numbers)}'>
                </div>

                <div class="tags-input-wrapper" data-type="emails" data-index="${index}">
                    <div class="tags-container"></div>
                    <div class="flex space-x-1 items-center mt-1">
                        <input type="text" id="emails_${index}"  name="email_${index}" class="tag-input input flex-grow" placeholder="Add email" autocomplete="off" spellcheck="false" />
                        <button data-index="${index}" type="button" class="addEmail flex h-9 w-9 cursor-pointer items-center justify-center rounded-full bg-brandColor text-white add-tag-btn">
                            <i class="icon-add text-2xl"></i>
                        </button>
                    </div>
                    <input type="hidden" name="related_contacts[${index}][emails]" class="hidden-json" value='${JSON.stringify(contact.emails)}'>
                </div>

                ${contacts.length > 1 ? `<button type="button" class="text-red-600 remove-contact-btn" data-index="${index}">🗑️</button>` : ''}
            `;

                                list.appendChild(div);

                                // Sync date input
                                document.querySelector(`input[name="related_contacts[${index}][eid_expiry]`).addEventListener('input', function () {
                                    contacts[index].eid_expiry = this.value;
                                    updateRelatedContact(contacts[index]);
                                });

                                // Sync other inputs
                                document.querySelector(`input[name="related_contacts[${index}][name]"]`).addEventListener('input', e => {
                                    contacts[index].name = e.target.value;
                                    updateRelatedContact(contacts[index]);
                                });

                                document.querySelector(`select[name="related_contacts[${index}][type]"]`).addEventListener('change', e => {
                                    contacts[index].type = e.target.value;
                                    updateRelatedContact(contacts[index]);
                                });

                                // Initialize tags inputs for mobile_numbers and emails
                                ['mobile_numbers', 'emails'].forEach(field => {
                                    const wrapper = div.querySelector(`.tags-input-wrapper[data-type="${field}"]`);
                                    initTagsInput(wrapper, index, field);
                                });


                            });

                            document.querySelectorAll('.remove-contact-btn').forEach(btn => {
                                btn.onclick = () => {
                                    const index = parseInt(btn.dataset.index);
                                    const selected = contacts[index];
                                    if (selected.id) {
                                        deleteRelatedContact(selected.id);
                                    }
                                    contacts.splice(index, 1);
                                    renderContacts();
                                };
                            });

                            // Add phone number button handlers
                            document.querySelectorAll('.addPhoneNumber').forEach(btn => {
                                btn.onclick = () => {
                                    addFun(btn, 'mobile_numbers');
                                };
                            });

                            document.querySelectorAll('.addEmail').forEach(btn => {
                                btn.onclick = () => {
                                    addFun(btn, 'emails');
                                };
                            });

                        }


                        function addFun(btn, field) {
                            const index = parseInt(btn.dataset.index);
                            const input = document.querySelector(`input[id="${field}_${index}"]`);

                            if (input.value.trim()) {
                                // Sync inputs before adding
                                syncInputsToContacts();

                                contacts[index][field].push(input.value.trim());
                                updateRelatedContact(contacts[index]);
                                input.value = '';

                                // Update tags container and hidden input
                                const wrapper = document.querySelector(`.tags-input-wrapper[data-type="${field}"][data-index="${index}"]`);
                                if (!wrapper) return;
                                const container = wrapper.querySelector('.tags-container');

                                // Clear all tags first
                                container.innerHTML = '';
                                contacts[index][field].forEach((item, i) => {
                                    const tag = document.createElement('span');
                                    tag.className = 'inline-flex items-center bg-blue-100 text-blue-800 rounded px-2 py-1 text-sm mr-1 mb-1';
                                    tag.innerHTML = `${item} <button type="button" class="ml-1 text-red-600 remove-tag" data-i="${i}">x</button>`;
                                    container.appendChild(tag);
                                });

                                // Update hidden input
                                const hiddenInput = wrapper.querySelector('.hidden-json');
                                hiddenInput.value = JSON.stringify(contacts[index][field]);

                                // Bind remove tag buttons
                                wrapper.querySelectorAll('.remove-tag').forEach(btn => {
                                    btn.onclick = () => {
                                        const i = parseInt(btn.dataset.i);
                                        contacts[index][field].splice(i, 1);
                                        updateRelatedContact(contacts[index]);
                                        renderContacts();
                                    };
                                });
                            }
                        }

                        function validateContacts() {
                            for (let i = 0; i < contacts.length; i++) {
                                const contact = contacts[i];
                                if (!contact.name.trim()) {
                                    alert(`Contact #${i + 1}: Name is required.`);
                                    return false;
                                }
                                if (!contact.type.trim()) {
                                    alert(`Contact #${i + 1}: Type is required.`);
                                    return false;
                                }
                                if (contact.type !== 'Local Agent' && !contact.eid_expiry.trim()) {
                                    alert(`Contact #${i + 1}: EID Expiry is required unless contact is a Local Agent.`);
                                    return false;
                                }
                            }
                            return true;
                        }

                        function addContactRow() {
                            syncInputsToContacts();
                            if (!validateContacts()) {
                                return;
                            }
                            createRelatedContact(emptyContact()).then(contact => {
                                if (contact) {
                                    contacts.push(contact);
                                    renderContacts();
                                }
                            });

                        }

                        function initRelatedContacts() {
                            const oldContacts = {!! json_encode(old('related_contacts') ?? []) !!};
                            if (oldContacts.length) {
                                contacts = oldContacts.map(c => ({
                                    id: c.id ?? null,
                                    name: c.name ?? '',
                                    type: c.type ?? '',
                                    eid_expiry: c.eid_expiry ?? '',
                                    mobile_numbers: Array.isArray(c.mobile_numbers) ? c.mobile_numbers : JSON.parse(c.mobile_numbers || '[]'),
                                    emails: Array.isArray(c.emails) ? c.emails : JSON.parse(c.emails || '[]'),
                                }));
                            } else {
                                contacts = [emptyContact()];
                            }
                            renderContacts();
                        }



                        document.addEventListener('DOMContentLoaded', () => {
                            renderContacts();
                            setTimeout(() => {
                                syncInputsToContacts();
                                contacts.push(emptyContact());
                                renderContacts();
                                contacts.splice(contacts.length - 1, 1);
                                renderContacts()
                            }, 500);
                        });


                    </script>



                    <style>
                        .tags-container span {
                            display: inline-flex;
                            margin-right: 4px;
                            background: #bfdbfe; /* Tailwind blue-200 */
                            color: #1e40af; /* Tailwind blue-900 */
                            padding: 0.25rem 0.5rem;
                            border-radius: 0.25rem;
                            user-select: none;
                            align-items: center;
                        }

                        .tags-container span button {
                            background: transparent;
                            border: none;
                            cursor: pointer;
                            font-weight: bold;
                            margin-left: 0.25rem;
                            color: #dc2626; /* Tailwind red-600 */
                        }

                        .tag-input.input {
                            border: 1px solid #d1d5db; /* Tailwind gray-300 */
                            padding: 0.25rem 0.5rem;
                            border-radius: 0.25rem;
                            width: 100%;
                            box-sizing: border-box;
                            margin-top: 0.25rem;
                        }
                    </style>
                @endPushOnce

                {!! view_render_event('admin.contacts.persons.edit.form_controls.after') !!}
            </div>
        </div>
    </x-admin::form>
    <div id="toast-container" style="position: fixed; top: 1rem; right: 1rem; z-index: 9999;"></div>

    {!! view_render_event('admin.persons.edit.form.after') !!}
</x-admin::layouts>
