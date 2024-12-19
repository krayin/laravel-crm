<?php

namespace Webkul\Admin\Notifications;

use Illuminate\Mail\Mailable;

class Common extends Mailable
{
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(public $data) {}

    /**
     * Build the mail representation of the notification.
     */
    public function build()
    {
        $message = $this
            ->to($this->data['to'])
            ->subject($this->data['subject'])
            ->view('admin::emails.common.index', [
                'body' => $this->data['body'],
            ]);

        if (isset($this->data['attachments'])) {
            foreach ($this->data['attachments'] as $attachment) {
                $message->attachData($attachment['content'], $attachment['name'], [
                    'mime' => $attachment['mime'],
                ]);
            }
        }

        return $message;
    }
}
