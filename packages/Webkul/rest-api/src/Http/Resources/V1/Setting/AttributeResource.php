<?php

namespace Webkul\RestApi\Http\Resources\V1\Setting;

use Illuminate\Http\Resources\Json\JsonResource;

class AttributeResource extends JsonResource
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
            'code'            => $this->code,
            'name'            => $this->name,
            'type'            => $this->type,
            'lookup_type'     => $this->lookup_type,
            'entity_type'     => $this->entity_type,
            'sort_order'      => $this->sort_order,
            'validation'      => $this->validation,
            'is_required'     => $this->is_required,
            'is_unique'       => $this->is_unique,
            'quick_add'       => $this->quick_add,
            'is_user_defined' => $this->is_user_defined,
            'options'         => $this->options,
            'created_at'      => $this->created_at,
            'updated_at'      => $this->updated_at,
        ];
    }
}
