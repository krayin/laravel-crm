<?php

namespace Webkul\Admin\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request
     * @return array
     */
    public function toArray($request)
    {
        $isModel = $this->resource instanceof \Illuminate\Database\Eloquent\Model;

        return [
            'id'                  => $this->id,
            'parent_id'           => $this->parent_id ?? null,
            'parent_activity_id'  => $this->parent_activity_id ?? null,
            'title'               => $this->title,
            'type'                => $this->type,
            'comment'             => $this->comment,
            'additional'          => is_array($this->resource->additional) ? $this->resource->additional : json_decode($this->resource->additional, true),
            'schedule_from'       => $this->schedule_from,
            'schedule_to'         => $this->schedule_to,
            'is_done'             => $this->is_done,
            'user'                => new UserResource($this->user),
            'files'               => ActivityFileResource::collection($this->files),
            'participants'        => ActivityParticipantResource::collection($this->participants),
            'location'            => $this->location,
            'notes_count'         => $isModel ? ($this->notes_count ?? 0) : 0,
            'notes'               => $isModel && $this->resource->relationLoaded('notes')
                ? $this->notes->map(fn ($note) => [
                    'id'         => $note->id,
                    'comment'    => $note->comment,
                    'created_at' => $note->created_at,
                    'user'       => $note->user ? ['id' => $note->user->id, 'name' => $note->user->name] : null,
                ])->values()
                : [],
            'created_at'          => $this->created_at,
            'updated_at'          => $this->updated_at,
        ];
    }
}
