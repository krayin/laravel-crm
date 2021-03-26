<?php

namespace Webkul\Admin\Notifications\User;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Auth\Notifications\ResetPassword;

class UserResetPassword extends ResetPassword
{
    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable, $this->token);
        }

        return (new MailMessage)
            // ->from(core()->getSenderEmailDetails()['email'], core()->getSenderEmailDetails()['name'])
            ->view('admin::emails.users.forget-password', [
                'user_name' => $notifiable->name,
                'token'     => $this->token,
            ]);
    }
}
