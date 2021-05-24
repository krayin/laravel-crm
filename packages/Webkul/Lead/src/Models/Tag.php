<?php

namespace Webkul\Lead\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Lead\Contracts\Tag as TagContract;

class Tag extends Model implements TagContract
{
    protected $table = 'lead_tags';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'lead_id',
    ];

    /**
     * Get the lead that owns the tag.
     */
    public function lead()
    {
        return $this->belongsTo(LeadProxy::modelClass());
    }
}
