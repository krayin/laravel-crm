<?php

namespace Webkul\Lead\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Lead\Contracts\Type as TypeContract;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Type extends Model implements TypeContract
{
    use BelongsToTenant;
    protected $table = 'lead_types';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    /**
     * Get the leads.
     */
    public function leads()
    {
        return $this->hasMany(LeadProxy::modelClass());
    }
}
