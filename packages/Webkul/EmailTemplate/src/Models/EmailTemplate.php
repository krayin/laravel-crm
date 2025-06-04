<?php

namespace Webkul\EmailTemplate\Models;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;
use Webkul\EmailTemplate\Contracts\EmailTemplate as EmailTemplateContract;

class EmailTemplate extends Model implements EmailTemplateContract
{
    use BelongsToTenant;

    protected $fillable = [
        'name',
        'subject',
        'content',
        'tenant_id',
    ];
}
