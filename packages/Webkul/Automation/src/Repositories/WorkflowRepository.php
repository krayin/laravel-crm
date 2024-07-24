<?php

namespace Webkul\Automation\Repositories;

use Webkul\Core\Eloquent\Repository;
use Webkul\Automation\Contracts\Workflow;

class WorkflowRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    public function model()
    {
        return Workflow::class;
    }
}
