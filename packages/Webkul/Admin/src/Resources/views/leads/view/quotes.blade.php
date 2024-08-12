<v-lead-quotes></v-lead-quotes>

@pushOnce('scripts')
    <script 
        type="text/x-template" 
        id="v-lead-quotes-template"
    >
        @if (bouncer()->hasPermission('quotes'))
            <div class="p-4">
                <x-admin::table>
                    <x-admin::table.thead>
                        <x-admin::table.thead.tr>
                            <x-admin::table.th>
                                @lang('admin::app.leads.view.quotes.subject')
                            </x-admin::table.th>

                            <x-admin::table.th>
                                @lang('admin::app.leads.view.quotes.expired-at')
                            </x-admin::table.th>

                            <x-admin::table.th>
                                @lang('admin::app.leads.view.quotes.sub-total')
                                <span class="currency-code">({{ core()->currencySymbol(config('app.currency')) }})</span>
                            </x-admin::table.th>

                            <x-admin::table.th>
                                @lang('admin::app.leads.view.quotes.discount')
                                <span class="currency-code">({{ core()->currencySymbol(config('app.currency')) }})</span>
                            </x-admin::table.th>

                            <x-admin::table.th >
                                @lang('admin::app.leads.view.quotes.tax')
                                <span class="currency-code">({{ core()->currencySymbol(config('app.currency')) }})</span>
                            </x-admin::table.th>

                            <x-admin::table.th class="adjustment">
                                @lang('admin::app.leads.view.quotes.adjustment')
                                <span class="currency-code">({{ core()->currencySymbol(config('app.currency')) }})</span>
                            </x-admin::table.th>

                            <x-admin::table.th class="grand-total">
                                @lang('admin::app.leads.view.quotes.grand-total')
                                <span class="currency-code">({{ core()->currencySymbol(config('app.currency')) }})</span>
                            </x-admin::table.th>

                            <x-admin::table.th class="actions"></x-admin::table.th>
                        </x-admin::table.thead.tr>
                    </x-admin::table.thead>
                    
                    <x-admin::table.tbody>
                        <x-admin::table.tbody.tr v-for="quote in quotes">
                            <x-admin::table.td>@{{ quote.subject }}</x-admin::table.td>

                            <x-admin::table.td>@{{ quote.expired_at }}</x-admin::table.td>
                            
                            <x-admin::table.td>@{{ quote.sub_total }}</x-admin::table.td>

                            <x-admin::table.td>@{{ quote.discount_amount }}</x-admin::table.td>

                            <x-admin::table.td>@{{ quote.tax_amount }}</x-admin::table.td>

                            <x-admin::table.td>@{{ quote.adjustment_amount }}</x-admin::table.td>

                            <x-admin::table.td>@{{ quote.grand_total }}</x-admin::table.td>

                            <x-admin::table.td>
                                <x-admin::dropdown position="bottom-right">
                                    <x-slot:toggle>
                                        <icon class="icon-more text-2xl"></icon>
                                    </x-slot>

                                    <x-slot:menu class="!min-w-40">
                                        @if (bouncer()->hasPermission('quotes.edit'))
                                            <x-admin::dropdown.menu.item>
                                                <a :href="'{{ route('admin.quotes.edit') }}/' + quote.id">
                                                    <div class="flex items-center gap-2">
                                                        <span class="icon-edit text-2xl"></span>
                                                        
                                                        @lang('admin::app.leads.view.quotes.edit')
                                                    </div>
                                                </a>
                                            </x-admin::dropdown.menu.item>
                                        @endif

                                        @if (bouncer()->hasPermission('quotes.print'))
                                            <x-admin::dropdown.menu.item>
                                                <a :href="'{{ route('admin.quotes.print') }}/' + quote.id" target="_blank">
                                                    <div class="flex items-center gap-2">
                                                        <span class="icon-sent text-2xl"></span>

                                                        @lang('admin::app.leads.view.quotes.download')
                                                    </div>
                                                </a>

                                            </x-admin::dropdown.menu.item>
                                        @endif
                                        
                                        @if (bouncer()->hasPermission('quotes.delete'))
                                            <x-admin::dropdown.menu.item @click="removeQuote(quote)">
                                                <div class="flex items-center gap-2">
                                                    <span class="icon-delete text-2xl"></span>

                                                    @lang('admin::app.leads.view.quotes.delete')
                                                </div>
                                            </x-admin::dropdown.menu.item>
                                        @endif
                                    </x-slot>
                                </x-admin::dropdown>
                            </x-admin::table.td>
                        </x-admin::table.tbody.tr>
                    </x-admin::table.tbody>
                </x-admin::table>
            </div>
        @endif
    </script>


    <script type="module">
        app.component('v-lead-quotes', {
            template: '#v-lead-quotes-template',

            props: ['data'],

            data: function () {
                return {
                    quotes: @json($lead->quotes()->with(['person', 'user'])->get())
                }
            },

            methods: {
                removeQuote(quote) {
                    this.$emitter.emit('open-confirm-modal', {
                        agree: () => {
                            this.isLoading = true;

                            this.$axios.delete("{{ route('admin.leads.quotes.delete', $lead->id) }}/" + quote.id)
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
                }

            },
        });
    </script>
@endPushOnce