<?php

namespace Webkul\Admin\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LeadResource extends JsonResource
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
            'id'                   => $this->id,
            'title'                => $this->title,
            'lead_value'           => $this->lead_value,
            'formatted_lead_value' => core()->formatBasePrice($this->lead_value),
            'status'               => $this->status,
            'expected_close_date'  => $this->expected_close_date,
            'rotten_days'          => $this->rotten_days,
            'closed_at'            => $this->closed_at,
            'created_at'           => $this->created_at,
            'updated_at'           => $this->updated_at,
            'person'               => new PersonResource($this->person),
            'user'                 => new UserResource($this->user),
            'type'                 => new TypeResource($this->type),
            'source'               => new SourceResource($this->source),
            'pipeline'             => new PipelineResource($this->pipeline),
            'stage'                => new StageResource($this->stage),
            'tags'                 => TagResource::collection($this->tags),
        ];
    }
}
