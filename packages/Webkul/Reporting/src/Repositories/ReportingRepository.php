<?php

namespace Webkul\Reporting\Repositories;

use Webkul\Core\Eloquent\Repository;

class ReportingRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'Webkul\Reporting\Contracts\Reporting';
    }
}