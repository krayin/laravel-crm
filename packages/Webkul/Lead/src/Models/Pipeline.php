<?php

namespace Webkul\Lead\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Lead\Contracts\Pipeline as PipelineContract;

class Pipeline extends Model implements PipelineContract
{
    protected $table = 'lead_pipelines';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'is_default',
    ];

    /**
     * Get the leads.
     */
    public function leads()
    {
        return $this->hasMany(LeadProxy::modelClass());
    }

    /**
     * Get the stages that owns the pipline.
     */
    public function stages()
    {
        return $this->hasMany(PipelineStageProxy::modelClass());
    }
}
