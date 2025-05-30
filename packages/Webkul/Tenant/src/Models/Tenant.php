<?php

namespace Webkul\Tenant\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Tenant\Contracts\Tenant as TenantContract;

class Tenant extends Model implements TenantContract
{
    protected $table = 'tenants';

    protected $fillable = [
        'id',
        'created_at',
        'updated_at',
        'data',
        'multiatendedor_id',
    ];

    public function domain()
    {
        return $this->belongsTo(DomainProxy::modelClass());
    }
}
