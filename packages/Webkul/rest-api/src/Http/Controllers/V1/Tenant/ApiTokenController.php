<?php

namespace Webkul\RestApi\Http\Controllers\V1\Tenant;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Stancl\Tenancy\Database\Models\Tenant;
use Webkul\RestApi\Http\Controllers\V1\Controller;

class ApiTokenController extends Controller
{
    protected function authorizeSuper()
    {
        $user = Auth::user();

        if (!$user || !$user->is_super) {
            return response()->json([
                'message' => trans('rest-api::app.common.forbidden-error'),
            ], 401);
        }

        return null;
    }

    public function show($id)
    {
        if ($response = $this->authorizeSuper()) {
            return $response;
        }

        try {
            $tenant = Tenant::findOrFail($id);

            return response()->json([
                'has_token' => (bool) $tenant->api_token,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => trans('rest-api::app.token.tenant_not_found'),
            ], 404);
        }
    }

    public function store($id)
    {
        if ($response = $this->authorizeSuper()) {
            return $response;
        }

        try {
            $plainToken = Str::random(40);
            $updated = Tenant::where('id', $id)->update([
                'api_token' => hash('sha256', $plainToken),
            ]);

            if (!$updated) {
                return response()->json(['error' => trans('rest-api::app.token.tenant_not_found')], 404);
            }

            return response()->json([
                'token' => $plainToken,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => trans('rest-api::app.token.generate_error'),
            ], 500);
        }
    }

    public function destroy($id)
    {
        if ($response = $this->authorizeSuper()) {
            return $response;
        }

        try {
            $updated = Tenant::where('id', $id)->update([
                'api_token' => null,
            ]);

            if (!$updated) {
                return response()->json(['error' => trans('rest-api::app.token.tenant_not_found')], 404);
            }

            return new JsonResource([
                'message' => trans('rest-api::app.token.delete_success'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => trans('rest-api::app.token.delete_error'),
            ], 500);
        }
    }
}
