<?php

namespace Webkul\WebForm\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\WebForm\Contracts\WebForm as WebFormContract;

class WebForm extends Model implements WebFormContract
{
    protected $fillable = [
        'form_id',
        'title',
        'description',
        'submit_button_label',
        'submit_success_action',
        'submit_success_content',
        'create_lead',
        'background_color',
        'form_background_color',
        'form_title_color',
        'form_submit_button_color',
        'attribute_label_color',
    ];

    /**
     * The attributes that belong to the activity.
     */
    public function attributes()
    {
        return $this->hasMany(WebFormAttributeProxy::modelClass());
    }
}
