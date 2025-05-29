<?php

namespace Webkul\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;
use Webkul\Core\Contracts\CoreConfig as CoreConfigContract;

class CoreConfig extends Model implements CoreConfigContract
{
    use BelongsToTenant;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'core_config';

    protected $fillable = [
        'code',
        'value',
        'locale',
    ];

    protected $hidden = ['token'];
}
