<?php

namespace Webkul\Tenant\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Domain\Models\Domain;
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
        'lead_custom_fields_count',
    ];

    public function domains()
    {
        return $this->hasMany(Domain::class, 'tenant_id');
    }
}
