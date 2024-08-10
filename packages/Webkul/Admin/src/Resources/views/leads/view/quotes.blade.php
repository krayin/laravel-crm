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

                                    <x-slot:content>
                                        @if (bouncer()->hasPermission('quotes.edit'))
                                            <li>
                                                <a :href="'{{ route('admin.quotes.edit') }}/' + quote.id">
                                                    @lang('admin::app.leads.view.quotes.edit') }}
                                                </a>
                                            </li>
                                        @endif

                                        @if (bouncer()->hasPermission('quotes.print'))
                                            <li>
                                                <a :href="'{{ route('admin.quotes.print') }}/' + quote.id" target="_blank">
                                                    @lang('admin::app.leads.view.quotes.export-to-pdf') }}
                                                </a>
                                            </li>
                                        @endif
                                        
                                        @if (bouncer()->hasPermission('quotes.delete'))
                                            <li @click="removeQuote(quote)">
                                                @lang('admin::app.leads.view.quotes.remove') }}
                                            </li>
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

            },
        });
    </script>
@endPushOnce