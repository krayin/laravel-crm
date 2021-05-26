<?php

namespace Webkul\Email\Repositories;

use Illuminate\Container\Container;
use Webkul\Core\Eloquent\Repository;
use Illuminate\Support\Facades\Mail;
use Webkul\Email\Mails\Email;

class ThreadRepository extends Repository
{
    /**
     * AttachmentRepository object
     *
     * @var \Webkul\Attribute\Repositories\AttachmentRepository
     */
    protected $attachmentRepository;

    /**
     * Parser object
     *
     * @var \Webkul\Email\Helpers\Parser
     */
    protected $emailParser;

    /**
     * Create a new repository instance.
     *
     * @param  \Webkul\Attribute\Repositories\AttachmentRepository  $attachmentRepository
     * @param  \Illuminate\Container\Container  $container
     * @return void
     */
    public function __construct(
        AttachmentRepository $attachmentRepository,
        Container $container
    )
    {
        $this->attachmentRepository = $attachmentRepository;

        parent::__construct($container);
    }

    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'Webkul\Email\Contracts\Thread';
    }

    /**
     * @param  \Webkul\Email\Helpers\Parser  $emailParser
     * @return self
     */
    public function setEmailParser($emailParser)
    {
        $this->emailParser = $emailParser;

        return $this;
    }

    /**
     * @param  array  $data
     * @return \Webkul\Email\Contracts\Thread
     */
    public function create(array $data)
    {
        $thread = parent::create(array_merge($this->sanitizeEmails($data), [
            'message_id' => $data['message_id'] ?? time() . '@example.com',
        ]));

        $this->attachmentRepository->setEmailParser($this->emailParser)->uploadAttachments($thread, $data);

        Mail::send(new Email($thread));

        return $thread;
    }

    /**
     * @param  array  $data
     * @return array
     */
    public function sanitizeEmails(array $data)
    {
        $data['reply_to'] = array_values(array_filter($data['reply_to']));

        $data['cc'] = array_values(array_filter($data['cc']));

        $data['bcc'] = array_values(array_filter($data['bcc']));

        return $data;
    }
}