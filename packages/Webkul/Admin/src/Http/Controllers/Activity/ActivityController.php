<?php

namespace Webkul\Admin\Http\Controllers\Activity;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Webkul\Activity\Repositories\ActivityRepository;
use Webkul\Activity\Repositories\FileRepository;
use Webkul\Admin\DataGrids\Activity\ActivityDataGrid;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Attribute\Repositories\AttributeRepository;
use Webkul\Admin\Http\Resources\ActivityResource;

class ActivityController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        protected ActivityRepository $activityRepository,
        protected FileRepository $fileRepository,
        protected AttributeRepository $attributeRepository,
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view('admin::activities.index');
    }

    /**
     * Returns a listing of the resource.
     */
    public function get(): JsonResponse
    {
        if (! request()->has('view_type')) {
            return datagrid(ActivityDataGrid::class)->process();
        }

        $startDate = request()->get('startDate')
            ? Carbon::createFromTimeString(request()->get('startDate').' 00:00:01')
            : Carbon::now()->startOfWeek()->format('Y-m-d H:i:s');

        $endDate = request()->get('endDate')
            ? Carbon::createFromTimeString(request()->get('endDate').' 23:59:59')
            : Carbon::now()->endOfWeek()->format('Y-m-d H:i:s');

        $activities = $this->activityRepository->getActivities([$startDate, $endDate])->toArray();

        return response()->json([
            'activities' => $activities,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(): RedirectResponse|JsonResponse
    {
        $this->validate(request(), [
            'type'          => 'required',
            'comment'       => 'required_if:type,note',
            'schedule_from' => 'required_unless:type,note,file',
            'schedule_to'   => 'required_unless:type,note,file',
            'file'          => 'required_if:type,file'
        ]);

        /**
         * TODO
         * 
         * Check if meeting is overlapping with other meetings 
         */

        Event::dispatch('activity.create.before');

        $activity = $this->activityRepository->create(array_merge(request()->all(), [
            'is_done' => request('type') == 'note' ? 1 : 0,
            'user_id' => auth()->guard('user')->user()->id,
        ]));

        Event::dispatch('activity.create.after', $activity);

        if (request()->ajax()) {
            return response()->json([
                'data'    => new ActivityResource($activity),
                'message' => trans('admin::app.activities.create-success'),
            ]);
        }

        session()->flash('success', trans('admin::app.activities.create-success'));

        return redirect()->back();
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): View
    {
        $activity = $this->activityRepository->findOrFail($id);

        $leadId = old('lead_id') ?? optional($activity->leads()->first())->id;

        $lookUpEntityData = $this->attributeRepository->getLookUpEntity('leads', $leadId);

        return view('admin::activities.edit', compact('activity', 'lookUpEntityData'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id): RedirectResponse|JsonResponse
    {
        Event::dispatch('activity.update.before', $id);

        $activity = $this->activityRepository->update(request()->all(), $id);

        Event::dispatch('activity.update.after', $activity);

        if (request()->ajax()) {
            return response()->json([
                'data'    => new ActivityResource($activity),
                'message' => trans('admin::app.activities.update-success'),
            ]);
        }

        session()->flash('success', trans('admin::app.activities.update-success'));

        return redirect()->route('admin.activities.index');
    }

    /**
     * Mass Update the specified resources.
     */
    public function massUpdate(): JsonResponse
    {
        foreach (request()->input('rows') as $activityId) {
            Event::dispatch('activity.update.before', $activityId);

            $activity = $this->activityRepository->update([
                'is_done' => request()->input('value'),
            ], $activityId);

            Event::dispatch('activity.update.after', $activity);
        }

        return response()->json([
            'message' => trans('admin::app.activities.mass-update-success'),
        ]);
    }

    /**
     * Download file from storage.
     */
    public function download(int $id): StreamedResponse
    {
        $file = $this->fileRepository->findOrFail($id);

        return Storage::download($file->path);
    }

    /*
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $activity = $this->activityRepository->findOrFail($id);

        try {
            Event::dispatch('activity.delete.before', $id);

            $this->activityRepository->delete($id);

            Event::dispatch('activity.delete.after', $id);

            return response()->json([
                'message' => trans('admin::app.activities.destroy-success'),
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => trans('admin::app.activities.destroy-failed'),
            ], 400);
        }
    }

    /**
     * Mass Delete the specified resources.
     */
    public function massDestroy(): JsonResponse
    {
        foreach (request('rows') as $activityId) {
            Event::dispatch('activity.delete.before', $activityId);

            $this->activityRepository->delete($activityId);

            Event::dispatch('activity.delete.after', $activityId);
        }

        return response()->json([
            'message' => trans('admin::app.response.destroy-success'),
        ]);
    }
}
