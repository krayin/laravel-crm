<?php

namespace Webkul\Core\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;

class TranslatableModel extends Model
{
    use Translatable;

    /**
     * @return string
     */
    protected function locale()
    {
        if ($this->defaultLocale) {
            return $this->defaultLocale;
        }

        return config('translatable.locale') ?: app()->make('translator')->getLocale();
    }
}