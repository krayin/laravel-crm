<?php

namespace Webkul\Email\InboundEmailProcessor;

use Webkul\Email\Helpers\HtmlFilter;
use Webkul\Email\Helpers\Parser;
use Webkul\Email\InboundEmailProcessor\Contracts\InboundEmailProcessor;
use Webkul\Email\Repositories\AttachmentRepository;
use Webkul\Email\Repositories\EmailRepository;

class SendgridEmailProcessor implements InboundEmailProcessor
{
    /**
     * Create a new repository instance.
     *
     * @return void
     */
    public function __construct(
        protected EmailRepository $emailRepository,
        protected AttachmentRepository $attachmentRepository,
        protected Parser $emailParser,
        protected HtmlFilter $htmlFilter
    ) {}

    /**
     * Process the inbound email.
     */
    public function process($content = null): void
    {
        $this->emailParser->setText($content);

        $email = $this->emailRepository->findOneWhere(['message_id' => $messageID = $this->emailParser->getHeader('message-id')]);

        if ($email) {
            return;
        }

        $headers = [
            'from'          => $this->emailParser->parseEmailAddress('from'),
            'sender'        => $this->emailParser->parseEmailAddress('sender'),
            'reply_to'      => $this->emailParser->parseEmailAddress('to'),
            'cc'            => $this->emailParser->parseEmailAddress('cc'),
            'bcc'           => $this->emailParser->parseEmailAddress('bcc'),
            'subject'       => $this->emailParser->getHeader('subject'),
            'name'          => $this->emailParser->parseSenderName(),
            'source'        => 'email',
            'user_type'     => 'person',
            'message_id'    => $messageID ?? time().'@'.config('mail.domain'),
            'reference_ids' => htmlspecialchars_decode($this->emailParser->getHeader('references')),
            'in_reply_to'   => htmlspecialchars_decode($this->emailParser->getHeader('in-reply-to')),
        ];

        foreach ($headers['reply_to'] as $to) {
            if ($email = $this->emailRepository->findOneWhere(['message_id' => $to])) {
                break;
            }
        }

        if (! isset($email) && $headers['in_reply_to']) {
            $email = $this->emailRepository->findOneWhere(['message_id' => $headers['in_reply_to']]);

            if (! $email) {
                $email = $this->emailRepository->findOneWhere([['reference_ids', 'like',  '%'.$headers['in_reply_to'].'%']]);
            }
        }

        if (! isset($email) && $headers['reference_ids']) {
            $referenceIds = explode(' ', $headers['reference_ids']);

            foreach ($referenceIds as $referenceId) {
                if ($email = $this->emailRepository->findOneWhere([['reference_ids', 'like', '%'.$referenceId.'%']])) {
                    break;
                }
            }
        }

        if (! $reply = $this->emailParser->getMessageBody('text')) {
            $reply = $this->emailParser->getTextMessageBody();
        }

        if (! isset($email)) {
            $email = $this->emailRepository->create(array_merge($headers, [
                'folders'       => ['inbox'],
                'reply'         => $reply,
                'unique_id'     => time().'@'.config('mail.domain'),
                'reference_ids' => [$headers['message_id']],
                'user_type'     => 'person',
            ]));
        } else {
            $this->emailRepository->update([
                'folders'       => array_unique(array_merge($email->folders, ['inbox'])),
                'reference_ids' => array_merge($email->reference_ids ?? [], [$headers['message_id']]),
            ], $email->id);

            $this->emailRepository->create(array_merge($headers, [
                'reply'         => $this->htmlFilter->process($reply, ''),
                'parent_id'     => $email->id,
                'user_type'     => 'person',
            ]));
        }
    }
}
