<?php

namespace Webkul\Admin\Http\Controllers\Settings;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Prettus\Repository\Criteria\RequestCriteria;
use Webkul\Admin\DataGrids\Settings\UserDataGrid;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Admin\Http\Requests\MassDestroyRequest;
use Webkul\Admin\Http\Requests\MassUpdateRequest;
use Webkul\Admin\Http\Resources\UserResource;
use Webkul\Admin\Notifications\User\Create as UserCreatedNotification;
use Webkul\User\Repositories\GroupRepository;
use Webkul\User\Repositories\RoleRepository;
use Webkul\User\Repositories\UserRepository;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        protected UserRepository $userRepository,
        protected GroupRepository $groupRepository,
        protected RoleRepository $roleRepository
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): View|JsonResponse
    {
        if (request()->ajax()) {
            return datagrid(UserDataGrid::class)->process();
        }

        $roles = $this->roleRepository->all();

        $groups = $this->groupRepository->all();

        return view('admin::settings.users.index', compact('roles', 'groups'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(): View|JsonResponse
    {
        $validated = $this->validate(request(), [
            'email' => 'required|email|unique:users,email',
            'name' => 'required',
            'password' => 'nullable',
            'confirm_password' => 'nullable|required_with:password|same:password',
            'role_id' => 'required|integer|exists:roles,id',
            'status' => 'boolean|in:0,1',
            'view_permission' => 'string|in:global,group,individual',
            'groups' => 'required_if:view_permission,group|array',
            'groups.*' => 'integer|exists:groups,id',
        ]);

        /**
         * Reject any role that grants privileges beyond the acting user's own, so an administrator
         * role (or any broader custom role) can never be granted by a non-administrator.
         */
        $this->preventUnauthorizedRoleAssignment($validated['role_id']);

        /**
         * Build the payload from the validated data only; never mass-assign the raw request.
         */
        $data = Arr::only($validated, [
            'name', 'email', 'password', 'role_id', 'status', 'view_permission', 'groups',
        ]);

        if (! empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }

        Event::dispatch('settings.user.create.before');

        $admin = $this->userRepository->create($data);

        $admin->groups()->sync($data['groups'] ?? []);

        try {
            Mail::queue(new UserCreatedNotification($admin));
        } catch (\Exception $e) {
            report($e);
        }

        Event::dispatch('settings.user.create.after', $admin);

        return new JsonResponse([
            'data' => $admin,
            'message' => trans('admin::app.settings.users.index.create-success'),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): View|JsonResponse
    {
        $authUser = auth()->guard('user')->user();

        /**
         * A non-administrator may only view their own account, mirroring the ownership rule enforced
         * in update() so another user's details cannot be read by id (IDOR).
         */
        if ($authUser->role?->permission_type !== 'all' && $authUser->id != $id) {
            abort(401, trans('admin::app.errors.unauthorized'));
        }

        $admin = $this->userRepository->with(['role', 'groups'])->findOrFail($id);

        return new JsonResponse([
            'data' => $admin,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(int $id): JsonResponse
    {
        $authUser = auth()->guard('user')->user();

        $isAdministrator = $authUser->role?->permission_type === 'all';

        /**
         * The `settings.user.users.edit` permission is already enforced by the ACL middleware. Here
         * we additionally restrict a non-administrator to editing only their own account, which
         * blocks horizontal access to other accounts (IDOR).
         */
        if (! $isAdministrator && $authUser->id != $id) {
            abort(401, trans('admin::app.errors.unauthorized'));
        }

        $validated = $this->validate(request(), [
            'email' => 'required|email|unique:users,email,'.$id,
            'name' => 'required|string',
            'password' => 'nullable|string|min:6',
            'confirm_password' => 'nullable|required_with:password|same:password',
            'role_id' => 'required|integer|exists:roles,id',
            'status' => 'nullable|boolean|in:0,1',
            'view_permission' => 'required|string|in:global,group,individual',
            'groups' => 'required_if:view_permission,group|array',
            'groups.*' => 'integer|exists:groups,id',
        ]);

        /**
         * A non-administrator may only edit their own profile fields. Reject — with a clear error
         * rather than a silent success — any attempt to change privileged fields such as their role
         * or data scope, which the field whitelist below would otherwise drop unnoticed. Editing
         * others is already blocked above, so only full administrators reach the role path here and
         * are trusted to assign any role.
         */
        if (
            ! $isAdministrator
            && ((int) request('role_id') !== (int) $authUser->role_id
                || request('view_permission') !== $authUser->view_permission)
        ) {
            abort(401, trans('admin::app.errors.unauthorized'));
        }

        /**
         * Whitelist writable fields from the validated data. A non-administrator editing their own
         * account can only touch profile fields, never role, data scope, status, or group membership.
         */
        $data = Arr::only($validated, $isAdministrator
            ? ['name', 'email', 'password', 'role_id', 'status', 'view_permission', 'groups']
            : ['name', 'email', 'password']
        );

        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = bcrypt($data['password']);
        }

        /**
         * The primary administrator (id 1) can never be demoted, rescoped, or disabled, so the
         * system is never left without a full administrator.
         */
        if ((int) $id === 1) {
            $data = Arr::except($data, ['role_id', 'view_permission']);

            $data['status'] = 1;
        }

        if ($authUser->id == $id) {
            $data['status'] = true;
        }

        Event::dispatch('settings.user.update.before', $id);

        $admin = $this->userRepository->update($data, $id);

        if ($isAdministrator && (int) $id !== 1) {
            $admin->groups()->sync(request('groups') ?? []);
        }

        Event::dispatch('settings.user.update.after', $admin);

        return new JsonResponse([
            'data' => $admin,
            'message' => trans('admin::app.settings.users.index.update-success'),
        ]);
    }

    /**
     * Search user results.
     */
    public function search(): JsonResource
    {
        $users = $this->userRepository
            ->pushCriteria(app(RequestCriteria::class))
            ->all();

        return UserResource::collection($users);
    }

    /**
     * Destroy specified user.
     */
    public function destroy(int $id): JsonResponse
    {
        /**
         * Never delete the last remaining user or the primary administrator (id 1).
         */
        if ($this->userRepository->count() == 1 || (int) $id === 1) {
            return new JsonResponse([
                'message' => trans('admin::app.settings.users.index.last-delete-error'),
            ], 400);
        }

        try {
            Event::dispatch('user.admin.delete.before', $id);

            $this->userRepository->delete($id);

            Event::dispatch('user.admin.delete.after', $id);

            return new JsonResponse([
                'message' => trans('admin::app.settings.users.index.delete-success'),
            ], 200);
        } catch (\Exception $e) {
        }

        return new JsonResponse([
            'message' => trans('admin::app.settings.users.index.delete-failed'),
        ], 500);
    }

    /**
     * Mass Update the specified resources.
     */
    public function massUpdate(MassUpdateRequest $massDestroyRequest): JsonResponse
    {
        $count = 0;

        $users = $this->userRepository->findWhereIn('id', $massDestroyRequest->input('indices'));

        foreach ($users as $users) {
            /**
             * Never mass-update the acting user or the primary administrator (id 1).
             */
            if (auth()->guard('user')->user()->id == $users->id || (int) $users->id === 1) {
                continue;
            }

            Event::dispatch('settings.user.update.before', $users->id);

            $this->userRepository->update([
                'status' => $massDestroyRequest->input('value'),
            ], $users->id);

            Event::dispatch('settings.user.update.after', $users->id);

            $count++;
        }

        if (! $count) {
            return response()->json([
                'message' => trans('admin::app.settings.users.index.mass-update-failed'),
            ], 400);
        }

        return response()->json([
            'message' => trans('admin::app.settings.users.index.mass-update-success'),
        ]);
    }

    /**
     * Mass Delete the specified resources.
     */
    public function massDestroy(MassDestroyRequest $massDestroyRequest): JsonResponse
    {
        $count = 0;

        $users = $this->userRepository->findWhereIn('id', $massDestroyRequest->input('indices'));

        foreach ($users as $user) {
            /**
             * Never mass-delete the acting user or the primary administrator (id 1).
             */
            if (auth()->guard('user')->user()->id == $user->id || (int) $user->id === 1) {
                continue;
            }

            Event::dispatch('settings.user.delete.before', $user->id);

            $this->userRepository->delete($user->id);

            Event::dispatch('settings.user.delete.after', $user->id);

            $count++;
        }

        if (! $count) {
            return response()->json([
                'message' => trans('admin::app.settings.users.index.mass-delete-failed'),
            ], 400);
        }

        return response()->json([
            'message' => trans('admin::app.settings.users.index.mass-delete-success'),
        ]);
    }

    /**
     * Guard against privilege escalation through role assignment.
     *
     * A full administrator may assign any role, including administrator. Everyone else may only
     * assign a role whose privileges are a subset of their own, and may never assign a role whose
     * permission type is `all`. This mirrors the "cannot grant a role above your own" model used by
     * mainstream CRMs (Salesforce role hierarchy, HubSpot/SuiteCRM/EspoCRM permission sets).
     */
    protected function preventUnauthorizedRoleAssignment(?int $roleId): void
    {
        $authUser = auth()->guard('user')->user();

        /**
         * Full administrators are trusted to grant any role.
         */
        if ($authUser->role?->permission_type === 'all') {
            return;
        }

        $role = $this->roleRepository->find($roleId);

        /**
         * A non-administrator can never grant the administrator (`all`) role.
         */
        if (! $role || $role->permission_type === 'all') {
            abort(401, trans('admin::app.errors.unauthorized'));
        }

        /**
         * The requested role must not contain any permission the acting user does not personally
         * hold, so a user can never grant privileges beyond their own.
         */
        if (! empty(array_diff($role->permissions ?? [], $authUser->role?->permissions ?? []))) {
            abort(401, trans('admin::app.errors.unauthorized'));
        }
    }
}
