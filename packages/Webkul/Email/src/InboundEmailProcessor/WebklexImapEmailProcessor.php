<?php

namespace Webkul\Email\InboundEmailProcessor;

use Webklex\IMAP\Facades\Client;
use Webkul\Email\Enums\SupportedFolderEnum;
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
        $this->client = Client::make($this->getDefaultConfigs());

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
     *
     * @param  ?\Webklex\PHPIMAP\Message  $message
     */
    public function processMessage($message = null): void
    {
        $attributes = $message->getAttributes();

        $messageId = $attributes['message_id']->first();

        $email = $this->emailRepository->findOneByField('message_id', $messageId);

        if ($email) {
            return;
        }

        $replyToEmails = $this->getEmailsByAttributeCode($attributes, 'to');

        foreach ($replyToEmails as $to) {
            if ($email = $this->emailRepository->findOneWhere(['message_id' => $to])) {
                break;
            }
        }

        if (! isset($email) && isset($attributes['in_reply_to'])) {
            $inReplyTo = $attributes['in_reply_to']->first();

            $email = $this->emailRepository->findOneWhere(['message_id' => $inReplyTo]);

            if (! $email) {
                $email = $this->emailRepository->findOneWhere([['reference_ids', 'like',  '%'.$inReplyTo.'%']]);
            }
        }

        $references = [$messageId];

        if (! isset($email) && isset($attributes['references'])) {
            array_push($references, ...$attributes['references']->all());

            foreach ($references as $reference) {
                if ($email = $this->emailRepository->findOneWhere([['reference_ids', 'like', '%'.$reference.'%']])) {
                    break;
                }
            }
        }

        /**
         * Maps the folder name to the supported folder in our application.
         *
         * To Do: Review this.
         */
        $folderName = match ($message->getFolder()->name) {
            'INBOX'     => SupportedFolderEnum::INBOX->value,
            'Important' => SupportedFolderEnum::IMPORTANT->value,
            'Starred'   => SupportedFolderEnum::STARRED->value,
            'Drafts'    => SupportedFolderEnum::DRAFT->value,
            'Sent Mail' => SupportedFolderEnum::SENT->value,
            'Trash'     => SupportedFolderEnum::TRASH->value,
            default     => '',
        };

        $parentEmail = null;

        if ($email) {
            $parentEmail = $this->emailRepository->update([
                'folders'       => array_unique(array_merge($email->folders, [$folderName])),
                'reference_ids' => array_merge($email->reference_ids ?? [], [$references]),
            ], $email->id);
        }

        $email = $this->emailRepository->create([
            'from'          => $attributes['from']->first()->mail,
            'subject'       => $attributes['subject']->first(),
            'name'          => $attributes['from']->first()->personal,
            'reply'         => $message->bodies['html'] ?? $message->bodies['text'],
            'is_read'       => (int) $message->flags()->has('seen'),
            'folders'       => [$folderName],
            'reply_to'      => $this->getEmailsByAttributeCode($attributes, 'to'),
            'cc'            => $this->getEmailsByAttributeCode($attributes, 'cc'),
            'bcc'           => $this->getEmailsByAttributeCode($attributes, 'bcc'),
            'source'        => 'email',
            'user_type'     => 'person',
            'unique_id'     => $messageId,
            'message_id'    => $messageId,
            'reference_ids' => $references,
            'created_at'    => $this->convertToDesiredTimezone($message->date->toDate()),
            'parent_id'     => $parentEmail?->id,
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

            if (in_array($folder->name, ['All Mail'])) {
                return;
            }

            return $folder->query()->since(now()->subDays(10))->get()->each(function ($message) {
                $this->processMessage($message);
            });
        });
    }

    /**
     * Get the emails by the attribute code.
     */
    protected function getEmailsByAttributeCode(array $attributes, string $attributeCode): array
    {
        $emails = [];

        if (isset($attributes[$attributeCode])) {
            $emails = collect($attributes[$attributeCode]->all())->map(fn ($attribute) => $attribute->mail)->toArray();
        }

        return $emails;
    }

    /**
     * Convert the date to the desired timezone.
     *
     * @param  \Carbon\Carbon  $carbonDate
     * @param  ?string  $targetTimezone
     */
    protected function convertToDesiredTimezone($carbonDate, $targetTimezone = null)
    {
        $targetTimezone = $targetTimezone ?: config('app.timezone');

        return $carbonDate->clone()->setTimezone($targetTimezone);
    }

    /**
     * Get the default configurations.
     */
    protected function getDefaultConfigs(): array
    {
        $defaultConfig = config('imap.accounts.default');

        $defaultConfig['host'] = core()->getConfigData('email.imap.account.host') ?: $defaultConfig['host'];

        $defaultConfig['port'] = core()->getConfigData('email.imap.account.port') ?: $defaultConfig['port'];

        $defaultConfig['encryption'] = core()->getConfigData('email.imap.account.encryption') ?: $defaultConfig['encryption'];

        $defaultConfig['validate_cert'] = (bool) core()->getConfigData('email.imap.account.validate_cert');

        $defaultConfig['username'] = core()->getConfigData('email.imap.account.username') ?: $defaultConfig['username'];

        $defaultConfig['password'] = core()->getConfigData('email.imap.account.password') ?: $defaultConfig['password'];

        return $defaultConfig;
    }
}
