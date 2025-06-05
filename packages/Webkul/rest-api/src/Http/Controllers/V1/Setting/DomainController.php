<?php

namespace Webkul\RestApi\Http\Controllers\V1\Setting;

use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Domain\Repositories\DomainRepository;
use Webkul\RestApi\Http\Controllers\V1\Controller;
use Webkul\RestApi\Http\Resources\V1\Setting\DomainResource;

class DomainController extends Controller
{
    public function __construct(
        protected DomainRepository $domainRepository) {}

    public function index(): JsonResource
    {
        $tenants = $this->allResources($this->domainRepository);

        return DomainResource::collection($tenants);
    }
}
