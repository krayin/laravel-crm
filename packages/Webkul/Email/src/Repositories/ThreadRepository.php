<?php

namespace Webkul\Email\Repositories;

use Webkul\Core\Eloquent\Repository;
use Illuminate\Support\Facades\Mail;
use Webkul\Email\Mails\Email;

class ThreadRepository extends Repository
{
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
     * @param array $data
     * @return \Webkul\Product\Contracts\Product
     */
    public function create(array $data)
    {
        $thread = parent::create(array_merge($this->sanitizeEmails($data), [
            'message_id' => $data['message_id'] ?? time() . '@example.com',
        ]));

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