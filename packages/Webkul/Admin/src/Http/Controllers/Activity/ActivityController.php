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
use Webkul\Contact\Repositories\PersonRepository;
use Webkul\Lead\Repositories\LeadRepository;
use Webkul\User\Repositories\UserRepository;

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
        protected LeadRepository $leadRepository,
        protected UserRepository $userRepository,
        protected PersonRepository $personRepository,
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
     * Check if activity duration is overlapping with another activity duration.
     */
    public function checkIfOverlapping(): JsonResponse
    {
        $isOverlapping = $this->activityRepository->isDurationOverlapping(
            request('schedule_from'),
            request('schedule_to'),
            request('participants'),
            request('id')
        );

        return response()->json([
            'overlapping' => $isOverlapping,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(): RedirectResponse
    {
        $this->validate(request(), [
            'type'          => 'required',
            'comment'       => 'required_if:type,note',
            'schedule_from' => 'required_unless:type,note',
            'schedule_to'   => 'required_unless:type,note',
        ]);

        Event::dispatch('activity.create.before');

        $activity = $this->activityRepository->create(array_merge(request()->all(), [
            'is_done' => request('type') == 'note' ? 1 : 0,
            'user_id' => auth()->guard('user')->user()->id,
        ]));

        if (request('participants')) {
            if (is_array(request('participants.users'))) {
                foreach (request('participants.users') as $userId) {
                    $activity->participants()->create([
                        'user_id' => $userId,
                    ]);
                }
            }

            if (is_array(request('participants.persons'))) {
                foreach (request('participants.persons') as $personId) {
                    $activity->participants()->create([
                        'person_id' => $personId,
                    ]);
                }
            }
        }

        if (request('lead_id')) {
            $lead = $this->leadRepository->find(request('lead_id'));

            $lead->activities()->attach($activity->id);
        }

        Event::dispatch('activity.create.after', $activity);

        session()->flash('success', trans('admin::app.activities.create-success', ['type' => trans('admin::app.activities.'.$activity->type)]));

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

        if (request('participants')) {
            $activity->participants()->delete();

            if (is_array(request('participants.users'))) {
                foreach (request('participants.users') as $userId) {
                    $activity->participants()->create([
                        'user_id' => $userId,
                    ]);
                }
            }

            if (is_array(request('participants.persons'))) {
                foreach (request('participants.persons') as $personId) {
                    $activity->participants()->create([
                        'person_id' => $personId,
                    ]);
                }
            }
        }

        if (request('lead_id')) {
            $lead = $this->leadRepository->find(request('lead_id'));

            if (! $lead->activities->contains($id)) {
                $lead->activities()->attach($id);
            }
        }

        Event::dispatch('activity.update.after', $activity);

        if (request()->ajax()) {
            return response()->json([
                'message' => trans('admin::app.activities.update-success', ['type' => trans('admin::app.activities.'.$activity->type)]),
            ]);
        } else {
            session()->flash('success', trans('admin::app.activities.update-success', ['type' => trans('admin::app.activities.'.$activity->type)]));

            return redirect()->route('admin.activities.index');
        }
    }

    /**
     * Mass Update the specified resources.
     */
    public function massUpdate(): JsonResponse
    {
        $count = 0;

        $data = request()->all();

        foreach (request('rows') as $activityId) {
            Event::dispatch('activity.update.before', $activityId);

            $activity = $this->activityRepository->update([
                'is_done' => request('value'),
            ], $activityId);

            Event::dispatch('activity.update.after', $activity);

            $count++;
        }

        if (! $count) {
            return response()->json([
                'message' => trans('admin::app.activities.mass-update-failed'),
            ], 400);
        }

        return response()->json([
            'message' => trans('admin::app.activities.mass-update-success'),
        ]);
    }

    /**
     * Search participants results.
     */
    public function searchParticipants(): JsonResponse
    {
        $users = $this->userRepository->findWhere([
            ['name', 'like', '%'.urldecode(request()->input('query')).'%'],
        ]);

        $persons = $this->personRepository->findWhere([
            ['name', 'like', '%'.urldecode(request()->input('query')).'%'],
        ]);

        return response()->json([
            'users'   => $users,
            'persons' => $persons,
        ]);
    }

    /**
     * Upload files to storage.
     */
    public function upload(): RedirectResponse
    {
        $this->validate(request(), [
            'file' => 'required',
        ]);

        Event::dispatch('activities.file.create.before');

        $file = $this->fileRepository->upload(request()->all());

        if ($file) {
            if ($leadId = request('lead_id')) {
                $lead = $this->leadRepository->find($leadId);

                $lead->activities()->attach($file->activity->id);
            }

            Event::dispatch('activities.file.create.after', $file);

            session()->flash('success', trans('admin::app.activities.file-upload-success'));
        } else {
            session()->flash('error', trans('admin::app.activities.file-upload-error'));
        }

        return redirect()->back();
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
                'message' => trans('admin::app.activities.destroy-success', ['type' => trans('admin::app.activities.'.$activity->type)]),
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => trans('admin::app.activities.destroy-failed', ['type' => trans('admin::app.activities.'.$activity->type)]),
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
            'message' => trans('admin::app.response.destroy-success', ['name' => trans('admin::app.activities.title')]),
        ]);
    }
}
