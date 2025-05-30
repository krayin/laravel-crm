<?php

declare(strict_types=1);

namespace Stancl\Tenancy\Database\Concerns;

trait ConvertsDomainsToLowercase
{
    public static function bootConvertsDomainsToLowercase()
    {
        static::saving(function ($model) {
            $model->domain = strtolower($model->domain);
        });
    }
}
