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
        return view('admin::settings.roles.index', [
            'tableClass' => '\Webkul\Admin\DataGrids\Setting\RoleDataGrid'
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $acl = app('acl');

        return view('admin::settings.roles.create', compact('acl'));
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

        $roleData = request()->all();

        if ($roleData['permission_type'] == 'custom') {
            array_push($roleData['permissions'], "admin.datagrid.api");
        }

        $role = $this->roleRepository->create($roleData);

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

        $acl = app('acl');

        return view('admin::settings.roles.edit', compact('role', 'acl'));
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

        $roleData = request()->all();

        if ($roleData['permission_type'] == 'custom') {
            array_push($roleData['permissions'], "admin.datagrid.api");
        }

        $role = $this->roleRepository->update($roleData, $id);

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

        if ($role->admins && $role->admins->count() >= 1) {
            $status = false;
            $responseCode = 400;
            $message = trans('admin::app.settings.roles.being-used');

            session()->flash('error', $message);
        } else if ($this->roleRepository->count() == 1) {
            $status = false;
            $responseCode = 400;
            $message = trans('admin::app.settings.roles.last-delete-error');

            session()->flash('error', $message);
        } else {
            try {
                Event::dispatch('settings.role.delete.before', $id);

                $this->roleRepository->delete($id);

                Event::dispatch('settings.role.delete.after', $id);

                $status = false;
                $responseCode = 200;
                $message = trans('admin::app.settings.roles.delete-success');

                session()->flash('success', $message);
            } catch(\Exception $exception) {
                $status = false;
                $responseCode = 400;
                $message = $exception->getMessage();

                session()->flash('error', $message);
            }
        }

        return response()->json([
            'status'    => $status,
            'message'   => $message,
        ], $responseCode);
    }
}