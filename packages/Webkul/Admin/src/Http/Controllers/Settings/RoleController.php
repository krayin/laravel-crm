<?php

namespace Webkul\Admin\Http\Controllers\Settings;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Event;
use Illuminate\View\View;
use Webkul\Admin\DataGrids\Settings\RoleDataGrid;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\User\Contracts\Role;
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
     */
    public function create(): View
    {
        return view('admin::settings.roles.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(): RedirectResponse|JsonResponse
    {
        $this->validate(request(), [
            'name' => 'required',
            'permission_type' => 'required|in:all,custom',
            'description' => 'required',
        ]);

        if (request('permission_type') == 'custom') {
            $this->validate(request(), [
                'permissions' => 'required',
            ]);
        }

        /**
         * A non-administrator may neither create a full-administrator role nor grant permissions
         * beyond their own, so role management cannot be used to escalate privileges. The denial is
         * surfaced as a flash message rather than a 401 page, since this is a standard form submit.
         */
        if (! $this->canManageRole(request('permission_type'), (array) request('permissions'))) {
            if (request()->ajax()) {
                return response()->json([
                    'message' => trans('admin::app.errors.role-permissions-exceed-own'),
                ], 401);
            }

            session()->flash('error', trans('admin::app.errors.role-permissions-exceed-own'));

            return redirect()->back()->withInput();
        }

        Event::dispatch('settings.role.create.before');

        $data = request()->only([
            'name',
            'description',
            'permission_type',
            'permissions',
        ]);

        /**
         * Record who created the role so the listing can scope it by the acting user's data scope
         * (a user with the `individual` scope sees only the roles they created).
         */
        $data['created_by'] = auth()->guard('user')->id();

        $role = $this->roleRepository->create($data);

        Event::dispatch('settings.role.create.after', $role);

        if (request()->ajax()) {
            return response()->json([
                'data' => $role,
                'message' => trans('admin::app.settings.roles.index.create-success'),
            ]);
        }

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
     */
    public function update(int $id): RedirectResponse
    {
        $this->validate(request(), [
            'name' => 'required',
            'permission_type' => 'required|in:all,custom',
            'description' => 'required',
            'permissions' => 'required_if:permission_type,custom',
        ]);

        $role = $this->roleRepository->findOrFail($id);

        /**
         * A non-administrator may neither modify a role that is broader than their own (such as the
         * Administrator role) nor promote a role to full administrator nor grant permissions beyond
         * their own, so role management cannot be used to escalate privileges. The denial is
         * surfaced as a flash message rather than a 401 page, since this is a standard form submit.
         */
        if (! $this->canManageRole(request('permission_type'), (array) request('permissions'), $role)) {
            session()->flash('error', trans('admin::app.errors.role-permissions-exceed-own'));

            return redirect()->back()->withInput();
        }

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

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $response = [
            'responseCode' => 400,
        ];

        $role = $this->roleRepository->findOrFail($id);

        if ($role->users && $role->users->count() >= 1) {
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
                        'message' => $message,
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

    /**
     * Determine whether the acting user may create or edit a role with the given permissions.
     *
     * A full administrator may manage any role. Everyone else may only manage custom roles whose
     * permissions are a subset of their own, may never create or promote a role to full
     * administrator, and may never modify a role that already holds privileges beyond their own
     * (such as the Administrator role). This matches the "cannot grant or edit a role above your
     * own" model used by mainstream CRMs.
     *
     * @param  Role|null  $existingRole
     */
    protected function canManageRole(?string $permissionType, array $permissions, $existingRole = null): bool
    {
        $authUser = auth()->guard('user')->user();

        /**
         * Full administrators are trusted to manage any role.
         */
        if ($authUser->role?->permission_type === 'all') {
            return true;
        }

        $ownPermissions = $authUser->role?->permissions ?? [];

        /**
         * A non-administrator can never create or promote a role to full administrator, nor grant
         * any permission they do not personally hold.
         */
        if ($permissionType === 'all'
            || ! empty(array_diff($permissions, $ownPermissions))
        ) {
            return false;
        }

        /**
         * The role being edited must not already be broader than the acting user's own role, so a
         * full-administrator role (or any role holding extra permissions) cannot be tampered with.
         */
        if ($existingRole
            && ($existingRole->permission_type === 'all'
                || ! empty(array_diff($existingRole->permissions ?? [], $ownPermissions)))
        ) {
            return false;
        }

        return true;
    }
}
