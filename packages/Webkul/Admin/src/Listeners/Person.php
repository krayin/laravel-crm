<?php

namespace Webkul\Admin\Listeners;

use Webkul\Email\Repositories\EmailRepository;

class Person
{
    /**
     * EmailRepository object
     *
     * @var \Webkul\Email\Repositories\EmailRepository
     */
    protected $emailRepository;

    /**
     * Create a new controller instance.
     *
     * @param \Webkul\Email\Repositories\EmailRepository  $emailRepository
     *
     * @return void
     */
    public function __construct(EmailRepository $emailRepository)
    {
        $this->emailRepository = $emailRepository;
    }

    /**
     * @param  \Webkul\Contact\Models\Person  $person
     * @return void
     */
    public function linkToEmail($person)
    {
        if (! request('email_id')) {
            return;
        }

        $this->emailRepository->update([
            'person_id' => $person->id,
        ], request('email_id'));
    }
}