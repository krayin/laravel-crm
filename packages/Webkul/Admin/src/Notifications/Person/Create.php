<?php

namespace Webkul\Admin\Notifications\Person;

use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Messages\MailMessage;

class Create extends Mailable
{
    /**
     * @param  object  $person
     * @return void
     */
    public function __construct($person)
    {
        $this->person = $person;
    }

    /**
     * Build the mail representation of the notification.
     */
    public function build()
    {
        $personEmails = \Arr::pluck($this->person->emails, 'value');

        return $this
            ->to($personEmails)
            ->subject(trans('admin::app.mail.person.create-subject'))
            ->view('admin::emails.persons.create', [
                'person_name' => $this->person->name,
            ]);
    }
}
