<?php

namespace Webkul\Admin\Http\Controllers\User;

use Webkul\Admin\Http\Controllers\Controller;                               
use Webkul\RestApi\Http\Controllers\V1\Setting\TenantController as ApiTenantCtrl;
use Webkul\RestApi\Http\Controllers\V1\Setting\UserController   as ApiUserCtrl;
use Webkul\RestApi\Http\Controllers\V1\Setting\UserTenantController as ApiUserTenantCtrl;

use Illuminate\Http\Request;

class SuperAdminController extends Controller
{
    // ===== Métodos de Tenant (prefixados com "tenant") =====

    /**
     * Lista todos os tenants
     */
    public function tenantIndex(Request $request, ApiTenantCtrl $apiController)
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
    public function tenantStore(Request $request, ApiTenantCtrl $apiController)
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
    public function tenantEdit(Request $request, $id, ApiTenantCtrl $apiController)
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
    public function tenantUpdate(Request $request, $id, ApiTenantCtrl $apiController)
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
    public function tenantDestroy($id, ApiTenantCtrl $apiController)
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



    // ===== Métodos de User (prefixados com "user") =====

    /**
     * Lista todos os usuários
     */
    public function userIndex(Request $request, ApiUserCtrl $apiController)
    {
        $jsonResource = $apiController->index();
        $response     = $jsonResource->toResponse($request);
        $payload      = json_decode($response->getContent(), true);
        $users        = $payload['data'] ?? [];

        return view('admin::user.superAdmin.users.index', compact('users'));
    }

    /**
     * Exibe formulário de criação de usuário
     */
    public function userCreate()
    {
        return view('admin::user.superAdmin.users.create');
    }

    /**
     * Persiste um novo usuário
     */
    public function userStore(Request $request, ApiUserCtrl $apiController)
    {
        $jsonResource = $apiController->store();
        $response     = $jsonResource->toResponse($request);
        $payload      = json_decode($response->getContent(), true);

        if (in_array($response->getStatusCode(), [200, 201])) {
            return redirect()
                ->route('superAdmin.users.index')
                ->with('success', $payload['message'] ?? 'Usuário criado com sucesso');
        }

        return back()
            ->withErrors(['error' => $payload['message'] ?? 'Erro ao criar usuário'])
            ->withInput();
    }

    /**
     * Exibe formulário de edição de um usuário
     */
    public function userEdit(
        Request $request,
        $id,
        ApiUserCtrl $apiController,
        ApiUserTenantCtrl $apiUserTenantCtrl,
        ApiTenantCtrl $apiTenantCtrl
    ) {
        $jsonResource = $apiController->show($id);
        $userModel    = $jsonResource->resource;
    
        $firstPivot = $userModel->tenantPivots->first();
    
        $user = (object) [
            'id'                => $userModel->id,
            'name'              => $userModel->name,
            'email'             => $userModel->email,
            'is_super'          => $userModel->is_super,
            'multiatendedor_id' => $userModel->multiatendedor_id,
            'role_id'           => $firstPivot->role_id       ?? null,
            'status'            => $firstPivot->status        ?? null,
            'view_permission'   => $firstPivot->view_permission ?? null,
            'groups'            => [],
            'tenants'           => [],
        ];
    
        if ($userModel->tenantPivots->isNotEmpty()) {
            $pivotMap = $userModel->tenantPivots
                ->mapWithKeys(fn($p) => [$p->tenant_id => [
                    'pivot_id' => $p->id,
                    'role_id' => $p->role_id  // Adiciona role_id ao mapeamento
                ]])
                ->toArray();
        
            foreach (array_keys($pivotMap) as $tenantId) {
                $tRes = $apiTenantCtrl->show($tenantId)->resource;
                $data_decoded = json_decode($tRes->data, true);
                $name = $data_decoded['name'] ?? null;
        
                $user->tenants[] = (object) [
                    'id'            => $tRes->id,
                    'name'          => $name,
                    'connection_id' => $pivotMap[$tRes->id]['pivot_id'],
                    'role_id'       => $pivotMap[$tRes->id]['role_id']  // Adiciona role_id aqui
                ];
            }
        }
        
    
        $allTenantsRes = $apiTenantCtrl->index();
        $respAll       = $allTenantsRes->toResponse($request);
        $payloadAll    = json_decode($respAll->getContent(), true);
        $allTenants    = $payloadAll['data'] ?? [];
    
        $associatedIds     = collect($user->tenants)->pluck('id')->all();
        $availableTenants  = array_filter(
            $allTenants,
            fn(array $t) => ! in_array($t['id'], $associatedIds, true)
        );
    
        return view(
            'admin::user.superAdmin.users.edit',
            compact('user', 'availableTenants')
        );
    }
    

    /**
     * Atualiza um usuário existente
     */
    public function userUpdate(Request $request, $id, ApiUserCtrl $apiController)
    {
        $jsonResource = $apiController->update($id);
        $response     = $jsonResource->toResponse($request);
        $payload      = json_decode($response->getContent(), true);

        if (isset($payload['data'])) {
            return redirect()
                ->route('superAdmin.users.index')
                ->with('success', $payload['message'] ?? 'Usuário atualizado com sucesso');
        }

        return back()
            ->withErrors(['error' => $payload['message'] ?? 'Erro ao atualizar usuário'])
            ->withInput();
    }

    /**
     * Exclui um usuário
     */
    public function userDestroy($id, ApiUserCtrl $apiController)
    {
        $jsonResource = $apiController->destroy($id);
        $response     = $jsonResource->toResponse(request());
        $payload      = json_decode($response->getContent(), true);

        if ($response->getStatusCode() === 200) {
            return redirect()
                ->route('superAdmin.users.index')
                ->with('success', $payload['message'] ?? 'Usuário excluído com sucesso');
        }

        return back()
            ->withErrors(['error' => $payload['message'] ?? 'Erro ao excluir usuário']);
    }

    public function userTenantStore(Request $request, $userId, $tenantId, ApiUserTenantCtrl $apiUserTenantCtrl)
{
    // Dados fixos (como no Postman)
    $fixedData = [
        'status'          => 1,          // Valor fixo
        'view_permission' => 'global',    // Valor fixo
        'groups'          => []           // Valor fixo (array vazio)
    ];

    // Sobrescreve user_id e tenant_id com os valores da rota
    // E usa o role_id da requisição
    $requestData = array_merge($fixedData, [
        'user_id'   => $userId,
        'tenant_id' => $tenantId,
        'role_id'   => $request->input('role_id', 1) // Pega do formulário ou usa 1 como padrão
    ]);

    // Substitui os dados da requisição atual pelos dados ajustados
    $request->replace($requestData);

    try {
        // Chama o store() do ApiUserTenantCtrl (que já valida tudo)
        $jsonResource = $apiUserTenantCtrl->store();
        $payload = $jsonResource->toResponse($request)->getData(true);

        // Redireciona para a edição do usuário com mensagem de sucesso
        return redirect()
            ->route('superAdmin.users.edit', ['user' => $userId])
            ->with('success', $payload['message'] ?? 'Usuário vinculado ao tenant com sucesso!');

    } catch (\Exception $e) {
        return back()
            ->withErrors(['error' => $e->getMessage() ?? 'Erro ao vincular usuário ao tenant'])
            ->withInput();
    }
}
    public function userTenantDestroy($id, ApiUserTenantCtrl $apiUserTenantCtrl)
    {
        $jsonResource = $apiUserTenantCtrl->destroy($id);
        $response     = $jsonResource->toResponse(request());
        $payload      = json_decode($response->getContent(), true);

        if ($response->getStatusCode() === 200) {
            return back()
                ->with('success', $payload['message'] ?? 'Associação Usuário-Tenant excluído com sucesso');
        }

        return back()
            ->withErrors(['error' => $payload['message'] ?? 'Erro ao excluir associação usuário-tenant']);
    }

    public function userTenantUpdate(Request $request, $id, ApiUserCtrl $apiController)
    {
        $jsonResource = $apiController->tenantUpdate($id);
        $response     = $jsonResource->toResponse($request);
        $payload      = json_decode($response->getContent(), true);

        if (isset($payload['data'])) {
            return redirect()
                ->route('superAdmin.users.index')
                ->with('success', $payload['message'] ?? 'Usuário atualizado com sucesso');
        }

        return back()
            ->withErrors(['error' => $payload['message'] ?? 'Erro ao atualizar usuário'])
            ->withInput();
    }
}
