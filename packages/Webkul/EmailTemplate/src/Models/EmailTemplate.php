<?php

namespace Webkul\EmailTemplate\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\EmailTemplate\Contracts\EmailTemplate as EmailTemplateContract;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class EmailTemplate extends Model implements EmailTemplateContract
{
    use BelongsToTenant;
    
    protected $fillable = [
        'name',
        'subject',
        'content',
    ];
}
