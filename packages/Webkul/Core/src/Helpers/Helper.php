<?php

namespace Webkul\Core\Helpers;

class Helper
{
    /**
     * @param  string  $packageName
     * @return array
     */
    public function jsonTranslations($packageName)
    {
        $currentLocale = app()->getLocale();

        $path = __DIR__."/../../../$packageName/src/Resources/lang/$currentLocale/app.php";

        if (is_string($path) && is_readable($path)) {
            return include $path;
        } else {
            $currentLocale = 'en';

            $path = __DIR__."/../../../$packageName/src/Resources/lang/$currentLocale/app.php";

            return include $path;
        }
    }
}
