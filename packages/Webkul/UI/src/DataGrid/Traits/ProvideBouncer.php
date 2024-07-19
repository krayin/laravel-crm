<?php

namespace Webkul\UI\DataGrid\Traits;

trait ProvideBouncer
{
    use ProvideRouteResolver;

    /**
     * Check permissions.
     *
     * @param  array     $action
     * @param  bool      $specialPermission
     * @param  \Closure  $operation
     * @return void
     */
    private function checkPermissions($action, $specialPermission, $operation, $nameKey = 'title')
    {
        $eventName = isset($action[$nameKey]) ? $this->generateEventName($action[$nameKey]) : null;

        /**
         * In future if some cases needed, then return the below closure as per the case.
         */
        return $operation($action, $eventName);
    }

    /**
     * Generate event name.
     *
     * @param  string  $titleOrLabel
     * @return string
     */
    private function generateEventName($titleOrLabel)
    {
        $eventName = explode(' ', strtolower($titleOrLabel));

        return implode('.', $eventName);
    }
}
