<?php

namespace Webkul\RestApi\Http\Resources\V1\Setting;

use Illuminate\Http\Resources\Json\JsonResource;

class WebhookResource extends JsonResource
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
            'id'               => $this->id,
            'name'             => $this->name,
            'entity_type'      => $this->entity_type,
            'description'      => $this->description,
            'method'           => $this->method,
            'end_point'        => $this->end_point,
            'query_params'     => $this->query_params,
            'headers'          => $this->headers,
            'payload_type'     => $this->payload_type,
            'raw_payload_type' => $this->raw_payload_type,
            'payload'          => $this->payload,
            'created_at'       => $this->created_at,
            'updated_at'       => $this->updated_at,
        ];
    }
}
