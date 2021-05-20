@extends('admin::layouts.master')

@section('page_title')
    {{ __('admin::app.dashboard.title') }}
@stop

@section('content-wrapper')
    <div class="content full-page dashboard">
        <h1>{{ __('admin::app.dashboard.title') }}</h1>

        <div class="row-grid-3">
            {{-- @foreach($cards as $card)
            @endforeach --}}

            <div class="card">
                <label>
                    {{ __('admin::app.leads.title') }}

                    <select>
                        <option>this month</option>
                    </select>
                </label>

                <bar-chart id="lead-chart" data="{{ json_encode($leadData) }}"></bar-chart>
            </div>

            <div class="card">
                <label>
                    {{ __('admin::app.dashboard.activity') }}

                    <select>
                        <option>Today</option>
                    </select>
                </label>

                <h3>10 Activities 2 New Leads</h3>

                @foreach($activityData['data'] as $data)
                    @php($percent = ($data['value'] * 100) / $activityData['total'])

                    <div class="activity bar-data">
                        <span>{{ $data['label'] }}</span>
                        <div class="bar"><div class="primary" style="width: {{ $percent }}%;"></div></div>
                        <span>{{ $data['value'] }}/{{ $activityData['total'] }}</span>
                    </div>
                @endforeach
            </div>

            <div class="card">
                <label>
                    {{ __('admin::app.dashboard.top_deals') }}

                    <select>
                        <option>Today</option>
                    </select>
                </label>

                @foreach($dealData["data"] as $data)
                    <div class="deal">
                        <label>{{ $data['label'] }}</label>

                        <div class="details">
                            <span>{{ $data['amount'] }}</span>
                            <span>{{ $data['created_at'] }}</span>
                            <span>
                                @if ($data['status'] == 1)
                                    <span class="badge badge-round badge-primary"></span>
                                @elseif ($data['status'] == 2)
                                    <span class="badge badge-round badge-warning"></span>
                                @elseif ($data['status'] == 3)
                                    <span class="badge badge-round badge-success"></span>
                                @endif

                                {{ $data['statusLabel'] }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="card">
                <label>
                    {{ __('admin::app.dashboard.stages') }}

                    <select>
                        <option>Today</option>
                    </select>
                </label>

                @foreach($leadStages['data'] as $data)
                    @php($percent = ($data['value'] * 100) / $activityData['total'])

                    <div class="bar-data">
                        <span>{{ $data['label'] }}</span>
                        <div class="bar"><div class="{{ $data['bar_type'] }}" style="width: {{ $percent }}%;"></div></div>
                        <span>{{ $data['value'] }}/{{ $activityData['total'] }}</span>
                    </div>
                @endforeach
            </div>

            <div class="card">
                <label>
                    {{ __('admin::app.dashboard.emails') }}

                    <select>
                        <option>Monthly</option>
                    </select>
                </label>

                @foreach($emailData["data"] as $data)
                    <div class="email-data">
                        <span>{{ $data['count'] }}</span>
                        <span>{{ $data['label'] }}</span>
                    </div>
                @endforeach
            </div>

            <div class="dashed card">
                <div class="custom-card">+</div>
                <div class="custom-card">{{ __('admin::app.dashboard.custom_card') }}</div>
            </div>
        </div>
    </div>
@stop