<?php

namespace Webkul\DataGrid\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Webkul\DataGrid\Contracts\SavedFilter as SavedFilterContract;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class SavedFilter extends Model implements SavedFilterContract
{
    use HasFactory, BelongsToTenant;

    /**
     * Deinfine model table name.
     *
     * @var string
     */
    protected $table = 'datagrid_saved_filters';

    /**
     * Fillable property for the model.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'src',
        'name',
        'applied',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'applied' => 'json',
    ];
}
