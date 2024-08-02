<?php

namespace Webkul\Admin\Http\Controllers\Lead;

use Webkul\Activity\Repositories\ActivityRepository;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Admin\Http\Resources\ActivityResource;

class ActivityController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(protected ActivityRepository $activityRepository) {}

    /**
     * Display a listing of the resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $activities = $this->activityRepository
            ->leftJoin('lead_activities', 'activities.id', '=', 'lead_activities.activity_id')
            ->where('lead_activities.lead_id', $id)
            ->get();

        return ActivityResource::collection($activities);
    }
}
