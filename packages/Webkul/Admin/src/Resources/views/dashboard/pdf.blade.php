<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html
    lang="{{ $locale = app()->getLocale() }}"
    dir="{{ ($isRTL = in_array($locale, ['fa', 'ar'])) ? 'rtl' : 'ltr' }}"
>
    <head>
        <!-- meta tags -->
        <meta
            http-equiv="Cache-control"
            content="no-cache"
        >

        <meta
            http-equiv="Content-Type"
            content="text/html; charset=utf-8"
        />

        @php
            if ($locale == 'en') {
                $fontFamily = [
                    'regular' => 'DejaVu Sans',
                    'bold' => 'DejaVu Sans',
                ];
            } else {
                $fontFamily = [
                    'regular' => 'Arial, sans-serif',
                    'bold' => 'Arial, sans-serif',
                ];
            }

            if (in_array($locale, ['ar', 'fa', 'tr'])) {
                $fontFamily = [
                    'regular' => 'DejaVu Sans',
                    'bold' => 'DejaVu Sans',
                ];
            }
        @endphp

        <style type="text/css">
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
                font-family: {{ $fontFamily['regular'] }};
            }

            body {
                font-size: 11px;
                color: #1F2937;
                font-family: "{{ $fontFamily['regular'] }}";
            }

            b, th {
                font-family: "{{ $fontFamily['bold'] }}";
            }

            .page {
                padding: 32px;
            }

            .header-row {
                position: relative;
            }

            .logo {
                position: absolute;
                top: 0;
                right: 0;
                max-width: 140px;
                max-height: 50px;
            }

            .logo.rtl {
                right: auto;
                left: 0;
            }

            .report-title {
                font-size: 24px;
                font-weight: 700;
                color: #0F172A;
                margin-bottom: 6px;
            }

            .report-subtitle {
                font-size: 12px;
                color: #6B7280;
            }

            .divider {
                border-bottom: 1px solid #E5E7EB;
                margin: 16px 0 24px;
            }

            .section-title {
                font-size: 15px;
                font-weight: 700;
                color: #111827;
                margin: 0 0 10px;
            }

            .section {
                margin-bottom: 24px;
            }

            .table-wrapper {
                border: 1px solid #E5E7EB;
                border-radius: 6px;
            }

            table {
                width: 100%;
                border-collapse: collapse;
            }

            table.rtl thead tr th,
            table.rtl tbody tr td {
                text-align: right !important;
            }

            table thead th {
                background-color: #F8FAFC;
                color: #374151;
                font-size: 11px;
                font-weight: 700;
                text-align: left !important;
                padding: 10px 16px;
                border-bottom: 1px solid #E5E7EB;
            }

            table tbody td {
                padding: 10px 16px;
                font-size: 11px;
                color: #1F2937;
                text-align: left !important;
                border-bottom: 1px solid #F1F5F9;
            }

            table tbody tr:last-child td {
                border-bottom: none;
            }
        </style>
    </head>

    <body dir="{{ $isRTL ? 'rtl' : 'ltr' }}">
        <div class="page">
            <!-- Header -->
            <div class="header-row">
                @if ($logo)
                    <img
                        class="logo {{ $isRTL ? 'rtl' : '' }}"
                        src="{{ $logo }}"
                    />
                @endif

                <div class="report-title">
                    @lang('admin::app.dashboard.index.pdf.title')
                </div>

                <div class="report-subtitle">
                    @lang('admin::app.dashboard.index.pdf.filtered-date'): {{ $startDate->format('d M Y') }} - {{ $endDate->format('d M Y') }}
                </div>
            </div>

            <div class="divider"></div>

            <!-- Revenue -->
            <div class="section">
                <div class="section-title">
                    @lang('admin::app.dashboard.index.pdf.revenue')
                </div>

                <div class="table-wrapper">
                    <table class="{{ $isRTL ? 'rtl' : '' }}">
                        <thead>
                            <tr>
                                <th>@lang('admin::app.dashboard.index.pdf.metric')</th>
                                <th>@lang('admin::app.dashboard.index.pdf.current')</th>
                                <th>@lang('admin::app.dashboard.index.pdf.previous')</th>
                                <th>@lang('admin::app.dashboard.index.pdf.progress')</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                                <td>@lang('admin::app.dashboard.index.revenue.won-revenue')</td>
                                <td>{{ $revenueStats['total_won_revenue']['formatted_total'] }}</td>
                                <td>{{ core()->formatBasePrice($revenueStats['total_won_revenue']['previous']) }}</td>
                                <td>{{ number_format($revenueStats['total_won_revenue']['progress'], 2) }}%</td>
                            </tr>

                            <tr>
                                <td>@lang('admin::app.dashboard.index.revenue.lost-revenue')</td>
                                <td>{{ $revenueStats['total_lost_revenue']['formatted_total'] }}</td>
                                <td>{{ core()->formatBasePrice($revenueStats['total_lost_revenue']['previous']) }}</td>
                                <td>{{ number_format($revenueStats['total_lost_revenue']['progress'], 2) }}%</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Overview -->
            <div class="section">
                <div class="section-title">
                    @lang('admin::app.dashboard.index.pdf.overview')
                </div>

                <div class="table-wrapper">
                    <table class="{{ $isRTL ? 'rtl' : '' }}">
                        <thead>
                            <tr>
                                <th>@lang('admin::app.dashboard.index.pdf.metric')</th>
                                <th>@lang('admin::app.dashboard.index.pdf.current')</th>
                                <th>@lang('admin::app.dashboard.index.pdf.previous')</th>
                                <th>@lang('admin::app.dashboard.index.pdf.progress')</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                                <td>@lang('admin::app.dashboard.index.over-all.total-leads')</td>
                                <td>{{ $overAllStats['total_leads']['current'] }}</td>
                                <td>{{ $overAllStats['total_leads']['previous'] }}</td>
                                <td>{{ number_format($overAllStats['total_leads']['progress'], 2) }}%</td>
                            </tr>

                            <tr>
                                <td>@lang('admin::app.dashboard.index.over-all.average-lead-value')</td>
                                <td>{{ $overAllStats['average_lead_value']['formatted_total'] }}</td>
                                <td>{{ core()->formatBasePrice($overAllStats['average_lead_value']['previous']) }}</td>
                                <td>{{ number_format($overAllStats['average_lead_value']['progress'], 2) }}%</td>
                            </tr>

                            <tr>
                                <td>@lang('admin::app.dashboard.index.over-all.average-leads-per-day')</td>
                                <td>{{ number_format($overAllStats['average_leads_per_day']['current'], 2) }}</td>
                                <td>{{ number_format($overAllStats['average_leads_per_day']['previous'], 2) }}</td>
                                <td>{{ number_format($overAllStats['average_leads_per_day']['progress'], 2) }}%</td>
                            </tr>

                            <tr>
                                <td>@lang('admin::app.dashboard.index.over-all.total-quotations')</td>
                                <td>{{ $overAllStats['total_quotations']['current'] }}</td>
                                <td>{{ $overAllStats['total_quotations']['previous'] }}</td>
                                <td>{{ number_format($overAllStats['total_quotations']['progress'], 2) }}%</td>
                            </tr>

                            <tr>
                                <td>@lang('admin::app.dashboard.index.over-all.total-persons')</td>
                                <td>{{ $overAllStats['total_persons']['current'] }}</td>
                                <td>{{ $overAllStats['total_persons']['previous'] }}</td>
                                <td>{{ number_format($overAllStats['total_persons']['progress'], 2) }}%</td>
                            </tr>

                            <tr>
                                <td>@lang('admin::app.dashboard.index.over-all.total-organizations')</td>
                                <td>{{ $overAllStats['total_organizations']['current'] }}</td>
                                <td>{{ $overAllStats['total_organizations']['previous'] }}</td>
                                <td>{{ number_format($overAllStats['total_organizations']['progress'], 2) }}%</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Total Leads -->
            <div class="section">
                <div class="section-title">
                    @lang('admin::app.dashboard.index.pdf.total-leads')
                </div>

                <div class="table-wrapper">
                    <table class="{{ $isRTL ? 'rtl' : '' }}">
                        <thead>
                            <tr>
                                <th>@lang('admin::app.dashboard.index.pdf.date')</th>
                                <th>@lang('admin::app.dashboard.index.pdf.all')</th>
                                <th>@lang('admin::app.dashboard.index.pdf.won')</th>
                                <th>@lang('admin::app.dashboard.index.pdf.lost')</th>
                            </tr>
                        </thead>

                        <tbody>
                            @php
                                $allOverTime = $totalLeadsStats['all']['over_time'] ?? [];

                                $wonOverTime = $totalLeadsStats['won']['over_time'] ?? [];

                                $lostOverTime = $totalLeadsStats['lost']['over_time'] ?? [];
                            @endphp

                            @forelse ($allOverTime as $index => $interval)
                                <tr>
                                    <td>{{ $interval['label'] }}</td>
                                    <td>{{ $interval['count'] }}</td>
                                    <td>{{ $wonOverTime[$index]['count'] ?? 0 }}</td>
                                    <td>{{ $lostOverTime[$index]['count'] ?? 0 }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4">-</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Revenue By Sources -->
            <div class="section">
                <div class="section-title">
                    @lang('admin::app.dashboard.index.pdf.revenue-by-sources')
                </div>

                <div class="table-wrapper">
                    <table class="{{ $isRTL ? 'rtl' : '' }}">
                        <thead>
                            <tr>
                                <th>@lang('admin::app.dashboard.index.pdf.source')</th>
                                <th>@lang('admin::app.dashboard.index.pdf.total')</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($revenueBySources as $source)
                                <tr>
                                    <td>{{ $source->name ?? '-' }}</td>
                                    <td>{{ core()->formatBasePrice($source->total) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2">-</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Revenue By Types -->
            <div class="section">
                <div class="section-title">
                    @lang('admin::app.dashboard.index.pdf.revenue-by-types')
                </div>

                <div class="table-wrapper">
                    <table class="{{ $isRTL ? 'rtl' : '' }}">
                        <thead>
                            <tr>
                                <th>@lang('admin::app.dashboard.index.pdf.type')</th>
                                <th>@lang('admin::app.dashboard.index.pdf.total')</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($revenueByTypes as $type)
                                <tr>
                                    <td>{{ $type->name ?? '-' }}</td>
                                    <td>{{ core()->formatBasePrice($type->total) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2">-</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Open Leads By Stages -->
            <div class="section">
                <div class="section-title">
                    @lang('admin::app.dashboard.index.pdf.open-leads-by-stages')
                </div>

                <div class="table-wrapper">
                    <table class="{{ $isRTL ? 'rtl' : '' }}">
                        <thead>
                            <tr>
                                <th>@lang('admin::app.dashboard.index.pdf.stage')</th>
                                <th>@lang('admin::app.dashboard.index.pdf.total')</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($openLeadsByStates as $stage)
                                <tr>
                                    <td>{{ $stage->name ?? '-' }}</td>
                                    <td>{{ $stage->total }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2">-</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Top Selling Products -->
            <div class="section">
                <div class="section-title">
                    @lang('admin::app.dashboard.index.pdf.top-selling-products')
                </div>

                <div class="table-wrapper">
                    <table class="{{ $isRTL ? 'rtl' : '' }}">
                        <thead>
                            <tr>
                                <th>@lang('admin::app.dashboard.index.pdf.product')</th>
                                <th>@lang('admin::app.dashboard.index.pdf.price')</th>
                                <th>@lang('admin::app.dashboard.index.pdf.total')</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($topSellingProducts as $product)
                                <tr>
                                    <td>{{ $product['name'] ?? '-' }}</td>
                                    <td>{{ $product['formatted_price'] }}</td>
                                    <td>{{ $product['formatted_revenue'] }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3">-</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Top Persons -->
            <div class="section">
                <div class="section-title">
                    @lang('admin::app.dashboard.index.pdf.top-persons')
                </div>

                <div class="table-wrapper">
                    <table class="{{ $isRTL ? 'rtl' : '' }}">
                        <thead>
                            <tr>
                                <th>@lang('admin::app.dashboard.index.pdf.person')</th>
                                <th>@lang('admin::app.dashboard.index.pdf.total')</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($topPersons as $person)
                                <tr>
                                    <td>{{ $person['name'] ?? '-' }}</td>
                                    <td>{{ $person['formatted_revenue'] }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2">-</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </body>
</html>
