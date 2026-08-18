{!! view_render_event('admin.leads.view.quotes.before', ['lead' => $lead]) !!}

<v-lead-quotes></v-lead-quotes>

{!! view_render_event('admin.leads.view.quotes.after', ['lead' => $lead]) !!}

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-lead-quotes-template"
    >
        @if (bouncer()->hasPermission('quotes'))
            <div class="p-3">
                
                <div v-if="quotes.length">
                    {!! view_render_event('admin.leads.view.quotes.table.before', ['lead' => $lead]) !!}
                    
                    <x-admin::table>
                        {!! view_render_event('admin.leads.view.quotes.table.table_head.before', ['lead' => $lead]) !!}

                        <x-admin::table.thead>
                            <x-admin::table.thead.tr>
                                <x-admin::table.th class="!px-2 w-1/10">
                                    @lang('admin::app.leads.view.quotes.id')
                                </x-admin::table.th>

                                <x-admin::table.th class="!px-2">
                                    @lang('admin::app.leads.view.quotes.subject')
                                </x-admin::table.th>

                                <x-admin::table.th class="!px-2">
                                    @lang('admin::app.leads.view.quotes.expired-at')
                                </x-admin::table.th>

                                <x-admin::table.th class="!px-2">
                                    @lang('admin::app.leads.view.quotes.sub-total')
                                    <span class="currency-code">({{ core()->currencySymbol(config('app.currency')) }})</span>
                                </x-admin::table.th>

                                <x-admin::table.th class="!px-2">
                                    @lang('admin::app.leads.view.quotes.discount')
                                    <span class="currency-code">({{ core()->currencySymbol(config('app.currency')) }})</span>
                                </x-admin::table.th>

                                <x-admin::table.th class="!px-2">
                                    @lang('admin::app.leads.view.quotes.tax')
                                    <span class="currency-code">({{ core()->currencySymbol(config('app.currency')) }})</span>
                                </x-admin::table.th>

                                <x-admin::table.th class="!px-2">
                                    @lang('admin::app.leads.view.quotes.adjustment')
                                    <span class="currency-code">({{ core()->currencySymbol(config('app.currency')) }})</span>
                                </x-admin::table.th>

                                <x-admin::table.th class="!px-2">
                                    @lang('admin::app.leads.view.quotes.grand-total')
                                    <span class="currency-code">({{ core()->currencySymbol(config('app.currency')) }})</span>
                                </x-admin::table.th>

                                <x-admin::table.th class="actions"></x-admin::table.th>
                            </x-admin::table.thead.tr>
                        </x-admin::table.thead>

                        {!! view_render_event('admin.leads.view.quotes.table.table_head.after', ['lead' => $lead]) !!}

                        {!! view_render_event('admin.leads.view.quotes.table.table_body.before', ['lead' => $lead]) !!}

                        <x-admin::table.tbody>
                            <x-admin::table.tbody.tr v-for="quote in quotes" class="border-b">
                                <x-admin::table.td class="text-wrap !px-2">@{{ quote.id }}</x-admin::table.td>

                                <x-admin::table.td class="text-wrap !px-2">@{{ quote.subject }}</x-admin::table.td>

                                <x-admin::table.td class="!px-2">@{{ quote.expired_at }}</x-admin::table.td>

                                <x-admin::table.td class="!px-2">@{{ quote.sub_total }}</x-admin::table.td>

                                <x-admin::table.td class="!px-2">@{{ quote.discount_amount }}</x-admin::table.td>

                                <x-admin::table.td class="!px-2">@{{ quote.tax_amount }}</x-admin::table.td>

                                <x-admin::table.td class="!px-2">@{{ quote.adjustment_amount }}</x-admin::table.td>

                                <x-admin::table.td class="!px-2">@{{ quote.grand_total }}</x-admin::table.td>

                                <x-admin::table.td class="!px-2">
                                    {!! view_render_event('admin.leads.view.quotes.table.table_body.dropdown.before', ['lead' => $lead]) !!}

                                    <x-admin::dropdown position="bottom-right">
                                        <x-slot:toggle>
                                            <i class="icon-more cursor-pointer text-2xl"></i>
                                        </x-slot>

                                        <x-slot:menu class="!min-w-40">
                                            @if (bouncer()->hasPermission('quotes.mail'))
                                                {!! view_render_event('admin.leads.view.quotes.table.table_body.quote.mail.before', ['lead' => $lead]) !!}

                                                <x-admin::dropdown.menu.item>
                                                    <a href="javascript:void(0)" @click="sendMail(quote.id)">
                                                        <div class="flex items-center gap-2">
                                                            <span class="icon-mail text-2xl"></span>

                                                            @lang('admin::app.leads.view.quotes.mail')
                                                        </div>
                                                    </a>
                                                </x-admin::dropdown.menu.item>

                                                {!! view_render_event('admin.leads.view.quotes.table.table_body.quote.mail.after', ['lead' => $lead]) !!}
                                            @endif

                                            @if (bouncer()->hasPermission('quotes.edit'))
                                                {!! view_render_event('admin.leads.view.quotes.table.table_body.quote.edit.before', ['lead' => $lead]) !!}

                                                <x-admin::dropdown.menu.item>
                                                    <a :href="'{{ route('admin.quotes.edit') }}/' + quote.id + '?from=lead&lead_id={{ $lead->id }}'">
                                                        <div class="flex items-center gap-2">
                                                            <span class="icon-edit text-2xl"></span>

                                                            @lang('admin::app.leads.view.quotes.edit')
                                                        </div>
                                                    </a>
                                                </x-admin::dropdown.menu.item>

                                                {!! view_render_event('admin.leads.view.quotes.table.table_body.quote.edit.after', ['lead' => $lead]) !!}
                                            @endif

                                            @if (bouncer()->hasPermission('quotes.print'))
                                                {!! view_render_event('admin.leads.view.quotes.table.table_body.quote.download.before', ['lead' => $lead]) !!}

                                                <x-admin::dropdown.menu.item>
                                                    <a :href="'{{ route('admin.quotes.print') }}/' + quote.id" target="_blank">
                                                        <div class="flex items-center gap-2">
                                                            <span class="icon-download text-2xl"></span>

                                                            @lang('admin::app.leads.view.quotes.download')
                                                        </div>
                                                    </a>
                                                </x-admin::dropdown.menu.item>

                                                {!! view_render_event('admin.leads.view.quotes.table.table_body.quote.download.after', ['lead' => $lead]) !!}
                                            @endif

                                            @if (bouncer()->hasPermission('quotes.delete'))
                                                {!! view_render_event('admin.leads.view.quotes.table.table_body.quote.delete.before', ['lead' => $lead]) !!}

                                                <x-admin::dropdown.menu.item @click="removeQuote(quote)">
                                                    <div class="flex items-center gap-2">
                                                        <span class="icon-delete text-2xl"></span>

                                                        @lang('admin::app.leads.view.quotes.delete')
                                                    </div>
                                                </x-admin::dropdown.menu.item>

                                                {!! view_render_event('admin.leads.view.quotes.table.table_body.quote.delete.after', ['lead' => $lead]) !!}
                                            @endif
                                        </x-slot>
                                    </x-admin::dropdown>

                                    {!! view_render_event('admin.leads.view.quotes.table.table_body.dropdown.after', ['lead' => $lead]) !!}
                                </x-admin::table.td>
                            </x-admin::table.tbody.tr>
                        </x-admin::table.tbody>

                        {!! view_render_event('admin.leads.view.quotes.table.table_body.after', ['lead' => $lead]) !!}
                    </x-admin::table>
                    
                    {!! view_render_event('admin.leads.view.quotes.table.after', ['lead' => $lead]) !!}

                    {!! view_render_event('admin.leads.view.quotes.table.add_more.before', ['lead' => $lead]) !!}

                    <!-- Add New Quote -->
                    <div class="mt-4">
                        <a
                            class="flex max-w-max items-center gap-2 text-brandColor"
                            href="{{ route('admin.quotes.create', $lead->id) }}?from=lead"
                        >
                            <i class="icon-add text-md !text-brandColor"></i>

                            @lang('admin::app.leads.view.quotes.add-btn')
                        </a>
                    </div>

                    {!! view_render_event('admin.leads.view.quotes.table.add_more.after', ['lead' => $lead]) !!}
                </div>

                <div v-else>
                    <div class="grid justify-center justify-items-center gap-3.5 py-12">
                        <img
                            class="dark:mix-blend-exclusion dark:invert"
                            src="{{ vite()->asset('images/empty-placeholders/quotes.svg') }}"
                        >

                        <div class="flex flex-col items-center gap-2">
                            <p class="text-xl font-semibold dark:text-white">
                                @lang('admin::app.leads.view.quotes.empty-title')
                            </p>

                            <p class="text-gray-400">
                                @lang('admin::app.leads.view.quotes.empty-info')
                            </p>
                        </div>

                        <a
                            class="secondary-button"
                            href="{{ route('admin.quotes.create', $lead->id) }}?from=lead"
                        >
                            @lang('admin::app.leads.view.quotes.add-btn')
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </script>


    <script type="module">
        app.component('v-lead-quotes', {
            template: '#v-lead-quotes-template',

            props: ['data'],

            data: function () {
                return {
                    quotes: @json($lead->quotes()->with(['person', 'user'])->get()),
                }
            },

            methods: {
                removeQuote(quote) {
                    this.$emitter.emit('open-confirm-modal', {
                        agree: () => {
                            this.isLoading = true;

                            this.$axios.delete("{{ route('admin.leads.quotes.delete') }}/" + quote.id)
                                .then(response => {
                                    this.isLoading = false;

                                    const index = this.quotes.indexOf(quote);

                                    if (index !== -1) {
                                        this.quotes.splice(index, 1);
                                    }

                                    this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });
                                })
                                .catch(error => {
                                    this.isLoading = false;

                                    this.$emitter.emit('add-flash', { type: 'error', message: error.response.data.message });
                                });
                        }
                    });
                },

                sendMail(quoteId) {
                    this.$axios.post("{{ route('admin.leads.quotes.mail', ['quote_id' => 'replaceId']) }}".replace('replaceId', quoteId))
                        .then((response) => {
                            this.$emitter.emit('add-flash', {
                                type: 'success',
                                message: response.data.message,
                            });
                        })
                        .catch((error) => {
                            this.$emitter.emit('add-flash', {
                                type: 'error',
                                message: error?.response?.data?.message || 'Unable to send quote email.',
                            });
                        });
                }
            },
        });
    </script>
@endPushOnce
