<?php

namespace Webkul\Admin\Http\Controllers\Setting;

use Illuminate\Support\Facades\Event;
use Webkul\User\Repositories\RoleRepository;
use Webkul\Admin\Http\Controllers\Controller;

class RoleController extends Controller
{
    /**
     * RoleRepository object
     *
     * @var \Webkul\User\Repositories\RoleRepository
     */
    protected $roleRepository;

    /**
     * Create a new controller instance.
     *
     * @param  \Webkul\User\Repositories\RoleRepository  $roleRepository
     * @return void
     */
    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('admin::settings.roles.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin::settings.roles.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $this->validate(request(), [
            'name'            => 'required',
            'permission_type' => 'required',
        ]);

        Event::dispatch('settings.role.create.before');

        $role = $this->roleRepository->create(request()->all());

        Event::dispatch('settings.role.create.after', $role);

        session()->flash('success', trans('admin::app.settings.roles.create-success'));

        return redirect()->route('admin.settings.roles.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $role = $this->roleRepository->findOrFail($id);

        return view('admin::settings.roles.edit', compact('role'));
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
            'name'            => 'required',
            'permission_type' => 'required',
        ]);

        Event::dispatch('settings.role.update.before', $id);

        $role = $this->roleRepository->update(request()->all(), $id);

        Event::dispatch('settings.role.update.after', $role);

        session()->flash('success', trans('admin::app.settings.roles.update-success'));

        return redirect()->route('admin.settings.roles.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $role = $this->roleRepository->findOrFail($id);

        if ($role->admins->count() >= 1) {
            session()->flash('error', trans('admin::app.settings.roles.being-used'));
        } elseif($this->roleRepository->count() == 1) {
            session()->flash('error', trans('admin::app.settings.roles.last-delete-error'));
        } else {
            try {
                Event::dispatch('settings.role.delete.before', $id);

                $this->roleRepository->delete($id);

                Event::dispatch('settings.role.delete.after', $id);

                session()->flash('success', trans('admin::app.settings.roles.delete-success'));

                return response()->json(['message' => true], 200);
            } catch(\Exception $e) {
                session()->flash('error', trans('admin::app.settings.roles.delete-failed'));
            }
        }

        return response()->json(['message' => false], 400);
    }
}