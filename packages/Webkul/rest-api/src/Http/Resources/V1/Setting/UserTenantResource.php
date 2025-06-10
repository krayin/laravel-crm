<?php

namespace Webkul\RestApi\Http\Resources\V1\Setting;

use Illuminate\Http\Resources\Json\JsonResource;

class UserTenantResource extends JsonResource
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
            'id'                    => $this->id,
            'role_id'               => $this->role_id,
            'status'                => $this->status,
            'view_permission'       => $this->view_permission,
            'role'                  => $this->role,
        ];
    }
}