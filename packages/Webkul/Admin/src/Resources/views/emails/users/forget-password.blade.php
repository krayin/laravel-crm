@component('admin::emails.layout')
    <div style="margin-bottom: 34px;">
        <p style="font-size: 16px;color: #5E5E5E;line-height: 24px;">
            @lang('admin::app.emails.common.user.forget-password.dear', ['username' => $user_name]), 👋
        </p>

        <p style="font-size: 16px;color: #5E5E5E;line-height: 24px;">
            @lang('admin::app.emails.common.user.forget-password.info')
        </p>

        <p style="text-align: center;padding: 20px 0;">
            <a 
                href="{{ route('admin.reset_password.create', $token) }}"
                style="padding: 10px 20px;background: #0D8DD5;color: #ffffff;text-transform: uppercase;text-decoration: none; font-size: 16px"
            >
                @lang('admin::app.emails.common.user.forget-password.reset-password')
            </a>
        </p>

        <p style="font-size: 16px;color: #5E5E5E;line-height: 24px;">
            @lang('admin::app.emails.common.user.forget-password.final-summary')
        </p>

        <p style="font-size: 16px;color: #5E5E5E;line-height: 24px;">
            @lang('admin::app.emails.common.user.forget-password.thanks')
        </p>
    </div>
@endcomponent