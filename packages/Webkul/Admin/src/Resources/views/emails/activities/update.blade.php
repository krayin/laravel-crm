@component('admin::emails.layouts.master')
    <div style="padding: 30px;">
        <a href="{{ config('app.url') }}">
            <img src="{{ asset('vendor/webkul/admin/assets/images/logo.svg') }}" alt="{{ config('app.name') }}"/>
        </a>
    </div>

    <div style="padding: 30px;">
        <div style="font-size: 20px;color: #263238;line-height: 30px;margin-bottom: 34px;">
            <h1 style="font-size: 24px;">
                {{ __('admin::app.emails.activities.dear', ['name' => $user->name]) }},
            </h1>

            <p style="font-size: 16px;color: #5E5E5E; ">
                {{ __('admin::app.emails.activities.update-info') }}
            </p>

            <h4 style="font-size: 16px; ">
                {{ __('admin::app.emails.activities.details') }}
            </h4>

            <div>
                <div style="padding: 10px 0;width: 100%;font-size: 16px;position: relative;display: inline-block;">
                    <div style="width: 120px;float: left;color: #546E7A;box-sizing: border-box;">{{ __('admin::app.emails.activities.title') }}</div>
                    <div style="float: left;padding-left: 10px;width: calc(100% - 120px);box-sizing: border-box;">
                        {{ $activity->title }}
                    </div>
                </div>

                <div style="padding: 10px 0;width: 100%;font-size: 16px;position: relative;display: inline-block;">
                    <div style="width: 120px;float: left;color: #546E7A;box-sizing: border-box;">{{ __('admin::app.emails.activities.type') }}</div>
                    <div style="float: left;padding-left: 10px;width: calc(100% - 120px);box-sizing: border-box;">
                        {{ __('admin::app.emails.activities.' . $activity->type) }}
                    </div>
                </div>

                <div style="padding: 10px 0;width: 100%;font-size: 16px;position: relative;display: inline-block;">
                    <div style="width: 120px;float: left;color: #546E7A;box-sizing: border-box;">{{ __('admin::app.emails.activities.date') }}</div>
                    <div style="float: left;padding-left: 10px;width: calc(100% - 120px);box-sizing: border-box;">
                        {{ $activity->schedule_from->format("D M d, Y H:i A") . ' to ' . $activity->schedule_to->format("D M d, Y H:i A") }}
                    </div>
                </div>

                <div style="padding: 10px 0;width: 100%;font-size: 16px;position: relative;display: inline-block;">
                    <div style="width: 120px;float: left;color: #546E7A;box-sizing: border-box;">{{ __('admin::app.emails.activities.participants') }}</div>
                    <div style="float: left;padding-left: 10px;width: calc(100% - 120px);box-sizing: border-box;">
                        <ul style="padding-left: 18px;margin: 0;">
                            @foreach ($activity->participants as $participant)
                                <li>{{ $participant->name }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            <div style="width: 100%;float: left;margin: 20px 0;">
                <hr style="border-top: #D6DEE1;width: 100px;float: left;"/>
            </div>

            <p style="font-size: 16px;">
                {!! __('admin::app.emails.activities.cheers', ['app_name' => config('app.name')]) !!}
            </p>
        </div>
    </div>
@endcomponent