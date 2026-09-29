<?php

namespace Webkul\Admin\Http\Controllers\Settings\Warehouse;

use Webkul\Activity\Repositories\ActivityRepository;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Admin\Traits\HandlesActivityTimeline;

class ActivityController extends Controller
{
    use HandlesActivityTimeline;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        protected ActivityRepository $activityRepository
    ) {}

    /**
     * The pivot linking activities to the warehouse.
     */
    protected function timelinePivot(): array
    {
        return ['table' => 'warehouse_activities', 'foreign' => 'warehouse_id'];
    }
}
