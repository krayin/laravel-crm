<?php

namespace Webkul\RestApi\Http\Resources\V1\Setting;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'       => $this->id,
            'name'     => $this->name,
            'email'    => $this->email,
            'image'    => $this->image,
            'image_url'=> $this->image_url,
            'tenants'  => $this->tenantPivots->map(function($pivot) {
                return [
                    'tenant_id'   => $pivot->tenant->id ?? null,
                    'role_id'     => $pivot->role_id,
                    'status'      => $pivot->status,
                    'view_permission' => $pivot->view_permission,
                    'role'        => $pivot->role
                ];
            }),
        ];
    }
}