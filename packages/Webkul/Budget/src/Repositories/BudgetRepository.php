<?php

namespace Webkul\Budget\Repositories;

use Webkul\Core\Eloquent\Repository;

class BudgetRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'Webkul\Budget\Contracts\Budget';
    }
}