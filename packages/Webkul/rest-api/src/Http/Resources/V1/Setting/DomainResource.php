<?php

namespace Webkul\RestApi\Http\Resources\V1\Setting;

use Illuminate\Http\Resources\Json\JsonResource;

class DomainResource extends JsonResource
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
            'id'         => $this->id,
            'domain'     => $this->domain,
            'tenant_id'  => $this->tenant_id,
        ];
    }
}
