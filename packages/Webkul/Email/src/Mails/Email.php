<?php

namespace Webkul\Email\Mails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Mime\Email as MimeEmail;

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

        $this->withSymfonyMessage(function (MimeEmail $message) {
            $message->getHeaders()->addIdHeader('Message-ID', $this->email->message_id);

            $message->getHeaders()->addTextHeader('References', $this->email->parent_id
                ? implode(' ', $this->email->parent->reference_ids)
                : implode(' ', $this->email->reference_ids)
            );
        });

        foreach ($this->email->attachments as $attachment) {
            $this->attachFromStorage($attachment->path);
        }

        return $this;
    }
}