<?php

namespace Webkul\Activity\Repositories;

use Webkul\Core\Eloquent\Repository;

class ActivityRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'Webkul\Activity\Contracts\Activity';
    }
}