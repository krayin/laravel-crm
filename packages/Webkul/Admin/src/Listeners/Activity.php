<?php

namespace Webkul\Admin\Listeners;

use Webkul\Lead\Repositories\LeadRepository;

class Activity
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(protected LeadRepository $leadRepository) {}

    /**
     * @param  \Webkul\Activity\Contracts\Activity  $activity
     * @return void
     */
    public function afterUpdateOrCreate($activity)
    {
        if (! request()->input('lead_id')) {
            return;
        }

        $lead = $this->leadRepository->find(request()->input('lead_id'));

        if (! $lead->activities->contains($activity->id)) {
            $lead->activities()->attach($activity->id);
        }
    }
}
