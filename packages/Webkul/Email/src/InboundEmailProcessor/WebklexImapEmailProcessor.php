<?php

namespace Webkul\Email\InboundEmailProcessor;

use Webklex\IMAP\Facades\Client;
use Webkul\Email\InboundEmailProcessor\Contracts\InboundEmailProcessor;
use Webkul\Email\Repositories\AttachmentRepository;
use Webkul\Email\Repositories\EmailRepository;

class WebklexImapEmailProcessor implements InboundEmailProcessor
{
    /**
     * Folder name.
     */
    protected const FOLDER_NAME = 'INBOX';

    /**
     * Create a new repository instance.
     *
     * @return void
     */
    public function __construct(
        protected EmailRepository $emailRepository,
        protected AttachmentRepository $attachmentRepository
    ) {}

    /**
     * Get the messages from the mail server.
     */
    public function getMessages()
    {
        try {
            $client = Client::account('default');

            $client->connect();

            if (! $client->isConnected()) {
                throw new \Exception('Failed to connect to the mail server.');
            }

            $folder = $client->getFolder(self::FOLDER_NAME);

            $messages = $folder->query()->since(now()->subDays(10))->get();

            $client->disconnect();

            return $messages;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Process the inbound email.
     */
    public function process($message = null): void
    {
        $attributes = $message->getAttributes();

        $messageId = $attributes['message_id']->first();

        $email = $this->emailRepository->findOneByField('message_id', $messageId);

        if ($email) {
            return;
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

        $email = $this->emailRepository->create([
            'from'          => $attributes['from']->first()->mail,
            'subject'       => $attributes['subject']->first(),
            'name'          => $attributes['subject']->first(),
            'reply'         => $message->bodies['html'] ?? $message->bodies['text'],
            'is_read'       => 0,
            'folders'       => [strtolower('inbox')],
            'reply_to'      => $replyToEmails,
            'cc'            => [],
            'bcc'           => [],
            'source'        => 'email',
            'user_type'     => 'person',
            'unique_id'     => $messageId,
            'message_id'    => $messageId,
            'reference_ids' => $references,
            'created_at'    => $attributes['date']->first(),
            'parent_id'     => $parentId,
        ]);

        if ($message->hasAttachments()) {
            $this->attachmentRepository->uploadAttachments($email, [
                'source'      => 'email',
                'attachments' => $message->getAttachments(),
            ]);
        }
    }
}
