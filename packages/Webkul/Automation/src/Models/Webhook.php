<?php

namespace Webkul\Automation\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Webkul\Automation\Contracts\Webhook as ContractsWebhook;

class Webhook extends Model implements ContractsWebhook
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'entity_type',
        'description',
        'end_point',
    ];
}
