<?php

namespace Webkul\Email\Mails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Webkul\Email\Contracts\Thread;

class Email extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The thread instance.
     *
     * @var  \Webkul\Email\Contracts\Thread  $thread
     */
    public $thread;

    /**
     * Create a new thread instance.
     *
     * @param  \Webkul\Email\Contracts\Thread  $thread
     * @return void
     */
    public function __construct(Thread $thread)
    {
        $this->thread = $thread;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->to($this->thread->reply_to)
            ->replyTo($this->thread->email->message_id)
            ->cc($this->thread->cc)
            ->bcc($this->thread->bcc)
            ->subject($this->thread->email->subject)
            ->html($this->thread->reply);
        
        $this->withSwiftMessage(function ($message) {
            $message->getHeaders()->addTextHeader('Message-ID', $this->thread->message_id);

            $message->getHeaders()->addTextHeader('References', implode(' ', array_merge($this->thread->email->reference_ids ?? [], [
                $this->thread->message_id
            ])));
        });

        return $this;
    }
}