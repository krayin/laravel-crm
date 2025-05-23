<?php

namespace Webkul\RestApi\Http\Resources\V1\Setting;

use Illuminate\Http\Resources\Json\JsonResource;

class WebFormResource extends JsonResource
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
            'form_id'                  => $this->form_id,
            'title'                    => $this->title,
            'description'              => $this->description,
            'submit_button_label'      => $this->submit_button_label,
            'submit_success_action'    => $this->submit_success_action,
            'submit_success_content'   => $this->submit_success_content,
            'create_lead'              => $this->create_lead,
            'background_color'         => $this->background_color,
            'form_background_color'    => $this->form_background_color,
            'form_title_color'         => $this->form_title_color,
            'form_submit_button_color' => $this->form_submit_button_color,
            'attribute_label_color'    => $this->attribute_label_color,
        ];
    }
}
