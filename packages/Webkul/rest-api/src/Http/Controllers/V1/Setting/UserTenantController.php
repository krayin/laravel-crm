<?php

namespace Webkul\RestApi\Http\Controllers\V1\Setting;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Event;
use Webkul\RestApi\Http\Controllers\V1\Controller;
use Webkul\RestApi\Http\Request\MassDestroyRequest;
use Webkul\RestApi\Http\Request\MassUpdateRequest;
use Webkul\RestApi\Http\Resources\V1\Setting\UserTenantResource;
use Webkul\User\Repositories\UserTenantRepository;

class UserTenantController extends Controller
{
    public function __construct(protected UserTenantRepository $userTenantRepository) {}

    public function index(): JsonResource
    {
        $tenants = $this->allResources($this->userTenantRepository);
        return UserTenantResource::collection($tenants);
    }

    public function show(int $id): UserTenantResource
    {
        $tenant = $this->userTenantRepository->find($id);
        return new UserTenantResource($tenant);
    }

    public function store(): JsonResource
    {
        $data = $this->validate(request(), [
            'user_id'         => 'required|exists:users,id',
            'tenant_id'       => 'required|exists:tenants,id',
            'role_id'         => 'required|exists:roles,id',
            'status'          => 'sometimes|in:0,1',
            'view_permission' => 'sometimes|string',
            'groups'          => 'sometimes|array',
        ]);

        $data['status'] = $data['status'] ?? 1;
        $data['view_permission'] = $data['view_permission'] ?? 'global';

        Event::dispatch('settings.userTenant.create.before');

        $userTenant = $this->userTenantRepository->create($data);

        if (isset($data['groups'])) {
            $userTenant->groups()->sync($data['groups']);
        }

        Event::dispatch('settings.userTenant.create.after', $userTenant);

        return new JsonResource([
            'data' => new UserTenantResource($userTenant),
            'message' => trans('rest-api::app.settings.user-tenants.create-success'),
        ]);
    }

    public function update(int $id): JsonResource
    {
        $data = $this->validate(request(), [
            'user_id'         => 'sometimes|exists:users,id',
            'tenant_id'       => 'sometimes|exists:tenants,id',
            'role_id'         => 'sometimes|exists:roles,id',
            'status'          => 'sometimes|in:0,1',
            'view_permission' => 'sometimes|string',
            'groups'          => 'sometimes|array',
        ]);

        Event::dispatch('settings.userTenant.update.before', $id);

        $userTenant = $this->userTenantRepository->update($data, $id);

        if (isset($data['groups'])) {
            $userTenant->groups()->sync($data['groups']);
        }

        Event::dispatch('settings.userTenant.update.after', $userTenant);

        return new JsonResource([
            'data' => new UserTenantResource($userTenant),
            'message' => trans('rest-api::app.settings.user-tenants.updated-success'),
        ]);
    }

    public function destroy(int $id): JsonResource
    {
        Event::dispatch('settings.userTenant.delete.before', $id);

        try {
            $this->userTenantRepository->delete($id);

            Event::dispatch('settings.userTenant.delete.after', $id);

            return new JsonResource([
                'message' => trans('rest-api::app.settings.user-tenants.delete-success'),
            ]);
        } catch (\Exception $e) {
            return new JsonResource([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function massUpdate(MassUpdateRequest $request): JsonResource
    {
        $ids = $request->input('indices');
        $count = 0;

        foreach ($ids as $id) {
            Event::dispatch('settings.userTenant.update.before', $id);

            $userTenant = $this->userTenantRepository->find($id);

            if ($userTenant) {
                $userTenant->update(['status' => $request->input('value')]);
                Event::dispatch('settings.userTenant.update.after', $id);
                $count++;
            }
        }

        if (!$count) {
            return new JsonResource([
                'message' => trans('rest-api::app.settings.user-tenants.mass-update-failed'),
            ], 500);
        }

        return new JsonResource([
            'message' => trans('rest-api::app.settings.user-tenants.mass-update-success'),
        ]);
    }

    public function massDestroy(MassDestroyRequest $request): JsonResource
    {
        $ids = $request->input('indices');
        $count = 0;

        foreach ($ids as $id) {
            Event::dispatch('settings.userTenant.delete.before', $id);

            $userTenant = $this->userTenantRepository->find($id);

            if ($userTenant) {
                $userTenant->delete();
                Event::dispatch('settings.userTenant.delete.after', $id);
                $count++;
            }
        }

        if (!$count) {
            return new JsonResource([
                'message' => trans('rest-api::app.settings.user-tenants.mass-delete-failed'),
            ], 500);
        }

        return new JsonResource([
            'message' => trans('rest-api::app.settings.user-tenants.mass-delete-success'),
        ]);
    }
}
