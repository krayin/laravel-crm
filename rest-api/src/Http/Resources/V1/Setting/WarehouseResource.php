<?php

namespace Webkul\RestApi\Http\Resources\V1\Setting;

use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\RestApi\Http\Resources\V1\Activity\ActivityResource;

class WarehouseResource extends JsonResource
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
            'description'     => $this->description,
            'contact_numbers' => $this->contact_numbers,
            'contact_emails'  => $this->contact_emails,
            'contact_address' => $this->contact_address,
            'location'        => LocationResource::collection($this->locations),
            'tags'            => TagResource::collection($this->tags),
            'activities'      => ActivityResource::collection($this->activities),
        ];
    }
}
