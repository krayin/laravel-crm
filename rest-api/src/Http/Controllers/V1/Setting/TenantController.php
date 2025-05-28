<?php

namespace Webkul\RestApi\Http\Controllers\V1\Setting;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Webkul\RestApi\Http\Controllers\V1\Controller;
use Webkul\RestApi\Http\Resources\V1\Setting\TenantResource;
use Webkul\Tenant\Repositories\TenantRepository;

class TenantController extends Controller
{
    public function __construct(
        protected TenantRepository $tenantRepository) {}

    public function index(): JsonResource
    {
        $tenants = $this->allResources($this->tenantRepository);

        return TenantResource::collection($tenants);
    }

    public function show(int $id): JsonResource
    {
        try {

            return new TenantResource($this->tenantRepository->findOrFail($id));
        
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        
            abort(404, 'Tenant não encontrado');
        
        }
    }

    public function store(): JsonResource
    {
        $this->validate(request(), [
            'id'            => 'required',
            'data'             => 'required',
        ]);


        $data = request()->all();

        $data['data'] = json_encode($data['data']);
        $admin = $this->tenantRepository->create($data);

        $admin->save();

        return new JsonResource([
            'data'    => new TenantResource($admin),
            'message' => trans('rest-api::app.settings.users.create-success'),
        ]);
    }

     public function destroy(int $id): JsonResource
    {
       if ($this->tenantRepository->count() == 1) {
            return new JsonResource([
                'message' => trans('rest-api::app.settings.users.last-delete-error'),
            ], 400);
        } else {

            try {
                $this->tenantRepository->delete($id);


                return new JsonResource([
                    'message' => trans('rest-api::app.settings.users.delete-success'),
                ]);
            } catch (\Exception $exception) {
                return new JsonResource([
                    'message' => $exception->getMessage(),
                ], 500);
            }
        }
    }
}
