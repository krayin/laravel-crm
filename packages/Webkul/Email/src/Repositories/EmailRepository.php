<?php

namespace Webkul\Email\Repositories;

use Illuminate\Container\Container;
use Illuminate\Support\Facades\Event;
use Webkul\Core\Eloquent\Repository;
use Webkul\Email\Helpers\Parser;

class EmailRepository extends Repository
{
    /**
     * ThreadRepository object
     *
     * @var \Webkul\Attribute\Repositories\ThreadRepository
     */
    protected $threadRepository;

    /**
     * Parser object
     *
     * @var \Webkul\Email\Helpers\Parser
     */
    protected $emailParser;

    /**
     * Create a new repository instance.
     *
     * @param  \Webkul\Attribute\Repositories\ThreadRepository  $threadRepository
     * @param  \Webkul\Email\Helpers\Parser  $emailParser
     * @param  \Illuminate\Container\Container  $container
     * @return void
     */
    public function __construct(
        ThreadRepository $threadRepository,
        Parser $emailParser,
        Container $container
    )
    {
        $this->threadRepository = $threadRepository;

        $this->emailParser = $emailParser;

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
        $email = parent::create(array_merge($data, [
            'message_id' => $data['message_id'] ?? time() . '@example.com',
        ]));

        $thread = $this->threadRepository->create(array_merge($data, [
            'type'       => 'create',
            'message_id' => $email->message_id,
            'email_id'   => $email->id,
        ]));

        return $email;
    }

    /**
     * @param string $content
     * @return void
     */
    public function processInboundParseMail($content)
    {
        $this->emailParser->setText($content);

        $headers = [
            'from'          => $this->parseEmailAddress('from'),
            'sender'        => $this->parseEmailAddress('sender'),
            'reply_to'      => $this->parseEmailAddress('to'),
            'cc'            => $this->parseEmailAddress('cc'),
            'bcc'           => $this->parseEmailAddress('bcc'),
            'subject'       => $this->emailParser->getHeader('subject'),
            'source'        => 'email',
            'name'          => $headers['from'] == $from[0]['display']
                               ? current(explode('@', $from[0]['display']))
                               : $from[0]['display'],
            'user_type'     => 'person',
            'message_id'    => $this->emailParser->getHeader('message-id') ?? time() . '@example.com',
            'reference_ids' => htmlspecialchars_decode($this->emailParser->getHeader('references')),
            'in_reply_to'   => htmlspecialchars_decode($this->emailParser->getHeader('in-reply-to')),
        ];

        foreach ($toAdress as $to) {
            if ($email = $this->findOneWhere(['message_id' => $to])) {
                break;
            }
        }

        if (! isset($email) && $headers['in_reply_to']) {
            $email = $this->threadRepository->findOneWhere([['reference_ids', 'like', $headers['in_reply_to']]]);
        }
        
        if (! isset($email) && $headers['reference_ids']) {
            $referenceIds = explode(' ', $headers['reference_ids']);

            foreach ($referenceIds as $referenceId) {
                if ($email = $this->threadRepository->findOneWhere([['reference_ids', 'like', $referenceId]])) {
                    break;
                }
            }
        }

        if (! isset($email)) {
            $email = $this->create(array_merge($headers, [
                'reference_ids' => $headers['message_id'],
            ]));

            $thread = $this->threadRepository->setEmailParser($this->emailParser)->create(array_merge($headers, [
                'type'          => 'create',
                'user_type'     => 'person',
                'reference_ids' => $headers['message_id'],
            ]));
        } else {
            $thread = $this->threadRepository->findOneWhere(['message_id' => $headers['message_id']]);

            if ($thread) {
                return;
            }

            // Create person or admin if both are note exists (Optional)

            $this->threadRepository->setEmailParser($this->emailParser)->create(array_merge($headers, [
                'type' => 'reply',
            ]));
        }
    }

    /**
     * @param string $type
     * @return array
     */
    public function parseEmailAddress($type)
    {
        $addresses = mailparse_rfc822_parse_addresses($this->emailParser->getHeader($type));

        if (count($addresses) <= 1) {
            return [$addresses[0]['address']];
        }

        $emails = [];

        foreach ($addresses as $address) {
            if (filter_var($address['address'], FILTER_VALIDATE_EMAIL)) {
                $emails[] = $address['address'];
            }
        }

        return $emails;
    }
}