<?php

namespace Webkul\Admin\Notifications\User;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserUpdatePassword extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new admin instance.
     *
     * @param  \Webkul\User\Contracts\User  $user
     * @return void
     */
    public function __construct(public $user) {}

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(core()->getSenderEmailDetails()['email'], core()->getSenderEmailDetails()['name'])
            ->to($this->user->email, $this->user->name)
            ->subject(trans('shop::app.mail.update-password.subject'))
            ->view('shop::emails.users.update-password', ['user' => $this->user]);
    }
}
