<?php

namespace Webkul\Email\Repositories;

use Illuminate\Container\Container;
use Webkul\Core\Eloquent\Repository;
use Webkul\Email\Contracts\Email;

class EmailRepository extends Repository
{
    public function __construct(
        protected AttachmentRepository $attachmentRepository,
        Container $container
    ) {
        parent::__construct($container);
    }

    /**
     * Specify model class name.
     *
     * @return mixed
     */
    public function model()
    {
        return Email::class;
    }

    /**
     * Create.
     *
     * @return \Webkul\Email\Contracts\Email
     */
    public function create(array $data)
    {
        $uniqueId = time().'@'.config('mail.domain');

        $referenceIds = [];

        if (isset($data['parent_id'])) {
            $parent = parent::findOrFail($data['parent_id']);

            $referenceIds = $parent->reference_ids ?? [];
        }

        $data = $this->sanitizeEmails(array_merge([
            'source'        => 'web',
            'from'          => config('mail.from.address'),
            'user_type'     => 'admin',
            'folders'       => isset($data['is_draft']) ? ['draft'] : ['outbox'],
            'unique_id'     => $uniqueId,
            'message_id'    => $uniqueId,
            'reference_ids' => array_merge($referenceIds, [$uniqueId]),
        ], $data));

        $email = parent::create($data);

        $this->attachmentRepository->uploadAttachments($email, $data);

        return $email;
    }

    /**
     * Update.
     *
     * @param  int  $id
     * @param  string  $attribute
     * @return \Webkul\Email\Contracts\Email
     */
    public function update(array $data, $id, $attribute = 'id')
    {
        return parent::update($this->sanitizeEmails($data), $id);
    }

    /**
     * Sanitize emails.
     *
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
