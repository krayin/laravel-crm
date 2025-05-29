<?php

declare(strict_types=1);

namespace Stancl\Tenancy\Database;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class TenantScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        if (! tenancy()->initialized) {
            return;
        }

        $builder->where($model->qualifyColumn(BelongsToTenant::$tenantIdColumn), tenant()->getTenantKey())->orWhereNull($model->qualifyColumn(BelongsToTenant::$tenantIdColumn));
    }

    public function extend(Builder $builder)
    {
        $builder->macro('withoutTenancy', function (Builder $builder) {
            return $builder->withoutGlobalScope($this);
        });
    }
}
