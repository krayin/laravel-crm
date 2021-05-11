<?php

namespace Webkul\Lead\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Attribute\Traits\CustomAttribute;
use Webkul\Lead\Contracts\Lead as LeadContract;

class Lead extends Model implements LeadContract
{
    use CustomAttribute;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'description',
        'lead_value',
        'status',
        'lost_reason',
        'lost_at',
        'won_at',
        'closed_at',
        'user_id',
        'lead_source_id',
        'lead_type_id',
        'lead_pipeline_id',
        'lead_stage_id',
    ];

    /**
     * Get the type that owns the lead.
     */
    public function type()
    {
        return $this->belongsTo(TypeProxy::modelClass());
    }

    /**
     * Get the source that owns the lead.
     */
    public function source()
    {
        return $this->belongsTo(SourceProxy::modelClass());
    }

    /**
     * Get the pipeline that owns the lead.
     */
    public function pipeline()
    {
        return $this->belongsTo(PipelineProxy::modelClass());
    }

    /**
     * Get the stage that owns the lead.
     */
    public function stage()
    {
        return $this->belongsTo(StageProxy::modelClass());
    }

    /**
     * Get the products.
     */
    public function products()
    {
        return $this->hasMany(ProductProxy::modelClass());
    }
}
