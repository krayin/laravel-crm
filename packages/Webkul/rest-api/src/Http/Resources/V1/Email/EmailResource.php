<?php

namespace Webkul\RestApi\Http\Resources\V1\Email;

use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Admin\Http\Resources\EmailAttachmentResource;
use Webkul\RestApi\Http\Resources\V1\Contact\PersonResource;
use Webkul\RestApi\Http\Resources\V1\Lead\LeadResource;

class EmailResource extends JsonResource
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
            'id'            => $this->id,
            'subject'       => $this->subject,
            'source'        => $this->source,
            'user_type'     => $this->user_type,
            'name'          => $this->name,
            'reply'         => $this->reply,
            'is_read'       => $this->is_read,
            'folders'       => $this->folders,
            'from'          => $this->from,
            'sender'        => $this->sender,
            'reply_to'      => $this->reply_to,
            'cc'            => $this->cc,
            'bcc'           => $this->bcc,
            'unique_id'     => $this->unique_id,
            'message_id'    => $this->message_id,
            'reference_ids' => $this->reference_ids,
            'person'        => new PersonResource($this->person),
            'lead'          => new LeadResource($this->lead),
            'parent_id'     => $this->parent_id,
            'parent'        => new EmailResource($this->parent),
            'attachments'   => EmailAttachmentResource::collection($this->attachments),
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at,
        ];
    }
}
