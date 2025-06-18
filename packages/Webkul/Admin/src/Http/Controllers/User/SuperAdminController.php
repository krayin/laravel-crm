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
    public function userEdit(Request $request, $id, ApiUserCtrl $apiController, ApiUserTenantCtrl $apiUserTenantCtrl, ApiTenantCtrl $apiTenantCtrl)
    {
        $jsonResource = $apiController->show($id);
    
        $userModel = $jsonResource->resource;
    
        // Garante que tenantPivots é uma coleção antes de usar first()
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
            'tenants'           => [], // Inicializa a propriedade 'tenants' como um array vazio
        ];
    
        // Verifica se tenantPivots existe e não está vazio
        if ($userModel->tenantPivots && $userModel->tenantPivots->isNotEmpty()) {
            // Coleta os IDs dos pivôs (user_tenant.id) e os IDs dos tenants (tenant_id)
            $userTenantPivotsData = $userModel->tenantPivots->mapWithKeys(function ($pivot) {
                return [$pivot->tenant_id => $pivot->id]; // tenant_id => user_tenant_id
            })->toArray();
        
            $tenantIds = array_keys($userTenantPivotsData); // Apenas os IDs dos tenants
            
            $tenantsData = [];
        
            foreach ($tenantIds as $tenantId) {
                $tenantJsonResource = $apiTenantCtrl->show($tenantId);
                
                // Acessa o recurso do tenant (que é o modelo Tenant ou um array)
                $tenantData = $tenantJsonResource->resource;
                
                if ($tenantData) {
                    $tenantName = null;
                    // Verifica se a propriedade 'data' existe e é uma string
                    if (isset($tenantData->data) && is_string($tenantData->data)) {
                        $decodedData = json_decode($tenantData->data);
                        if ($decodedData && isset($decodedData->name)) {
                            $tenantName = $decodedData->name;
                        }
                    }
    
                    $tenantsData[] = (object) [
                        'id'            => $tenantData->id, // ID do tenant
                        'name'          => $tenantName,     // Nome do tenant obtido da string JSON
                        'connection_id' => $userTenantPivotsData[$tenantData->id], // ID da conexão user_tenant
                    ];
                }
            }
            $user->tenants = $tenantsData;
        }
    
        return view('admin::user.superAdmin.users.edit', compact('user'));
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
}
