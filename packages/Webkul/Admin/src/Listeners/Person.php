<?php

namespace Webkul\Admin\Listeners;

use Webkul\Admin\Traits\ScopesMailAccess;
use Webkul\Email\Repositories\EmailRepository;

class Person
{
    use ScopesMailAccess;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(protected EmailRepository $emailRepository) {}

    /**
     * @param  \Webkul\Contact\Models\Person  $person
     * @return void
     */
    public function linkToEmail($person)
    {
        if (! $email = $this->linkableRequestedEmail($person->user_id)) {
            return;
        }

        $this->emailRepository->update([
            'person_id' => $person->id,
        ], $email->id);
    }
}
