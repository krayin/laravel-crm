<?php

namespace Webkul\Admin\Http\Controllers\Activity;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Webkul\Activity\Repositories\ActivityRepository;
use Webkul\Activity\Repositories\FileRepository;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Contact\Repositories\PersonRepository;
use Webkul\Lead\Repositories\LeadRepository;
use Webkul\User\Repositories\UserRepository;

class ActivityController extends Controller
{
    /**
     * FileRepository object
     *
     * @var \Webkul\Activity\Repositories\FileRepository
     */
    protected $fileRepository;

    /**
     * ActivityRepository object
     *
     * @var \Webkul\Activity\Repositories\ActivityRepository
     */
    protected $activityRepository;

    /**
     * LeadRepository object
     *
     * @var \Webkul\Lead\Repositories\LeadRepository
     */
    protected $leadRepository;

    /**
     * UserRepository object
     *
     * @var \Webkul\User\Repositories\UserRepository
     */
    protected $userRepository;

    /**
     * PersonRepository object
     *
     * @var \Webkul\Contact\Repositories\PersonRepository
     */
    protected $personRepository;

    /**
     * Create a new controller instance.
     *
     * @param \Webkul\Activity\Repositories\ActivityRepository  $activityRepository
     * @param \Webkul\Activity\Repositories\FileRepository  $fileRepository
     * @param \Webkul\Activity\Repositories\LeadRepository  $leadRepository
     * @param \Webkul\User\Repositories\UserRepository  $userRepository
     * @param \Webkul\Contact\Repositories\PersonRepository  $personRepository
     *
     * @return void
     */
    public function __construct(
        ActivityRepository $activityRepository,
        FileRepository $fileRepository,
        LeadRepository $leadRepository,
        UserRepository $userRepository,
        PersonRepository $personRepository
    )
    {
        $this->activityRepository = $activityRepository;

        $this->fileRepository = $fileRepository;

        $this->leadRepository = $leadRepository;

        $this->userRepository = $userRepository;

        $this->personRepository = $personRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        if (request()->ajax()) {
            return app(\Webkul\Admin\DataGrids\Activity\ActivityDataGrid::class)->toJson();
        }

        return view('admin::activities.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
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
                        'user_id' => $userId
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

        if (request('type') == 'note') {
            session()->flash('success', trans('admin::app.activities.note-success'));
        } else if (request('type') == 'file') {
            session()->flash('success', trans('admin::app.activities.file-success'));
        } else {
            session()->flash('success', trans('admin::app.activities.create-success'));
        }

        return redirect()->back();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $activity = $this->activityRepository->findOrFail($id);

        return view('admin::activities.edit', compact('activity'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        Event::dispatch('activity.update.before');

        $activity = $this->activityRepository->update(request()->all(), $id);

        if (request('participants')) {
            $activity->participants()->delete();

            if (is_array(request('participants.users'))) {
                foreach (request('participants.users') as $userId) {
                    $activity->participants()->create([
                        'user_id' => $userId
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
                'status'  => true,
                'message' => trans('admin::app.activities.update-success'),
            ]);
        } else {
            session()->flash('success', trans('admin::app.activities.update-success'));

            return redirect()->route('admin.activities.index');
        }
    }

    /**
     * Search participants results
     *
     * @return \Illuminate\Http\Response
     */
    public function searchParticipants()
    {
        $users = $this->userRepository->findWhere([
            ['name', 'like', '%' . urldecode(request()->input('query')) . '%']
        ]);

        $persons = $this->personRepository->findWhere([
            ['name', 'like', '%' . urldecode(request()->input('query')) . '%']
        ]);

        return response()->json([
            'users'   => $users,
            'persons' => $persons,
        ]);
    }

    /**
     * Upload files to storage
     *
     * @return \Illuminate\View\View
     */
    public function upload()
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
     * Download file from storage
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function download($id)
    {
        $file = $this->fileRepository->findOrFail($id);

        return Storage::download($file->path);
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
            Event::dispatch('activity.delete.before', $id);

            $this->activityRepository->delete($id);

            Event::dispatch('activity.delete.after', $id);

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
