<?php

namespace Webkul\Automation\Repositories;

use Webkul\Automation\Contracts\Workflow;
use Webkul\Core\Eloquent\Repository;

class WorkflowRepository extends Repository
{
    /**
     * Specify Model class name.
     */
    public function model(): string
    {
        return Workflow::class;
    }
}
