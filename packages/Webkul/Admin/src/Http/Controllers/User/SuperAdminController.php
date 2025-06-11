<?php

namespace Webkul\Admin\Http\Controllers\User;

use Webkul\Admin\Http\Controllers\Controller;                               // Controller base do Webkul
use Webkul\RestApi\Http\Controllers\V1\Setting\TenantController as ApiCtrl; // Controller da API para Tenant
use Illuminate\Http\Request;

class SuperAdminController extends Controller
{
    // ===== Métodos de Tenant (prefixados com "tenant") =====

    /**
     * Lista todos os tenants
     */
    public function tenantIndex(Request $request, ApiCtrl $apiController)
    {
        $jsonResource = $apiController->index();
        $response     = $jsonResource->toResponse($request);
        $payload      = json_decode($response->getContent(), true);
        $tenants      = $payload['data'] ?? [];

        return view('admin::user.superAdmin.tenants.index', compact('tenants'));
    }

    /**
     * Exibe formulário de criação de tenant
     */
    public function tenantCreate()
    {
        return view('admin::user.superAdmin.tenants.create');
    }

    /**
     * Persiste um novo tenant
     */
    public function tenantStore(Request $request, ApiCtrl $apiController)
    {
        $jsonResource = $apiController->store();
        $response     = $jsonResource->toResponse($request);
        $payload      = json_decode($response->getContent(), true);

        if (in_array($response->getStatusCode(), [200, 201])) {
            return redirect()
                ->route('superAdmin.tenants.index')
                ->with('success', $payload['message'] ?? 'Tenant criado com sucesso');
        }

        return back()
            ->withErrors(['error' => $payload['message'] ?? 'Erro ao criar tenant'])
            ->withInput();
    }

    /**
     * Exibe formulário de edição de um tenant
     */
    public function tenantEdit(Request $request, $id, ApiCtrl $apiController)
    {
        $jsonResource = $apiController->show($id);
        $response     = $jsonResource->toResponse($request);
        $payload      = json_decode($response->getContent(), true);

        if (!is_array($payload)) {
            throw new \RuntimeException('Payload inválido da API');
        }

        $name   = $payload['data']['name'] 
                ?? $payload['name'] 
                ?? 'Nome não encontrado';

        $tenant = [
            'id'               => $payload['id'] ?? $id,
            'name'             => $name,
            'multiatendedor_id'=> $payload['multiatendedor_id'] ?? null,
            'domains'          => $payload['domains'] ?? []
        ];

        return view('admin::user.superAdmin.tenants.edit', compact('tenant'));
    }

    /**
     * Atualiza um tenant existente
     */
    public function tenantUpdate(Request $request, $id, ApiCtrl $apiController)
    {
        $jsonResource = $apiController->update($id);
        $response     = $jsonResource->toResponse($request);
        $payload      = json_decode($response->getContent(), true);

        if (isset($payload['data'])) {
            return redirect()
                ->route('superAdmin.tenants.index')
                ->with('success', $payload['message']);
        }

        return back()
            ->withErrors(['error' => $payload['message']])
            ->withInput();
    }

    /**
     * Exclui um tenant
     */
    public function tenantDestroy($id, ApiCtrl $apiController)
    {
        $jsonResource = $apiController->destroy($id);
        $response     = $jsonResource->toResponse(request());
        $payload      = json_decode($response->getContent(), true);

        if ($response->getStatusCode() === 200) {
            return redirect()
                ->route('superAdmin.tenants.index')
                ->with('success', $payload['message'] ?? 'Tenant excluído com sucesso');
        }

        return back()
            ->withErrors(['error' => $payload['message'] ?? 'Erro ao excluir tenant']);
    }
}
