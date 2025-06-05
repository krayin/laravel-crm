<?php

namespace Webkul\WebForm\Models;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;
use Webkul\WebForm\Contracts\WebForm as WebFormContract;

class WebForm extends Model implements WebFormContract
{
    use BelongsToTenant;

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
        'tenant_id',
    ];

    /**
     * The attributes that belong to the activity.
     */
    public function attributes()
    {
        return $this->hasMany(WebFormAttributeProxy::modelClass());
    }
}
