<?php

namespace Webkul\Admin\Http\Controllers\Setting;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Event;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\User\Repositories\GroupRepository;

class GroupController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(protected GroupRepository $groupRepository) {}

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        if (request()->ajax()) {
            return app(\Webkul\Admin\DataGrids\Setting\GroupDataGrid::class)->toJson();
        }

        return view('admin::settings.groups.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin::settings.groups.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(): JsonResource
    {
        $this->validate(request(), [
            'name' => 'required|unique:groups,name',
        ]);

        Event::dispatch('settings.group.create.before');

        $group = $this->groupRepository->create(request()->all());

        Event::dispatch('settings.group.create.after', $group);

        return new JsonResource([
            'data' => $group,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): JsonResource
    {
        $group = $this->groupRepository->findOrFail($id);

        return new JsonResource([
            'data' => $group,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        $this->validate(request(), [
            'name' => 'required|unique:groups,name,'.$id,
        ]);

        Event::dispatch('settings.group.update.before', $id);

        $group = $this->groupRepository->update(request()->all(), $id);

        Event::dispatch('settings.group.update.after', $group);

        return new JsonResource([
            'data' => $group,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $group = $this->groupRepository->findOrFail($id);

        try {
            Event::dispatch('settings.group.delete.before', $id);

            $this->groupRepository->delete($id);

            Event::dispatch('settings.group.delete.after', $id);

            return response()->json([
                'message' => trans('admin::app.settings.groups.destroy-success'),
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => trans('admin::app.settings.groups.delete-failed'),
            ], 400);
        }

        return response()->json([
            'message' => trans('admin::app.settings.groups.delete-failed'),
        ], 400);
    }
}
