<?php

namespace Webkul\Admin\Http\Controllers\Settings;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Event;
use Illuminate\View\View;
use Webkul\Admin\DataGrids\Setting\RoleDataGrid;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\User\Repositories\RoleRepository;

class RoleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(protected RoleRepository $roleRepository) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): View|JsonResponse
    {
        if (request()->ajax()) {
            return datagrid(RoleDataGrid::class)->process();
        }

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
            'description'     => 'required',
        ]);

        Event::dispatch('settings.role.create.before');

        $data = request()->only([
            'name',
            'description',
            'permission_type',
            'permissions',
        ]);

        if ($data['permission_type'] == 'custom') {
            if (! isset($data['permissions'])) {
                $data['permissions'] = [];
            }
        }

        $role = $this->roleRepository->create($data);

        Event::dispatch('settings.role.create.after', $role);

        session()->flash('success', trans('admin::app.settings.roles.index.create-success'));

        return redirect()->route('admin.settings.roles.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): View
    {
        $role = $this->roleRepository->findOrFail($id);

        return view('admin::settings.roles.edit', compact('role'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(int $id)
    {
        $this->validate(request(), [
            'name'            => 'required',
            'permission_type' => 'required|in:all,custom',
            'description'     => 'required',
        ]);

        Event::dispatch('settings.role.update.before', $id);

        $data = array_merge(request()->only([
            'name',
            'description',
            'permission_type',
        ]), [
            'permissions' => request()->has('permissions') ? request('permissions') : [],
        ]);

        $role = $this->roleRepository->update($data, $id);

        Event::dispatch('settings.role.update.after', $role);

        session()->flash('success', trans('admin::app.settings.roles.index.update-success'));

        return redirect()->route('admin.settings.roles.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        $response = [
            'responseCode' => 400,
        ];

        $role = $this->roleRepository->findOrFail($id);

        if ($role->admins && $role->admins->count() >= 1) {
            $response['message'] = trans('admin::app.settings.roles.index.being-used');

            session()->flash('error', $response['message']);
        } elseif ($this->roleRepository->count() == 1) {
            $response['message'] = trans('admin::app.settings.roles.index.last-delete-error');

            session()->flash('error', $response['message']);
        } else {
            try {
                Event::dispatch('settings.role.delete.before', $id);

                if (auth()->guard('user')->user()->role_id == $id) {
                    $response['message'] = trans('admin::app.settings.roles.index.current-role-delete-error');
                } else {
                    $this->roleRepository->delete($id);

                    Event::dispatch('settings.role.delete.after', $id);

                    $message = trans('admin::app.settings.roles.index.delete-success');

                    $response = [
                        'responseCode' => 200,
                        'message'      => $message,
                    ];

                    session()->flash('success', $message);
                }
            } catch (\Exception $exception) {
                $message = $exception->getMessage();

                $response['message'] = $message;

                session()->flash('error', $message);
            }
        }

        return response()->json($response, $response['responseCode']);
    }
}
