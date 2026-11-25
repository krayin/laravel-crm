{!! view_render_event('admin.leads.view.firma.before', ['lead' => $lead]) !!}

<v-lead-firma></v-lead-firma>

{!! view_render_event('admin.leads.view.firma.after', ['lead' => $lead]) !!}

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-lead-firma-template"
    >
        <div class="flex flex-col gap-4 p-3">
            <div class="flex items-center justify-between gap-2">
                <p class="text-gray-600 dark:text-gray-300">
                    @lang('admin::app.firma.lead.info')
                </p>

                <button
                    type="button"
                    class="primary-button"
                    @click="openModal"
                >
                    @lang('admin::app.firma.lead.send-btn')
                </button>
            </div>

            <!-- Requests -->
            <x-admin::table v-if="requests.length">
                <x-admin::table.thead>
                    <x-admin::table.thead.tr>
                        <x-admin::table.th class="!px-2">
                            @lang('admin::app.firma.lead.name')
                        </x-admin::table.th>

                        <x-admin::table.th class="!px-2">
                            @lang('admin::app.firma.lead.recipient')
                        </x-admin::table.th>

                        <x-admin::table.th class="!px-2">
                            @lang('admin::app.firma.lead.status')
                        </x-admin::table.th>

                        <x-admin::table.th class="!px-2">
                            @lang('admin::app.firma.lead.sent-at')
                        </x-admin::table.th>

                        <x-admin::table.th class="actions"></x-admin::table.th>
                    </x-admin::table.thead.tr>
                </x-admin::table.thead>

                <x-admin::table.tbody>
                    <x-admin::table.tbody.tr
                        v-for="request in requests"
                        ::key="request.id"
                        class="border-b"
                    >
                        <x-admin::table.td class="!px-2">
                            @{{ request.name }}
                        </x-admin::table.td>

                        <x-admin::table.td class="!px-2">
                            @{{ request.recipient_name }} (@{{ request.recipient_email }})
                        </x-admin::table.td>

                        <x-admin::table.td class="!px-2">
                            <p>@{{ request.status_label }}</p>

                            <p
                                class="text-xs text-gray-500"
                                v-if="request.status == 'uncertain'"
                            >
                                @lang('admin::app.firma.lead.uncertain-info')
                            </p>

                            <p
                                class="text-xs text-gray-500"
                                v-if="request.has_signed_document"
                            >
                                @lang('admin::app.firma.lead.signed-attached')
                            </p>

                            <p
                                class="text-xs text-gray-500"
                                v-if="request.no_invitation_email"
                            >
                                @lang('admin::app.firma.send.no-invitation')
                            </p>
                        </x-admin::table.td>

                        <x-admin::table.td class="!px-2">
                            @{{ request.sent_at ? $admin.formatDate(request.sent_at, 'd MMM yyyy, h:mm A') : '' }}
                        </x-admin::table.td>

                        <x-admin::table.td class="!px-2">
                            <button
                                type="button"
                                class="secondary-button"
                                v-if="request.firma_id"
                                :disabled="refreshing == request.id"
                                @click="refresh(request)"
                            >
                                @lang('admin::app.firma.lead.refresh-btn')
                            </button>
                        </x-admin::table.td>
                    </x-admin::table.tbody.tr>
                </x-admin::table.tbody>
            </x-admin::table>

            <p
                class="text-gray-400"
                v-else-if="! isLoading"
            >
                @lang('admin::app.firma.lead.empty')
            </p>

            <!-- Send Modal -->
            <Teleport to="body">
                <x-admin::modal ref="firmaSendModal">
                    <x-slot:header>
                        <h3 class="text-base font-semibold dark:text-white">
                            @lang('admin::app.firma.lead.send-title')
                        </h3>
                    </x-slot>

                    <x-slot:content>
                        <div class="flex flex-col gap-4">
                            <!-- Source -->
                            <div class="flex gap-4 text-sm text-gray-800 dark:text-white">
                                <label class="flex items-center gap-1">
                                    <input
                                        type="radio"
                                        value="template"
                                        v-model="form.source"
                                    />

                                    @lang('admin::app.firma.lead.source-template')
                                </label>

                                <label
                                    class="flex items-center gap-1"
                                    v-if="quotes.length"
                                >
                                    <input
                                        type="radio"
                                        value="quote"
                                        v-model="form.source"
                                    />

                                    @lang('admin::app.firma.lead.source-quote')
                                </label>
                            </div>

                            <!-- Template -->
                            <div v-if="form.source == 'template'">
                                <label class="mb-1.5 block text-xs font-medium text-gray-800 dark:text-white">
                                    @lang('admin::app.firma.lead.template')
                                </label>

                                <select
                                    class="w-full rounded border border-gray-300 px-2.5 py-2 text-sm font-normal text-gray-800 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400"
                                    v-model="form.template_id"
                                >
                                    <option
                                        v-for="template in templates"
                                        :value="template.id"
                                        :disabled="! template.supported"
                                    >
                                        @{{ template.supported ? template.name : template.name + ' (' + unsupportedLabel + ')' }}
                                    </option>
                                </select>

                                <p
                                    class="mt-1 text-xs text-gray-500"
                                    v-if="isLoadingTemplates"
                                >
                                    @lang('admin::app.firma.lead.loading-templates')
                                </p>

                                <p
                                    class="mt-1 text-xs text-gray-500"
                                    v-if="! templatesComplete"
                                >
                                    @lang('admin::app.firma.lead.templates-incomplete')
                                </p>
                            </div>

                            <!-- Quote -->
                            <div v-if="form.source == 'quote'">
                                <label class="mb-1.5 block text-xs font-medium text-gray-800 dark:text-white">
                                    @lang('admin::app.firma.lead.quote')
                                </label>

                                <select
                                    class="w-full rounded border border-gray-300 px-2.5 py-2 text-sm font-normal text-gray-800 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400"
                                    v-model="form.quote_id"
                                >
                                    <option
                                        v-for="quote in quotes"
                                        :value="quote.id"
                                    >
                                        @{{ quote.subject }}
                                    </option>
                                </select>
                            </div>

                            <!-- Name, recipient, expiration -->
                            <div v-for="field in fields">
                                <label class="mb-1.5 block text-xs font-medium text-gray-800 dark:text-white">
                                    @{{ field.label }}
                                </label>

                                <input
                                    class="w-full rounded border border-gray-300 px-2.5 py-2 text-sm font-normal text-gray-800 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400"
                                    :type="field.type"
                                    v-model="form[field.name]"
                                />
                            </div>
                        </div>
                    </x-slot>

                    <x-slot:footer>
                        <button
                            type="button"
                            class="primary-button"
                            :disabled="isSending"
                            @click="send"
                        >
                            @lang('admin::app.firma.lead.send-btn')
                        </button>
                    </x-slot>
                </x-admin::modal>
            </Teleport>
        </div>
    </script>

    @php
        $firmaQuotes = bouncer()->hasPermission('quotes')
            ? $lead->quotes->map(fn ($quote) => ['id' => $quote->id, 'subject' => $quote->subject])->values()
            : [];

        $firmaDefaults = [
            'name' => $lead->title,
            'recipient_name' => $lead->person?->name ?? '',
            'recipient_email' => data_get($lead->person?->emails, '0.value', ''),
        ];
    @endphp

    <script type="module">
        app.component('v-lead-firma', {
            template: '#v-lead-firma-template',

            data() {
                return {
                    requests: [],

                    templates: [],

                    templatesComplete: true,

                    isLoading: true,

                    isLoadingTemplates: false,

                    isSending: false,

                    refreshing: null,

                    /**
                     * One key per send dialog: the server refuses a second send with the same key,
                     * so a repeated click or a retried request never sends twice.
                     */
                    idempotencyKey: null,

                    quotes: @json($firmaQuotes),

                    defaults: @json($firmaDefaults),

                    unsupportedLabel: @json(trans('admin::app.firma.lead.template-unsupported')),

                    fields: [
                        { name: 'name', type: 'text', label: @json(trans('admin::app.firma.lead.request-name')) },
                        { name: 'recipient_name', type: 'text', label: @json(trans('admin::app.firma.lead.recipient-name')) },
                        { name: 'recipient_email', type: 'email', label: @json(trans('admin::app.firma.lead.recipient-email')) },
                        { name: 'expiration_hours', type: 'number', label: @json(trans('admin::app.firma.lead.expiration')) },
                    ],

                    form: {},
                };
            },

            mounted() {
                this.resetForm();

                this.load();
            },

            methods: {
                newKey() {
                    const bytes = window.crypto.getRandomValues(new Uint8Array(16));

                    bytes[6] = (bytes[6] & 0x0f) | 0x40;

                    bytes[8] = (bytes[8] & 0x3f) | 0x80;

                    const hex = Array.from(bytes, byte => byte.toString(16).padStart(2, '0')).join('');

                    return `${hex.slice(0, 8)}-${hex.slice(8, 12)}-${hex.slice(12, 16)}-${hex.slice(16, 20)}-${hex.slice(20)}`;
                },

                resetForm() {
                    this.idempotencyKey = this.newKey();

                    this.form = {
                        source: 'template',
                        template_id: null,
                        quote_id: this.quotes.length ? this.quotes[0].id : null,
                        name: this.defaults.name,
                        recipient_name: this.defaults.recipient_name,
                        recipient_email: this.defaults.recipient_email,
                        expiration_hours: null,
                    };
                },

                load() {
                    this.$axios.get("{{ route('admin.leads.firma.index', $lead->id) }}")
                        .then(response => {
                            this.requests = response.data.data;

                            this.isLoading = false;
                        })
                        .catch(() => this.isLoading = false);
                },

                openModal() {
                    this.$refs.firmaSendModal.open();

                    if (this.templates.length || this.isLoadingTemplates) {
                        return;
                    }

                    this.isLoadingTemplates = true;

                    this.$axios.get("{{ route('admin.leads.firma.templates') }}")
                        .then(response => {
                            this.templates = response.data.data;

                            this.templatesComplete = response.data.complete;

                            const first = this.templates.find(template => template.supported);

                            if (first && ! this.form.template_id) {
                                this.form.template_id = first.id;
                            }
                        })
                        .catch(error => {
                            this.$emitter.emit('add-flash', { type: 'error', message: error.response?.data?.message });
                        })
                        .finally(() => this.isLoadingTemplates = false);
                },

                send() {
                    this.isSending = true;

                    this.$axios.post("{{ route('admin.leads.firma.store', $lead->id) }}", {
                        ...this.form,
                        idempotency_key: this.idempotencyKey,
                    })
                        .then(response => {
                            this.$emitter.emit('add-flash', {
                                type: response.data.outcome == 'sent' ? 'success' : 'warning',
                                message: response.data.message,
                            });

                            this.$refs.firmaSendModal.close();

                            this.resetForm();

                            this.load();
                        })
                        .catch(error => {
                            /**
                             * The key is kept: a 422 means nothing was sent, and any other failure
                             * may hide a send, which the same key then refuses to repeat.
                             */
                            this.$emitter.emit('add-flash', {
                                type: 'error',
                                message: error.response?.status == 422
                                    ? (error.response.data.message ?? Object.values(error.response.data.errors ?? {}).flat().join(' '))
                                    : @json(trans('admin::app.firma.lead.send-unknown')),
                            });

                            this.load();
                        })
                        .finally(() => this.isSending = false);
                },

                refresh(request) {
                    this.refreshing = request.id;

                    this.$axios.post("{{ route('admin.leads.firma.refresh', [$lead->id, 'replaceId']) }}".replace('replaceId', request.id))
                        .then(response => {
                            const index = this.requests.findIndex(row => row.id == request.id);

                            if (index !== -1) {
                                this.requests.splice(index, 1, response.data.data);
                            }

                            this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });
                        })
                        .catch(error => {
                            this.$emitter.emit('add-flash', { type: 'error', message: error.response?.data?.message });
                        })
                        .finally(() => this.refreshing = null);
                },
            },
        });
    </script>
@endPushOnce
