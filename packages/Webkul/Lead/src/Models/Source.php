<?php

namespace Webkul\Lead\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Lead\Contracts\Source as SourceContract;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;
class Source extends Model implements SourceContract
{
    use BelongsToTenant;
    protected $table = 'lead_sources';

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
