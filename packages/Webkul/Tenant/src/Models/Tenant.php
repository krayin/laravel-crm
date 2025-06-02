<?php

namespace Webkul\Tenant\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Tenant\Contracts\Tenant as TenantContract;
use Webkul\Domain\Models\Domain;

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
 
    public function domains() {
        return $this->hasMany(Domain::class, 'tenant_id');  
    }

}
