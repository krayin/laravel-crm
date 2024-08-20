<?php

namespace Webkul\EmailTemplate\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\EmailTemplate\Contracts\EmailTemplate as EmailTemplateContract;

class EmailTemplate extends Model implements EmailTemplateContract
{
    protected $fillable = [
        'name',
        'subject',
        'content',
    ];
}
