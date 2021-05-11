<?php

namespace Webkul\Lead\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Lead\Contracts\PipelineStage as PipelineStageContract;

class PipelineStage extends Model implements PipelineStageContract
{
    public $timestamps = false;
    
    protected $table = 'lead_pipeline_stages';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'probability',
        'sort_order',
        'lead_stage_id',
        'lead_pipeline_id',
    ];

    /**
     * Get the pipeline that owns the pipeline stage.
     */
    public function pipeline()
    {
        return $this->belongsTo(PipelineProxy::modelClass());
    }

    /**
     * Get the stage that owns the pipeline stage.
     */
    public function stage()
    {
        return $this->belongsTo(StageProxy::modelClass());
    }
}
