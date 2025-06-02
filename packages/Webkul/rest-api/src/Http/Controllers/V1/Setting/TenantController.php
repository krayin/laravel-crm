<?php

namespace Webkul\RestApi\Http\Controllers\V1\Setting;

use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Core\Helpers\Helper;
use Webkul\Domain\Repositories\DomainRepository;
use Webkul\Domain\Repositories\DomainRepository;
use Webkul\RestApi\Http\Controllers\V1\Controller;
use Webkul\RestApi\Http\Resources\V1\Setting\DomainResource;
use Webkul\RestApi\Http\Resources\V1\Setting\DomainResource;
use Webkul\RestApi\Http\Resources\V1\Setting\TenantResource;
use Webkul\Tenant\Models\Tenant;
use Webkul\Tenant\Repositories\TenantRepository;

class TenantController extends Controller
{
    public function __construct(
        protected TenantRepository $tenantRepository,
        protected DomainRepository $domainRepository
    ) {}

    public function index(): JsonResource
    {
        try {
            $tenants = Tenant::with('domains')->get();

            return TenantResource::collection($tenants);
        } catch (\Exception $e) {
            return new JsonResource([
                'message' => 'Error: '.$e->getMessage(),
            ], 500);
        }
    }

    public function show(int $id): JsonResource
    {
        try {

            return new TenantResource(Tenant::with('domains')->findOrFail($id));

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {

            abort(404, 'Tenant não encontrado');

        }
    }

    public function store(): JsonResource
    {
        try {
            $this->validate(request(), [
                'multiatendedor_id' => 'required',
                'data'              => 'required',
            ]);

            $data = request()->all();

            $domainName = Helper::formatDomainName($data['data']['name']);

            $data['data'] = json_encode($data['data']);

            $admin = $this->tenantRepository->create($data);
            $admin->save();

            $tenant_id = $admin['id'];

            // Domínio referente ao local
            $domainData = [
                'domain'    => $domainName.'.localhost',
                'tenant_id' => $tenant_id,
            ];

            try {
                $domain = $this->domainRepository->create($domainData);
                $domain->save();
                $updateDomain = true;
            } catch (\Exception $e) {
                logger()->error('Erro ao criar domínio: '.$e->getMessage());
                $updateDomain = false;
            }

            if ($updateDomain) {
                return new JsonResource([
                    'data' => [
                        'tenant' => new TenantResource($admin),
                        'domain' => new DomainResource($domain),
                    ],
                    'message' => trans('rest-api::app.settings.tenants.create-success'),
                ]);
            }

            return new JsonResource([
                'data'    => new TenantResource($admin),
                'message' => trans('rest-api::app.settings.tenants.failed-create-domain'),
            ]);

        } catch (\Exception $e) {
            logger()->error('Erro ao criar tenant: '.$e->getMessage());

            return new JsonResource([
                'message' => 'Erro ao criar o tenant: '.$e->getMessage(),
            ], 500);
        }
    }

    public function update($id): JsonResource
    {
        try {
            $this->validate(request(), [
                'multiatendedor_id' => 'required',
                'data'              => 'required',
            ]);

            $data = request()->all();

            $domainName = Helper::formatDomainName($data['data']['name']);
            $data['data'] = json_encode($data['data']);

            $admin = $this->tenantRepository->update($data, $id);
            $admin->save();

            $tenant_id = $admin['id'];

            $domainData = [
                'domain'    => $domainName.'.localhost',
                'tenant_id' => $tenant_id,
            ];

            try {
                $existingDomain = $admin->domains()->first();

                if ($existingDomain) {
                    $domain = $this->domainRepository->update($domainData, $existingDomain->id);
                } else {
                    $domain = $this->domainRepository->create($domainData);
                }

                $domain->save();
                $updateDomain = true;
            } catch (\Exception $e) {
                logger()->error('Erro ao atualizar/criar domínio: '.$e->getMessage());
                $updateDomain = false;
            }

            if ($updateDomain) {
                return new JsonResource([
                    'data' => [
                        'tenant' => new TenantResource($admin),
                        'domain' => new DomainResource($domain),
                    ],
                    'message' => trans('rest-api::app.settings.tenants.updated-success'),
                ]);
            }

            return new JsonResource([
                'data'    => new TenantResource($admin),
                'message' => trans('rest-api::app.settings.tenants.failed-create-domain'),
            ]);

        } catch (\Exception $e) {
            logger()->error('Erro ao atualizar tenant: '.$e->getMessage());

            return new JsonResource([
                'message' => 'Erro ao atualizar o tenant: '.$e->getMessage(),
            ], 500);
        }
    }

    public function destroy(int $id): JsonResource
    {
        if ($this->tenantRepository->count() == 1) {
            return new JsonResource([
                'message' => trans('rest-api::app.settings.tenants.last-delete-error'),
            ], 400);
        } else {

            try {
                $this->tenantRepository->delete($id);

                return new JsonResource([
                    'message' => trans('rest-api::app.settings.tenants.delete-success'),
                ]);
            } catch (\Exception $exception) {
                return new JsonResource([
                    'message' => $exception->getMessage(),
                ], 500);
            }
        }
    }
}
