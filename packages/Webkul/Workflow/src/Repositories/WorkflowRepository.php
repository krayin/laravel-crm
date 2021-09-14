<?php

namespace Webkul\Workflow\Repositories;

use Webkul\Core\Eloquent\Repository;

class WorkflowRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'Webkul\Workflow\Contracts\Workflow';
    }
}