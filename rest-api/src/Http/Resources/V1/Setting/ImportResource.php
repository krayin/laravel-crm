<?php

namespace Webkul\RestApi\Http\Resources\V1\Setting;

use Illuminate\Http\Resources\Json\JsonResource;

class ImportResource extends JsonResource
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
            'id'                   => $this->id,
            'state'                => $this->state,
            'process_in_queue'     => (bool) $this->process_in_queue,
            'type'                 => $this->type,
            'action'               => $this->action,
            'validation_strategy'  => $this->validation_strategy,
            'allowed_errors'       => $this->allowed_errors,
            'processed_rows_count' => $this->processed_rows_count,
            'invalid_rows_count'   => $this->invalid_rows_count,
            'errors_count'         => $this->errors_count,
            'errors'               => $this->errors,
            'field_separator'      => $this->field_separator,
            'file_path'            => $this->file_path,
            'error_file_path'      => $this->error_file_path,
            'summary'              => $this->summary,
            'started_at'           => $this->started_at,
            'completed_at'         => $this->completed_at,
            'created_at'           => $this->created_at,
            'updated_at'           => $this->updated_at,
        ];
    }
}
