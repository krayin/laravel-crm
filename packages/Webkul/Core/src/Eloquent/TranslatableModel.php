<?php

namespace Webkul\Core\Eloquent;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

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
