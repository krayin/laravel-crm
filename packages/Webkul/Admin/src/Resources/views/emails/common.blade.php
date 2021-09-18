@component('admin::emails.layouts.master')
    <div style="text-align: center;">
        <a href="{{ config('app.url') }}">
            <img src="{{ asset('vendor/webkul/admin/assets/images/logo.svg') }}" alt="{{ config('app.name') }}"/>
        </a>
    </div>

    <div style="padding: 30px;">
        <div style="font-size: 20px;color: #242424;line-height: 30px;margin-bottom: 34px;">
            <h1 style="font-size: 24px;">
                {{ __('admin::app.emails.common.dear', ['name' => $name ?? '']) }},
            </h1>

            {!! $body !!}
        </div>

        <div style="width: 100%;float: left;margin: 20px 0;">
            <hr style="border-top: #D6DEE1;width: 100px;float: left;"/>
        </div>

        <p style="font-size: 16px;">
            {!! __('admin::app.emails.common.cheers', ['app_name' => config('app.name')]) !!}
        </p>
    </div>
@endcomponent