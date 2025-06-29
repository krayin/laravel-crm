@props([
    'entity'            => null,
    'entityControlName' => null,
])

<!-- Related Persons Button -->
<div>
    <button
        class="flex h-[74px] w-[84px] flex-col items-center justify-center gap-1 rounded-lg border border-transparent bg-green-200 font-medium text-green-900 transition-all hover:border-green-400"
        @click="$refs.relatedPersonsModal.open()"
    >
        <span class="icon-user text-2xl dark:!text-green-900"></span>
        Related
    </button>

    <v-related-persons-modal
        ref="relatedPersonsModal"
        :entity="{{ json_encode($entity) }}"
    ></v-related-persons-modal>
</div>

@pushOnce('scripts')
    <script type="text/x-template" id="v-related-persons-template">
        <Teleport to="body">
            <x-admin::modal
                ref="modal"
                position="center"
                size="large"
            >
                <x-slot:header>
                    <h3 class="text-base font-semibold dark:text-white">
                        Related Persons
                    </h3>
                    </x-slot>

                    <x-slot:content  >
                        <div v-if="entity?.related_contacts?.length">
                            <table class="w-full table-auto border text-sm">
                                <thead class="bg-gray-100 dark:bg-gray-800">
                                <tr>
                                    <th class="p-2 border">Name</th>
                                    <th class="p-2 border">Type</th>
                                    <th class="p-2 border">EID Expiry</th>
                                    <th class="p-2 border">Phone Numbers</th>
                                    <th class="p-2 border">Emails</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(contact, index) in entity.related_contacts" :key="index">
                                    <td class="p-2 border">@{{ contact.name }}</td>
                                    <td class="p-2 border">@{{ contact.type }}</td>
                                    <td class="p-2 border">@{{ formatDate(contact.eid_expiry) }}</td>
                                    <td class="p-2 border">
                                        <ul>
                                            <li v-for="phone in parseJsonArray(contact.mobile_numbers)" :key="phone">@{{ phone }}</li>
                                        </ul>
                                    </td>
                                    <td class="p-2 border">
                                        <ul>
                                            <li v-for="email in parseJsonArray(contact.emails)" :key="email">@{{ email }}</li>
                                        </ul>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div v-else class="text-gray-500 text-center py-4">
                            No related contacts found.
                        </div>
                        </x-slot>

                        <x-slot:footer>
                            <x-admin::button class="secondary-button" @click="close()" title="Close" />
                            </x-slot>
            </x-admin::modal>
        </Teleport>
    </script>

    <script type="module">
        app.component('v-related-persons-modal', {
            template: '#v-related-persons-template',

            props: {
                entity: {
                    type: Object,
                    required: true
                }
            },

            methods: {
                open() {
                    console.log('Related Contacts:', this.entity?.related_contacts);
                    this.$refs.modal.open();
                },
                close() {
                    this.$refs.modal.close();
                },
                parseJsonArray(jsonString) {
                    if (!jsonString) return [];
                    try {
                        return JSON.parse(jsonString);
                    } catch (e) {
                        return [];
                    }
                },
                formatDate(dateStr) {
                    if (!dateStr) return '';

                    const date = new Date(dateStr);
                    const now = new Date();

                    // Format the date like "29 Jun 2025"
                    const formattedDate = date.toLocaleDateString('en-GB', {
                        year: 'numeric',
                        month: 'short',
                        day: '2-digit'
                    });

                    // Calculate remaining days
                    const diffTime = date - now;
                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

                    let remainText = '';
                    if (diffDays > 0) {
                        remainText = `(in ${diffDays} day${diffDays > 1 ? 's' : ''})`;
                    } else if (diffDays === 0) {
                        remainText = `(today)`;
                    } else {
                        remainText = `(${Math.abs(diffDays)} day${Math.abs(diffDays) > 1 ? 's' : ''} ago)`;
                    }

                    return `${formattedDate} ${remainText}`;
                }

            }
        });
    </script>
@endPushOnce
