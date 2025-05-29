<?php

namespace Webkul\RestApi\Http\Resources\V1\Lead;

use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\RestApi\Http\Resources\V1\Contact\PersonResource;
use Webkul\RestApi\Http\Resources\V1\Product\LeadProductResource;
use Webkul\RestApi\Http\Resources\V1\Setting\UserResource;

class LeadResource extends JsonResource
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
            'id'                     => $this->id,
            'title'                  => $this->title,
            'description'            => $this->description,
            'lead_value'             => $this->lead_value,
            'status'                 => $this->status,
            'lost_reason'            => $this->lost_reason,
            'closed_at'              => $this->closed_at,
            'user'                   => new UserResource($this->user),
            'person'                 => new PersonResource($this->person),
            'lead_products'          => LeadProductResource::collection($this->products),
            'lead_source_id'         => $this->lead_source_id,
            'lead_type_id'           => $this->lead_type_id,
            'lead_pipeline_id'       => $this->lead_pipeline_id,
            'lead_pipeline_stage_id' => $this->lead_pipeline_stage_id,
            'created_at'             => $this->created_at,
            'updated_at'             => $this->updated_at,
            'expected_close_date'    => $this->expected_close_date,
        ];
    }
}
