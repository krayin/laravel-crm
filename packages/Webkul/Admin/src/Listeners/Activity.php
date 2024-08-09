<?php

namespace Webkul\Admin\Listeners;

use Webkul\Contact\Repositories\PersonRepository;
use Webkul\Lead\Repositories\LeadRepository;
use Webkul\Warehouse\Repositories\WarehouseRepository;
use Webkul\Activity\Contracts\Activity as ActivityContract;

class Activity
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        protected LeadRepository $leadRepository,
        protected PersonRepository $personRepository,
        protected WarehouseRepository $warehouseRepository
    ) {}

    /**
     * Link activity to lead or person.
     */
    public function afterUpdateOrCreate(ActivityContract $activity): void
    {
        if (request()->input('lead_id')) {
            $lead = $this->leadRepository->find(request()->input('lead_id'));

            if (! $lead->activities->contains($activity->id)) {
                $lead->activities()->attach($activity->id);
            }
        } else if(request()->input('person_id')) {
            $person = $this->personRepository->find(request()->input('person_id'));

            if (! $person->activities->contains($activity->id)) {
                $person->activities()->attach($activity->id);
            }
        } else if(request()->input('warehouse_id')) {
            $warehouse = $this->warehouseRepository->find(request()->input('warehouse_id'));

            if (! $warehouse->activities->contains($activity->id)) {
                $warehouse->activities()->attach($activity->id);
            }
        }
    }
}
