<?php

namespace Webkul\Admin\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityParticipantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'user'   => $this->when($this->user, new UserResource($this->user)),
            'person' => $this->when($this->person, new PersonResource($this->person)),
        ];
    }
}
