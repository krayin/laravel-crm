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
use Webkul\User\Contracts\User;
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

        $roles = $this->assignableRoles();

        $groups = $this->groupRepository->all();

        return view('admin::settings.users.index', compact('roles', 'groups'));
    }

    /**
     * The roles the acting user is allowed to assign.
     *
     * A full administrator may assign any role. Everyone else — mirroring Salesforce delegated
     * administration and HubSpot ("you can't assign permissions you don't have") — only sees roles
     * whose privileges are a subset of their own, and never the administrator role. This keeps the
     * UI in step with the server-side guard so an un-assignable role is never even offered.
     */
    protected function assignableRoles()
    {
        $roles = $this->roleRepository->all();

        $authUser = auth()->guard('user')->user();

        if ($authUser->role?->permission_type === 'all') {
            return $roles;
        }

        $ownPermissions = $authUser->role?->permissions ?? [];

        return $roles
            ->filter(fn ($role) => $role->permission_type !== 'all'
                && empty(array_diff($role->permissions ?? [], $ownPermissions)))
            ->values();
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

        /**
         * Record who created the account so the listing can scope it by ownership (a user with the
         * `individual` data scope sees only the users they created), mirroring how Krayin scopes
         * lead and contact records by their owner.
         */
        $data['created_by'] = auth()->guard('user')->id();

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

        $admin = $this->userRepository->with(['role', 'groups'])->findOrFail($id);

        /**
         * A non-administrator may view their own account or any user that is not above them, but
         * never a user whose privileges exceed their own (mirrors the rule enforced in update()).
         */
        if ($authUser->id != $id) {
            $this->preventManagingHigherPrivilegedUser($admin);
        }

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

        $isSelf = $authUser->id == $id;

        $targetUser = $this->userRepository->findOrFail($id);

        /**
         * The `settings.user.users.edit` permission is already enforced by the ACL middleware. A
         * non-administrator editing another user may only edit users that are not above them, so an
         * administrator (or any user holding a broader role) can never be tampered with.
         */
        if (! $isSelf) {
            $this->preventManagingHigherPrivilegedUser($targetUser);
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
         * A user may never escalate their own account: reject — with a clear error rather than a
         * silent success — any attempt to change their own role or data scope.
         */
        if (
            ! $isAdministrator
            && $isSelf
            && ((int) request('role_id') !== (int) $authUser->role_id
                || request('view_permission') !== $authUser->view_permission)
        ) {
            abort(401, trans('admin::app.errors.own-privileges'));
        }

        /**
         * A non-administrator may only assign a role, and a data scope, that do not exceed their own.
         */
        $this->preventUnauthorizedRoleAssignment($validated['role_id']);

        $this->preventUnauthorizedScopeAssignment($validated['view_permission'], $targetUser->view_permission);

        /**
         * Whitelist writable fields. A user editing their OWN account may only touch profile fields
         * (never self-escalate); editing another user (delegated management) they may also set the
         * role, data scope, status and groups — all already constrained above to their own level.
         */
        $data = Arr::only($validated, ($isAdministrator || ! $isSelf)
            ? ['name', 'email', 'password', 'role_id', 'status', 'view_permission', 'groups']
            : ['name', 'email', 'password']
        );

        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = bcrypt($data['password']);
        }

        /**
         * The primary administrator (id 1) can never be demoted, rescoped, or disabled. Reject any
         * such attempt with a clear error rather than silently ignoring it and reporting success.
         */
        if ((int) $id === 1) {
            if (
                (int) request('role_id') !== (int) $targetUser->role_id
                || request('view_permission') !== $targetUser->view_permission
                || (request()->filled('status') && ! (int) request('status'))
            ) {
                abort(401, trans('admin::app.errors.primary-admin-protected'));
            }

            $data['status'] = 1;
        }

        if ($isSelf) {
            $data['status'] = true;
        }

        Event::dispatch('settings.user.update.before', $id);

        $admin = $this->userRepository->update($data, $id);

        /**
         * Group membership is only writable when the role/scope fields are (an administrator, or a
         * delegated manager editing another user) and never for the primary administrator.
         */
        if (($isAdministrator || ! $isSelf) && (int) $id !== 1) {
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

        /**
         * A non-administrator may not delete a user whose privileges exceed their own.
         */
        if (auth()->guard('user')->user()->id != $id) {
            $this->preventManagingHigherPrivilegedUser($this->userRepository->findOrFail($id));
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
             * Skip the acting user, the primary administrator (id 1) and any user whose privileges
             * exceed the acting user's own.
             */
            if (
                auth()->guard('user')->user()->id == $users->id
                || (int) $users->id === 1
                || ! $this->canManageUser($users)
            ) {
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
             * Skip the acting user, the primary administrator (id 1) and any user whose privileges
             * exceed the acting user's own.
             */
            if (
                auth()->guard('user')->user()->id == $user->id
                || (int) $user->id === 1
                || ! $this->canManageUser($user)
            ) {
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
            abort(401, trans('admin::app.errors.role-exceeds-own'));
        }

        /**
         * The requested role must not contain any permission the acting user does not personally
         * hold, so a user can never grant privileges beyond their own.
         */
        if (! empty(array_diff($role->permissions ?? [], $authUser->role?->permissions ?? []))) {
            abort(401, trans('admin::app.errors.role-exceeds-own'));
        }
    }

    /**
     * Guard against widening a data scope beyond the acting user's own.
     *
     * A full administrator may grant any scope. Everyone else may only assign a scope that is not
     * broader than their own (individual < group < global), so a delegated manager cannot grant a
     * user — or themselves — more visibility than they hold.
     */
    protected function preventUnauthorizedScopeAssignment(?string $scope, ?string $currentScope = null): void
    {
        $authUser = auth()->guard('user')->user();

        if ($authUser->role?->permission_type === 'all') {
            return;
        }

        $rank = ['individual' => 0, 'group' => 1, 'global' => 2];

        /**
         * The scope may be kept at the target's existing level, but never raised beyond the greater
         * of the acting user's own scope and the target's current scope — so no extra visibility is
         * ever granted.
         */
        $ceiling = max($rank[$authUser->view_permission] ?? 0, $rank[$currentScope] ?? 0);

        if (($rank[$scope] ?? 0) > $ceiling) {
            abort(401, trans('admin::app.errors.scope-exceeds-own'));
        }
    }

    /**
     * Determine whether the acting user may manage (edit or delete) the given user.
     *
     * A full administrator may manage anyone. Everyone else may manage only users that are not above
     * them — a user holding the administrator role, or any permission the acting user does not
     * personally hold, is off-limits. This prevents a delegated manager from tampering with a
     * superior's account.
     *
     * @param  User  $targetUser
     */
    protected function canManageUser($targetUser): bool
    {
        $authUser = auth()->guard('user')->user();

        if ($authUser->role?->permission_type === 'all') {
            return true;
        }

        if ($targetUser->role?->permission_type === 'all') {
            return false;
        }

        return empty(array_diff($targetUser->role?->permissions ?? [], $authUser->role?->permissions ?? []));
    }

    /**
     * Abort when the acting user is not allowed to manage the given user.
     *
     * @param  User  $targetUser
     */
    protected function preventManagingHigherPrivilegedUser($targetUser): void
    {
        if (! $this->canManageUser($targetUser)) {
            abort(401, trans('admin::app.errors.user-exceeds-own'));
        }
    }
}
