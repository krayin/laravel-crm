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

     public static function formatDomainName(string $name): string
    {
        $name = mb_strtolower($name, 'UTF-8');
        
        $map = [
            'á|à|ã|â|ä' => 'a',
            'é|è|ê|ë' => 'e',
            'í|ì|î|ï' => 'i',
            'ó|ò|õ|ô|ö' => 'o',
            'ú|ù|û|ü' => 'u',
            'ç' => 'c',
            'ñ' => 'n'
        ];
        
        foreach ($map as $pattern => $replacement) {
            $name = preg_replace("/$pattern/u", $replacement, $name);
        }
        
        return preg_replace('/[^a-z0-9]/', '', $name);
    }
}
