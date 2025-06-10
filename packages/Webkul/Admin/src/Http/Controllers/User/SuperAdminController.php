<?php

namespace Webkul\Admin\Http\Controllers\User;

use Webkul\Admin\Http\Controllers\Controller;                               // Controller base do Webkul
use Webkul\RestApi\Http\Controllers\V1\Setting\TenantController as ApiCtrl; // o controller da API
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SuperAdminController extends Controller
{
    public function index(Request $request, ApiCtrl $apiController)
    {
        $jsonResource = $apiController->index();

        $response    = $jsonResource->toResponse($request);
        $rawJson     = $response->getContent();

        $payload = json_decode($rawJson, true);
        $tenants = $payload['data'] ?? [];

        return view('admin::user.superAdmin.tenants.index', compact('tenants'));
    }

    public function edit(Request $request, $id, ApiCtrl $apiController)
    {
        $jsonResource = $apiController->show($id);
        $response = $jsonResource->toResponse($request);
        $payload = json_decode($response->getContent(), true);

        if (!is_array($payload)) {
            throw new \RuntimeException('Payload inválido da API');
        }

        $name = $payload['data']['name'] 
            ?? $payload['name'] 
            ?? 'Nome não encontrado';


        $tenant = [
            'id' => $payload['id'] ?? $id,
            'name' => $name,
            'multiatendedor_id' => $payload['multiatendedor_id'] ?? null,
            'domains' => $payload['domains'] ?? []
        ];

        return view('admin::user.superAdmin.tenants.edit', compact('tenant'));
    }


    public function update(Request $request, $id, ApiCtrl $apiController)
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

    public function destroy($id, ApiCtrl $apiController)
    {
        $jsonResource = $apiController->destroy($id);
        $response = $jsonResource->toResponse(request());
        $payload = json_decode($response->getContent(), true);

        if ($response->getStatusCode() === 200) {
            return redirect()
                ->route('superAdmin.tenants.index')
                ->with('success', $payload['message'] ?? 'Tenant excluído com sucesso');
        }

        return back()
            ->withErrors(['error' => $payload['message'] ?? 'Erro ao excluir tenant']);
    }


}
