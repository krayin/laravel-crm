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
        // 1) Executa o método index() do controller da API
        $jsonResource = $apiController->index();

        // 2) Transforma em resposta HTTP e obtém o JSON bruto
        $response    = $jsonResource->toResponse($request);
        $rawJson     = $response->getContent();

        // 3) Log para debugar em storage/logs/laravel.log
        Log::info('TENANTS RAW JSON: ' . $rawJson);

        // 4) Decodifica e extrai só o array “data”
        $payload = json_decode($rawJson, true);
        $tenants = $payload['data'] ?? [];

        // 5) Passa para a view
        return view('admin::user.superAdmin.index', compact('tenants'));
    }
}
