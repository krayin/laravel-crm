<?php

namespace Webkul\Client\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Client\Contracts\Client as ClientContract;

class Client extends Model implements ClientContract
{
    protected $fillable = [
        'name',
        'email',
        'logo',
        'contact_person_name',
        'contact_person_mobile',
        'contact_person_email',
        'gst_no',
        'pan_no',
        'billing_address',
        'status',
    ];
}
