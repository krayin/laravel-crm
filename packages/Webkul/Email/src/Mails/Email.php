<?php

namespace Webkul\Email\Mails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Mime\Email as Emails;

class Email extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The email instance.
     *
     * @var  \Webkul\Email\Contracts\Email  $email
     */
    public $email;

    /**
     * Create a new email instance.
     *
     * @param  \Webkul\Email\Contracts\Email  $email
     * @return void
     */
    public function __construct($email)
    {
        $this->email = $email;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->from($this->email->from)
            ->to($this->email->reply_to)
            ->replyTo($this->email->parent_id ? $this->email->parent->unique_id : $this->email->unique_id)
            ->cc($this->email->cc ?? [])
            ->bcc($this->email->bcc ?? [])
            ->subject($this->email->parent_id ? $this->email->parent->subject : $this->email->subject)
            ->html($this->email->reply);

        foreach ($this->email->attachments as $attachment) {
            $this->attachFromStorage($attachment->path);
        }

        return $this;
    }
}