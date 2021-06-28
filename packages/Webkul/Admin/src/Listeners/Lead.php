<?php

namespace Webkul\Admin\Listeners;

use Webkul\Email\Repositories\EmailRepository;

class Lead
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
     * @param  \Webkul\Lead\Models\Lead  $lead
     * @return void
     */
    public function linkToEmail($lead)
    {
        if (! request('email_id')) {
            return;
        }

        $this->emailRepository->update([
            'lead_id' => $lead->id,
        ], request('email_id'));
    }
}