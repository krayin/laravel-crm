<?php

namespace Webkul\Attribute\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Webkul\Attribute\Contracts\Attribute as AttributeContract;
use Illuminate\Support\Facades\Request;
class Attribute extends Model implements AttributeContract
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code',
        'name',
        'type',
        'entity_type',
        'lookup_type',
        'is_required',
        'is_unique',
        'quick_add',
        'validation',
        'is_user_defined',
    ];

    /**
     * Get the options.
     */
    public function options()
    {
        return $this->hasMany(AttributeOptionProxy::modelClass());
    }

    protected static function booted()
    {
        static::addGlobalScope('orderBySortOrder', function ($query) {
            $query->orderBy('sort_order');
        });

        // ✅ Exclude attributes by name based on role permission
        static::addGlobalScope('visibleFieldsByRole', function ($query) {


            $user = Auth::user();

            if (
                !$user ||
                app()->runningInConsole() ||
                str_contains(Request::path(), 'settings')
            ) {
                return;
            }

            Log::error(collect($query->getQuery()->wheres));

            // Skip if running in CLI (e.g., seeder, artisan)
            if (!$user || app()->runningInConsole()) {
                return;
            }

            $hasPersonEntityFilter = collect($query->getQuery()->wheres)->contains(function ($where) {
                return isset($where['column'], $where['value'])
                    && $where['column'] === 'entity_type'
                    && $where['value'] === 'persons';
            });


            if ($hasPersonEntityFilter) {
                // List of visible field names for this role
                $allowedFields = $user->role->visible_person_fields ?? [];

                // Apply condition: only include attributes where name is in allowed list
                if (!empty($allowedFields)) {
                    $query->whereNotIn('code', $allowedFields);
                }
            }

        });

    }
}
