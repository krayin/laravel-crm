<?php

namespace Webkul\Admin\Http\Controllers\Setting;

use Illuminate\Support\Facades\Event;

use Webkul\User\Repositories\GroupRepository;
use Webkul\Admin\Http\Controllers\Controller;

class GroupController extends Controller
{
    /**
     * GroupRepository object
     *
     * @var \Webkul\User\Repositories\GroupRepository
     */
    protected $groupRepository;

    /**
     * Create a new controller instance.
     *
     * @param  \Webkul\User\Repositories\GroupRepository  $groupRepository
     * @return void
     */
    public function __construct(GroupRepository $groupRepository)
    {
        $this->groupRepository = $groupRepository;
    }

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
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $this->validate(request(), [
            'name' => 'required|unique:groups,name',
        ]);

        Event::dispatch('settings.group.create.before');

        $group = $this->groupRepository->create(request()->all());

        Event::dispatch('settings.group.create.after', $group);

        session()->flash('success', trans('admin::app.settings.groups.create-success'));

        return redirect()->route('admin.settings.groups.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $group = $this->groupRepository->findOrFail($id);

        return view('admin::settings.groups.edit', compact('group'));
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
            'name' => 'required|unique:groups,name,' . $id,
        ]);

        Event::dispatch('settings.group.update.before', $id);

        $group = $this->groupRepository->update(request()->all(), $id);

        Event::dispatch('settings.group.update.after', $group);

        session()->flash('success', trans('admin::app.settings.groups.update-success'));

        return redirect()->route('admin.settings.groups.index');
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
        } catch(\Exception $exception) {
            return response()->json([
                'message' => trans('admin::app.settings.groups.delete-failed'),
            ], 400);
        }

        return response()->json([
            'message' => trans('admin::app.settings.groups.delete-failed'),
        ], 400);
    }
}
