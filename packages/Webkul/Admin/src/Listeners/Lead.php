<?php

namespace Webkul\Admin\Listeners;

use Webkul\Admin\Traits\ScopesMailAccess;
use Webkul\Email\Repositories\EmailRepository;

class Lead
{
    use ScopesMailAccess;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(protected EmailRepository $emailRepository) {}

    /**
     * File the mail named by the request's `email_id` under the new lead.
     *
     * @param  \Webkul\Lead\Models\Lead  $lead
     * @return void
     */
    public function linkToEmail($lead)
    {
        if (! $email = $this->linkableRequestedEmail($lead->user_id)) {
            return;
        }

        $this->emailRepository->update([
            'lead_id' => $lead->id,
        ], $email->id);
    }
}
