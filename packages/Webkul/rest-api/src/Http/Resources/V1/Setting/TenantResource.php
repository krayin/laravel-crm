<?php

namespace Webkul\RestApi\Http\Resources\V1\Setting;

use Illuminate\Http\Resources\Json\JsonResource;

class TenantResource extends JsonResource
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
            'id'                       => $this->id,
            'data'                     => json_decode($this->data, true),
            'multiatendedor_id'        => $this->multiatendedor_id,
            'domains'                  => DomainResource::collection($this->whenLoaded('domains')),
            'created_at'               => $this->created_at,
            'updated_at'               => $this->updated_at,
            'lead_custom_fields_count' => $this->lead_custom_fields_count,
        ];
    }
}
