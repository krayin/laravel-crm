<?php

namespace Webkul\RestApi\Http\Resources\V1\Setting;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'              => $this->id,
            'name'            => $this->name,
            'email'           => $this->email,
            'status'          => $this->status,
            'view_permission' => $this->view_permission,
            'role'            => new RoleResource($this->role),
            'created_at'      => $this->created_at,
            'updated_at'      => $this->updated_at,
            'image'           => $this->image,
            'image_url'       => $this->image_url,
        ];
    }
}
