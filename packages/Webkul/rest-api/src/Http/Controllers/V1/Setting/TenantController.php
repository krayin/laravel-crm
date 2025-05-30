<?php

namespace Webkul\RestApi\Http\Controllers\V1\Setting;

use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Domain\Repositories\DomainRepository;
use Webkul\RestApi\Http\Controllers\V1\Controller;
use Webkul\RestApi\Http\Resources\V1\Setting\DomainResource;
use Webkul\RestApi\Http\Resources\V1\Setting\TenantResource;
use Webkul\Tenant\Repositories\TenantRepository;

class TenantController extends Controller
{
    public function __construct(
        protected TenantRepository $tenantRepository,
        protected DomainRepository $domainRepository
    ) {}

    public function formatDomainName(string $name): string
    {
        $name = mb_strtolower($name, 'UTF-8');

        $map = [
            'á|à|ã|â|ä' => 'a',
            'é|è|ê|ë'   => 'e',
            'í|ì|î|ï'   => 'i',
            'ó|ò|õ|ô|ö' => 'o',
            'ú|ù|û|ü'   => 'u',
            'ç'         => 'c',
            'ñ'         => 'n',
        ];

        foreach ($map as $pattern => $replacement) {
            $name = preg_replace("/$pattern/u", $replacement, $name);
        }

        return preg_replace('/[^a-z0-9]/', '', $name);
    }

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
            'multiatendedor_id' => 'required',
            'data'              => 'required',

        ]);

        $data = request()->all();

        $domainName = $this->formatDomainName($data['data']['name']);

        $data['data'] = json_encode($data['data']);
        $admin = $this->tenantRepository->create($data);

        $admin->save();

        $tenant_id = $admin['id'];

        // Dominio referente ao local!
        $domainData = [
            'domain'    => $domainName.'.localhost',
            'tenant_id' => $tenant_id,
        ];

        try {
            $domain = $this->domainRepository->create($domainData);
            $domain->save();
            $createDomain = true;
        } catch (Exception $e) {
            $createDomain = false;
        }

        if ($createDomain) {
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

    }

    public function update($id)
    {
        $this->validate(request(), [
            'multiatendedor_id' => 'required',
            'data'              => 'required',
        ]);

        $data = request()->all();

        $domainName = $this->formatDomainName($data['data']['name']);

        $data['data'] = json_encode($data['data']);

        $admin = $this->tenantRepository->update($data, $id);

        $admin->save();

        return new JsonResource([
            'data'    => new TenantResource($admin),
            'message' => trans('rest-api::app.settings.tenants.updated-success'),
        ]);
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
