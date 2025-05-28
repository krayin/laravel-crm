<?php

namespace Webkul\RestApi\Http\Controllers\V1\Setting;

use Illuminate\Http\Resources\Json\JsonResource;
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

    public function show(int $id): TenantResource
    {
        $tenants = $this->allResources($this->tenantRepository);

        $resource = $tenants->where('id', $id)->first();

        return new TenantResource($resource);
    }
}
