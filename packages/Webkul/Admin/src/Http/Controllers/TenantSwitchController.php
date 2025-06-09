<?php

namespace Webkul\Admin\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Webkul\User\Models\UserTenant;
use Webkul\User\Models\User;
use Webkul\Tenant\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;

class TenantSwitchController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        
        $tenants = UserTenant::withoutGlobalScopes()
            ->where('user_id', $user->id)
            ->with('tenant.domains')
            ->get()
            ->map(function ($userTenant) {
                $tenant = $userTenant->tenant;
                $domain = optional($tenant->domains->first())->domain;
    
                return [
                    'tenant_id' => $tenant->id,
                    'domain' => $domain,
                    'name' => json_decode($tenant->data)->name ?? null,
                ];
            });;
        return json_encode($tenants);
    }

    public function switch(Request $request)
    {
        $tenantId = $request->input('tenant_id');
        $tenant = Tenant::findOrFail($tenantId);
        $tenantDomain = $tenant->domains->first()->domain;

        $userId = auth()->id();
        $randomText = Str::random(16);
        $expiresAt = now()->addMinute()->timestamp;

        $data = [
            'r' => $randomText,
            'u' => $userId,
            'e' => $expiresAt,
        ];

        $encrypted = Crypt::encryptString(json_encode($data));

        $token = rtrim(strtr(base64_encode($encrypted), '+/', '-_'), '=');

        $url = "http://$tenantDomain:8000/switch-login?token=$token";
        return redirect()->away($url);
    }

    public function handle_switch(Request $request)
    {
        $token = $request->query('token');

        $encrypted = base64_decode(strtr($token, '-_', '+/'));

        try {
            $data = json_decode(Crypt::decryptString($encrypted), true);
        } catch (\Exception $e) {
            abort(403, 'Token inválido.');
        }

        if (now()->timestamp > $data['e']) {
            abort(403, 'Token expirado.');
        }

        $user = \App\Models\User::find($data['u']);

        if (! $user) {
            abort(403, 'Usuário não encontrado.');
        }

        auth()->guard('user')->login($user);  

        return redirect()->route('admin.dashboard.index');
    }

}