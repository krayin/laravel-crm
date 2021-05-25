<?php

namespace Webkul\Email\Repositories;

use Illuminate\Container\Container;
use Webkul\Core\Eloquent\Repository;

class EmailRepository extends Repository
{
    /**
     * ThreadRepository object
     *
     * @var \Webkul\Attribute\Repositories\ThreadRepository
     */
    protected $threadRepository;

    /**
     * Create a new repository instance.
     *
     * @param  \Webkul\Attribute\Repositories\ThreadRepository  $threadRepository
     * @param  \Illuminate\Container\Container  $container
     * @return void
     */
    public function __construct(
        ThreadRepository $threadRepository,
        Container $container
    )
    {
        $this->threadRepository = $threadRepository;

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
     * @param array $data
     * @return \Webkul\Product\Contracts\Product
     */
    public function create(array $data)
    {
        $email = parent::create(array_merge($data, [
            'message_id' => time() . '@example.com',
        ]));

        $thread = $this->threadRepository->create(array_merge($data, [
            'type'       => 'create',
            'message_id' => $email->message_id,
            'email_id'   => $email->id,
        ]));

        return $email;
    }
}