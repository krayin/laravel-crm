<?php

namespace Webkul\Admin\Http\Controllers\Settings;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Event;
use Illuminate\View\View;
use Webkul\Admin\DataGrids\Settings\GroupDataGrid;
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
     */
    public function index(): View|JsonResponse
    {
        if (request()->ajax()) {
            return datagrid(GroupDataGrid::class)->process();
        }

        return view('admin::settings.groups.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(): JsonResponse
    {
        $this->validate(request(), [
            'name' => 'required|unique:groups,name',
        ]);

        Event::dispatch('settings.group.create.before');

        $group = $this->groupRepository->create(request()->only([
            'name',
            'description',
        ]));

        Event::dispatch('settings.group.create.after', $group);

        return new JsonResponse([
            'data'    => $group,
            'message' => trans('admin::app.settings.groups.index.create-success'),
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
     */
    public function update(int $id): JsonResponse
    {
        $this->validate(request(), [
            'name' => 'required|unique:groups,name,'.$id,
        ]);

        Event::dispatch('settings.group.update.before', $id);

        $group = $this->groupRepository->update(request()->only([
            'name',
            'description',
        ]), $id);

        Event::dispatch('settings.group.update.after', $group);

        return new JsonResponse([
            'data'    => $group,
            'message' => trans('admin::app.settings.groups.index.update-success'),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id): JsonResponse
    {
        $group = $this->groupRepository->findOrFail($id);

        try {
            Event::dispatch('settings.group.delete.before', $id);

            $group->delete($id);

            Event::dispatch('settings.group.delete.after', $id);

            return new JsonResponse([
                'message' => trans('admin::app.settings.groups.index.destroy-success'),
            ], 200);
        } catch (\Exception $exception) {
            return new JsonResponse([
                'message' => trans('admin::app.settings.groups.index.delete-failed'),
            ], 400);
        }
    }
}
