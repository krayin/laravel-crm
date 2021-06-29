<?php

namespace Webkul\Email\Repositories;

use Illuminate\Container\Container;
use Illuminate\Support\Facades\Event;
use Webkul\Email\Helpers\Parser;
use Webkul\Email\Helpers\Htmlfilter;
use Webkul\Core\Eloquent\Repository;

class EmailRepository extends Repository
{
    /**
     * AttachmentRepository object
     *
     * @var \Webkul\Email\Repositories\AttachmentRepository
     */
    protected $attachmentRepository;

    /**
     * Parser object
     *
     * @var \Webkul\Email\Helpers\Parser
     */
    protected $emailParser;

    /**
     * Htmlfilter object
     *
     * @var \Webkul\Email\Helpers\Htmlfilter
     */
    protected $htmlFilter;

    /**
     * Create a new repository instance.
     *
     * @param  \Webkul\Email\Repositories\AttachmentRepository  $attachmentRepository
     * @param  \Webkul\Email\Helpers\Parser  $emailParser
     * @param  \Webkul\Email\Helpers\Htmlfilter  $htmlFilter
     * @param  \Illuminate\Container\Container  $container
     * @return void
     */
    public function __construct(
        AttachmentRepository $attachmentRepository,
        Parser $emailParser,
        Htmlfilter $htmlFilter,
        Container $container
    )
    {
        $this->attachmentRepository = $attachmentRepository;

        $this->emailParser = $emailParser;

        $this->htmlFilter = $htmlFilter;

        parent::__construct($container);
    }

    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'Webkul\Email\Contracts\Email';
    }

    /**
     * @param  array  $data
     * @return \Webkul\Email\Contracts\Email
     */
    public function create(array $data)
    {
        $email = parent::create($this->sanitizeEmails($data));

        $this->attachmentRepository->setEmailParser($this->emailParser)->uploadAttachments($email, $data);

        return $email;
    }

    /**
     * @param array  $data
     * @param int    $id
     * @param string $attribute
     * @return \Webkul\Email\Contracts\Email
     */
    public function update(array $data, $id, $attribute = "id")
    {
        $email = $this->findOrFail($id);

        parent::update($this->sanitizeEmails($data), $id);

        $this->attachmentRepository->setEmailParser($this->emailParser)->uploadAttachments($email, $data);

        return $email;

    }

    /**
     * @param string $content
     * @return void
     */
    public function processInboundParseMail($content)
    {
        $this->emailParser->setText($content);

        $email = $this->findOneWhere(['message_id' => $this->emailParser->getHeader('message-id')]);

        if ($email) {
            return;
        }

        if (! $fromNameParts = mailparse_rfc822_parse_addresses($this->emailParser->getHeader('from'))) {
            $fromNameParts = mailparse_rfc822_parse_addresses($this->emailParser->getHeader('sender'));
        }

        $headers = [
            'from'          => current($this->parseEmailAddress('from')),
            'sender'        => $this->parseEmailAddress('sender'),
            'reply_to'      => $this->parseEmailAddress('to'),
            'cc'            => $this->parseEmailAddress('cc'),
            'bcc'           => $this->parseEmailAddress('bcc'),
            'subject'       => $this->emailParser->getHeader('subject'),
            'source'        => 'email',
            'name'          => $fromNameParts[0]['display'] == $fromNameParts[0]['address']
                               ? current(explode('@', $fromNameParts[0]['display']))
                               : $fromNameParts[0]['display'],
            'user_type'     => 'person',
            'message_id'    => $this->emailParser->getHeader('message-id') ?? time() . '@' . config('mail.domain'),
            'reference_ids' => htmlspecialchars_decode($this->emailParser->getHeader('references')),
            'in_reply_to'   => htmlspecialchars_decode($this->emailParser->getHeader('in-reply-to')),
        ];

        foreach ($headers['reply_to'] as $to) {
            if ($email = $this->findOneWhere(['message_id' => $to])) {
                break;
            }
        }

        if (! isset($email) && $headers['in_reply_to']) {
            $email = $this->findOneWhere(['message_id' => $headers['in_reply_to']]);

            if (! $email) {
                $email = $this->findOneWhere([['reference_ids', 'like',  '%' . $headers['in_reply_to'] . '%']]);
            }
        }
        
        if (! isset($email) && $headers['reference_ids']) {
            $referenceIds = explode(' ', $headers['reference_ids']);

            foreach ($referenceIds as $referenceId) {
                if ($email = $this->findOneWhere([['reference_ids', 'like', '%' . $referenceId . '%']])) {
                    break;
                }
            }
        }

        if (! $reply = $this->emailParser->getMessageBody('text')) {
            $reply = $this->emailParser->getTextMessageBody();
        }

        if (! isset($email)) {
            $email = $this->create(array_merge($headers, [
                'folders'       => ['inbox'],
                'reply'         => $reply, //$this->htmlFilter->HTMLFilter($reply, ''),
                'unique_id'     => time() . '@' . config('mail.domain'),
                'reference_ids' => [$headers['message_id']],
                'user_type'     => 'person',
            ]));
        } else {
            $this->update([
                'reference_ids' => array_merge($email->reference_ids ?? [], [$headers['message_id']]),
            ], $email->id);

            $this->create(array_merge($headers, [
                'reply'         => $this->htmlFilter->HTMLFilter($reply, ''),
                'parent_id'     => $email->id,
                'user_type'     => 'person',
            ]));
        }
    }

    /**
     * @param string $type
     * @return array
     */
    public function parseEmailAddress($type)
    {
        $emails = [];

        $addresses = mailparse_rfc822_parse_addresses($this->emailParser->getHeader($type));

        if (count($addresses) > 1) {
            foreach ($addresses as $address) {
                if (filter_var($address['address'], FILTER_VALIDATE_EMAIL)) {
                    $emails[] = $address['address'];
                }
            }
        } else if ($addresses) {
            $emails[] = $addresses[0]['address'];
        }

        return $emails;
    }

    /**
     * @param  array  $data
     * @return array
     */
    public function sanitizeEmails(array $data)
    {
        if (isset($data['reply_to'])) {
            $data['reply_to'] = array_values(array_filter($data['reply_to']));
        }

        if (isset($data['cc'])) {
            $data['cc'] = array_values(array_filter($data['cc']));
        }

        if (isset($data['bcc'])) {
            $data['bcc'] = array_values(array_filter($data['bcc']));
        }

        return $data;
    }
}