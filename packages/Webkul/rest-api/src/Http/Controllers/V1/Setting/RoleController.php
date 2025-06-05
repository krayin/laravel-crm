<?php

namespace Webkul\RestApi\Http\Controllers\V1\Setting;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Event;
use Webkul\RestApi\Http\Controllers\V1\Controller;
use Webkul\RestApi\Http\Resources\V1\Setting\RoleResource;
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
    public function index(): JsonResource
    {
        $roles = $this->allResources($this->roleRepository);

        return RoleResource::collection($roles);
    }

    /**
     * Show resource.
     */
    public function show(int $id): RoleResource
    {
        $resource = $this->roleRepository->find($id);

        return new RoleResource($resource);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(): JsonResource
    {
        $this->validate(request(), [
            'name'            => 'required|unique:roles,name',
            'permission_type' => 'required',
        ]);

        Event::dispatch('settings.role.create.before');

        $roleData = request()->all();

        if (
            $roleData['permission_type'] === 'custom'
            && ! isset($roleData['permissions'])
        ) {
            $roleData['permissions'] = [];
        }

        $role = $this->roleRepository->create($roleData);

        Event::dispatch('settings.role.create.after', $role);

        return new JsonResource([
            'data'    => new RoleResource($role),
            'message' => trans('rest-api::app.settings.roles.create-success'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(int $id): JsonResource
    {
        $this->validate(request(), [
            'name'            => 'required',
            'permission_type' => 'required',
        ]);

        Event::dispatch('settings.role.update.before', $id);

        $roleData = request()->all();

        if (
            $roleData['permission_type'] === 'custom'
            && ! isset($roleData['permissions'])
        ) {
            $roleData['permissions'] = [];
        }

        $role = $this->roleRepository->update($roleData, $id);

        Event::dispatch('settings.role.update.after', $role);

        return new JsonResource([
            'data'    => new RoleResource($role),
            'message' => trans('rest-api::app.settings.roles.update-success'),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResource
    {
        $response = ['code' => 400];

        $role = $this->roleRepository->findOrFail($id);

        if ($role->admins && $role->admins->count() >= 1) {
            $response['message'] = trans('rest-api::app.settings.roles.being-used');
        } elseif ($this->roleRepository->count() == 1) {
            $response['message'] = trans('rest-api::app.settings.roles.last-delete-error');
        } else {
            try {
                Event::dispatch('settings.role.delete.before', $id);

                if (auth()->guard()->user()->role_id == $id) {
                    $response['message'] = trans('rest-api::app.settings.roles.current-role-delete-error');
                } else {
                    $this->roleRepository->delete($id);

                    Event::dispatch('settings.role.delete.after', $id);

                    $response = [
                        'code'    => 200,
                        'message' => trans('rest-api::app.settings.roles.delete-success'),
                    ];
                }
            } catch (\Exception $exception) {
                report($exception);
            }
        }

        return new JsonResource(['message' => $response['message']], $response['code']);
    }
}
