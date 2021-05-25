<?php

namespace Webkul\Admin\Http\Controllers\Activity;

use Illuminate\Support\Facades\Event;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Lead\Repositories\ActivityRepository;

class ActivityController extends Controller
{
    /**
     * ActivityRepository object
     *
     * @var \Webkul\Lead\Repositories\ActivityRepository
     */
    protected $activityRepository;

    /**
     * Create a new controller instance.
     *
     * @param \Webkul\Lead\Repositories\ActivityRepository  $activityRepository
     *
     * @return void
     */
    public function __construct(ActivityRepository $activityRepository)
    {
        $this->activityRepository = $activityRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('admin::activities.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function store($id)
    {
        $this->validate(request(), [
            'type'          => 'required',
            'comment'       => 'required_if:type,note',
            'schedule_from' => 'required_unless:type,note',
            'schedule_to'   => 'required_unless:type,note',
        ]);

        Event::dispatch('leads.activity.create.before');

        $activity = $this->activityRepository->create(array_merge(request()->all(), [
            'is_done' => request('type') == 'note' ? 1 : 0,
            'user_id' => auth()->guard('user')->user()->id,
            'lead_id' => $id,
        ]));

        Event::dispatch('leads.activity.create.after', $activity);
        
        session()->flash('success', trans('admin::app.activities.create-success'));

        return redirect()->back();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        Event::dispatch('leads.activity.update.before');

        $activity = $this->activityRepository->update(request()->all(), $id);

        Event::dispatch('leads.activity.update.after', $activity);

        if (request()->ajax()) {
            return response()->json([
                'status'  => true,
                'message' => trans('admin::app.activities.update-success'),
            ]);
        } else {
            session()->flash('success', trans('admin::app.activities.update-success'));

            return redirect()->route('admin.products.index');
        }
    }

    /**
     * Send email
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function sendEmail($id)
    {
        dd(request()->all());
        $lead = $this->leadRepository->findOrFail($id);
        
    }

    /*
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->activityRepository->findOrFail($id);
        
        try {
            Event::dispatch('leads.activity.delete.before', $id);

            $this->activityRepository->delete($id);

            Event::dispatch('leads.activity.delete.after', $id);

            return response()->json([
                'status'    => true,
                'message'   => trans('admin::app.activities.destroy-success'),
            ], 200);
        } catch(\Exception $exception) {
            return response()->json([
                'status'  => false,
                'message' => trans('admin::app.activities.destroy-failed'),
            ], 400);
        }
    }
}