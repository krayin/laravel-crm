<?php

namespace Webkul\Automation\Models;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;
use Webkul\Automation\Contracts\Workflow as WorkflowContract;

class Workflow extends Model implements WorkflowContract
{
    use BelongsToTenant;

    protected $casts = [
        'conditions' => 'array',
        'actions'    => 'array',
    ];

    protected $fillable = [
        'name',
        'description',
        'entity_type',
        'event',
        'condition_type',
        'conditions',
        'actions',
        'tenant_id',
    ];
}
