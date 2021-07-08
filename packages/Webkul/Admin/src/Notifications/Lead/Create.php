<?php

namespace Webkul\Admin\Notifications\Lead;

use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Messages\MailMessage;

class Create extends Mailable
{
    /**
     * @param  object  $user
     * @param  Number  $leadId
     * @return void
     */
    public function __construct($user, $leadId)
    {
        $this->user = $user;
        $this->leadId = $leadId;
    }

    /**
     * Build the mail representation of the notification.
     */
    public function build()
    {
        return $this
                ->to($this->user->email)
                ->subject(trans('admin::app.mail.lead.create-subject'))
                ->view('admin::emails.leads.create', [
                    'user_name' => $this->user->name,
                    'lead_id'   => $this->leadId,
                ]);
    }
}
