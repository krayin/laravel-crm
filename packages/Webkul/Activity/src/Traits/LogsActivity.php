<?php

namespace Webkul\Activity\Traits;

trait LogsActivity
{
    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::updated(function ($model) {
            dd($model);
        });
    }
}