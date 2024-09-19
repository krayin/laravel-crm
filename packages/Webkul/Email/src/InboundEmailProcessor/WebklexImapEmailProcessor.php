<?php

namespace Webkul\Email\InboundEmailProcessor;

use Illuminate\Support\Facades\Storage;
use Webkul\Email\InboundEmailProcessor\Contracts\InboundEmailProcessor;
use Webkul\Email\Repositories\AttachmentRepository;
use Webkul\Email\Repositories\EmailRepository;

class WebklexImapEmailProcessor implements InboundEmailProcessor
{
    /**
     * Webklex client.
     */
    protected $client;

    /**
     * Create a new repository instance.
     *
     * @return void
     */
    public function __construct(
        protected EmailRepository $emailRepository,
        protected AttachmentRepository $attachmentRepository
    ) {
        $this->client = \Webklex\IMAP\Facades\Client::account('default');
    }

    /**
     * Process the inbound email.
     */
    public function process($content = null): void
    {
        $this->client->connect();

        $folders = $this->client->getFolders();

        foreach ($folders as $folder) {
            $messages = $folder->messages()->all()->get();

            foreach ($messages as $message) {
                $attributes = $message->getAttributes();

                $messageId = $attributes['message_id']->first();

                $email = $this->emailRepository->findOneByField('message_id', $messageId);

                if ($email) {
                    continue;
                }

                $references = [$messageId];

                $parentId = null;

                if (isset($attributes['references'])) {
                    array_push($references, ...$attributes['references']->all());

                    $parentId = $this->emailRepository->findOneByField('message_id', $attributes['references']->first())?->id;
                }

                $replyToEmails = [];

                foreach ($attributes['to']->all() as $to) {
                    $replyToEmails[] = $to->mail;
                }

                $email = $this->emailRepository->getModel();
                $email->subject = $attributes['subject']->first();
                $email->user_type = 'tenant';
                $email->name = $attributes['subject']->first();
                $email->reply = $message->bodies['html'] ?? $message->bodies['text'];
                $email->is_read = 0;
                $email->folders = [strtolower('inbox')];
                $email->from = $attributes['from']->first()->mail;
                $email->reply_to = $replyToEmails;
                $email->cc = [];
                $email->bcc = [];
                $email->unique_id = $messageId;
                $email->message_id = $messageId;
                $email->reference_ids = $references;
                $email->created_at = $attributes['date']->first();
                $email->parent_id = $parentId;
                $email->save();
            }

            $path = 'app/public/emails/'.$email->id;

            Storage::makeDirectory('emails/'.$email->id);

            if ($message->hasAttachments()) {
                $attachments = $message->getAttachments();

                foreach ($attachments as $attachment) {
                    $attachment->save(storage_path($path));

                    $attachment = [
                        'name'         => $attachment->getName(),
                        'path'         => 'emails/'.$email->id.'/'.$attachment->getName(),
                        'size'         => $attachment->getSize(),
                        'content_type' => $attachment->getContentType(),
                        'email_id'     => $email->id,
                    ];

                    $this->attachmentRepository->create($attachment);
                }
            }

            $this->client->disconnect();
        }
    }
}
