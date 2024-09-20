<?php

namespace Webkul\Email\InboundEmailProcessor;

use Webklex\IMAP\Facades\Client;
use Webkul\Email\InboundEmailProcessor\Contracts\InboundEmailProcessor;
use Webkul\Email\Repositories\AttachmentRepository;
use Webkul\Email\Repositories\EmailRepository;

class WebklexImapEmailProcessor implements InboundEmailProcessor
{
    /**
     * The IMAP client instance.
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
        $this->client = Client::account('default');

        $this->client->connect();

        if (! $this->client->isConnected()) {
            throw new \Exception('Failed to connect to the mail server.');
        }
    }

    /**
     * Close the connection.
     */
    public function __destruct()
    {
        $this->client->disconnect();
    }

    /**
     * Process messages from all folders.
     */
    public function processMessagesFromAllFolders()
    {
        try {
            $rootFolders = $this->client->getFolders();

            $this->processMessagesFromLeafFolders($rootFolders);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Process the inbound email.
     */
    public function processMessage($message = null): void
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
            'name'          => $attributes['from']->first()->personal,
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

    /**
     * Process the messages from all folders.
     *
     * @param  \Webklex\IMAP\Support\FolderCollection  $rootFoldersCollection
     */
    protected function processMessagesFromLeafFolders($rootFoldersCollection = null): void
    {
        $rootFoldersCollection->each(function ($folder) {
            if (! $folder->children->isEmpty()) {
                $this->processMessagesFromLeafFolders($folder->children);

                return;
            }

            return $folder->query()->since(now()->subDays(10))->get()->each(function ($message) {
                $this->processMessage($message);
            });
        });
    }
}
