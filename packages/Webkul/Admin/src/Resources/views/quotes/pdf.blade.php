<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
    <head>
        <meta http-equiv="Cache-control" content="no-cache">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

        <style type="text/css">
            * {
                font-family: DejaVu Sans;
            }

            body, th, td, h5 {
                font-size: 12px;
                color: #000;
            }

            .container {
                padding: 20px;
                display: block;
            }

            .quote-summary {
                margin-bottom: 20px;
            }

            .table {
                margin-top: 20px;
            }

            .table table {
                width: 100%;
                border-collapse: collapse;
                text-align: left;
            }

            .table thead th {
                font-weight: 700;
                border-top: solid 1px #d3d3d3;
                border-bottom: solid 1px #d3d3d3;
                border-left: solid 1px #d3d3d3;
                padding: 5px 10px;
                background: #F4F4F4;
            }

            .table thead th:last-child {
                border-right: solid 1px #d3d3d3;
            }

            .table tbody td {
                padding: 5px 10px;
                border-bottom: solid 1px #d3d3d3;
                border-left: solid 1px #d3d3d3;
                color: #3A3A3A;
                vertical-align: middle;
            }

            .table tbody td p {
                margin: 0;
            }

            .table tbody td:last-child {
                border-right: solid 1px #d3d3d3;
            }

           .sale-summary {
                margin-top: 40px;
                float: right;
            }

            .sale-summary tr td {
                padding: 3px 5px;
            }

            .sale-summary tr.bold {
                font-weight: 600;
            }

            .label {
                color: #000;
                font-weight: bold;
            }

            .logo {
                height: 70px;
                width: 70px;
            }

            .text-center {
                text-align: center;
            }
        </style>
    </head>

    <body style="background-image: none; background-color: #fff;">
        <div class="container">

            <div class="header">
                <div class="row">
                    <div class="col-12">
                        <h1 class="text-center">{{ __('admin::app.quotes.quote') }}</h1>
                    </div>
                </div>

                <div class="image">
                    {{-- <img class="logo" src="{{ Storage::url(core()->getConfigData('sales.orderSettings.quote_slip_design.logo')) }}"/> --}}
                </div>
            </div>

            <div class="quote-summary">
                <div class="row">
                    <span class="label">{{ __('admin::app.quotes.quote-id') }} -</span>
                    <span class="value">#{{ $quote->id }}</span>
                </div>

                <div class="row">
                    <span class="label">{{ __('admin::app.quotes.quote-date') }} -</span>
                    <span class="value">{{ $quote->created_at->format('d-m-Y') }}</span>
                </div>

                <div class="row">
                    <span class="label">{{ __('admin::app.quotes.valid-until') }} -</span>
                    <span class="value">{{ $quote->expired_at->format('d-m-Y') }}</span>
                </div>

                <div class="row">
                    <span class="label">{{ __('admin::app.quotes.sales-person') }} -</span>
                    <span class="value">{{ $quote->user->name }}</span>
                </div>

                <div class="table address">
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 50%">{{ __('admin::app.quotes.bill-to') }}</th>

                                @if ($quote->shipping_address)
                                    <th>{{ __('admin::app.quotes.ship-to') }}</th>
                                @endif
                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                                @if ($quote->billing_address)
                                    <td>
                                        <p>{{ $quote->billing_address['address'] }}</p>
                                        <p>{{ $quote->billing_address['postcode'] . ' ' .$quote->billing_address['city'] }} </p>
                                        <p>{{ $quote->billing_address['state'] }}</p>
                                        <p>{{ core()->country_name($quote->billing_address['country']) }}</p>
                                    </td>
                                @endif

                                @if ($quote->shipping_address)
                                    <td>
                                        <p>{{ $quote->shipping_address['address'] }}</p>
                                        <p>{{ $quote->shipping_address['postcode'] . ' ' .$quote->shipping_address['city'] }} </p>
                                        <p>{{ $quote->shipping_address['state'] }}</p>
                                        <p>{{ core()->country_name($quote->shipping_address['country']) }}</p>
                                    </td>
                                @endif
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="table items">
                    <table>
                        <thead>
                            <tr>
                                <th>{{ __('admin::app.quotes.sku') }}</th>

                                <th>{{ __('admin::app.quotes.product-name') }}</th>

                                <th class="text-center">{{ __('admin::app.quotes.price') }}</th>

                                <th class="text-center">{{ __('admin::app.quotes.quantity') }}</th>

                                <th class="text-center">{{ __('admin::app.quotes.amount') }}</th>

                                <th class="text-center">{{ __('admin::app.quotes.discount') }}</th>

                                <th class="text-center">{{ __('admin::app.quotes.tax') }}</th>

                                <th class="text-center">{{ __('admin::app.quotes.grand-total') }}</th>
                            </tr>
                        </thead>

                        <tbody>

                            @foreach ($quote->items as $item)
                                <tr>
                                    <td>{{ $item->sku }}</td>

                                    <td>
                                        {{ $item->name }}
                                    </td>

                                    <td>{!! core()->formatBasePrice($item->price, true) !!}</td>

                                    <td class="text-center">{{ $item->quantity }}</td>

                                    <td class="text-center">{!! core()->formatBasePrice($item->total, true) !!}</td>

                                    <td class="text-center">{!! core()->formatBasePrice($item->discount_amount, true) !!}</td>

                                    <td class="text-center">{!! core()->formatBasePrice($item->tax_amount, true) !!}</td>
                                    
                                    <td class="text-center">{!! core()->formatBasePrice($item->total + $item->tax_amount, true) !!}</td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>


                <table class="sale-summary">
                    <tr>
                        <td>{{ __('admin::app.quotes.sub-total') }}</td>
                        <td>-</td>
                        <td>{!! core()->formatBasePrice($quote->sub_total, true) !!}</td>
                    </tr>

                    <tr>
                        <td>{{ __('admin::app.quotes.tax') }}</td>
                        <td>-</td>
                        <td>{!! core()->formatBasePrice($quote->tax_amount, true) !!}</td>
                    </tr>

                    <tr>
                        <td>{{ __('admin::app.quotes.discount') }}</td>
                        <td>-</td>
                        <td>{!! core()->formatBasePrice($quote->discount_amount, true) !!}</td>
                    </tr>

                    <tr>
                        <td>{{ __('admin::app.quotes.adjustment') }}</td>
                        <td>-</td>
                        <td>{!! core()->formatBasePrice($quote->adjustment_amount, true) !!}</td>
                    </tr>

                    <tr>
                        <td><strong>{{ __('admin::app.quotes.grand-total') }}</strong></td>
                        <td><strong>-</strong></td>
                        <td><strong>{!! core()->formatBasePrice($quote->grand_total, true) !!}</strong></td>
                    </tr>
                </table>

            </div>

        </div>
    </body>
</html>
