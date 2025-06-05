<?php

namespace Webkul\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Domain\Contracts\Domain as DomainContract;

class Domain extends Model implements DomainContract
{
    protected $table = 'domains';

    protected $fillable = [
        'id',
        'domain',
        'tenant_id',
        'created_at',
        'updated_at',
    ];
}
