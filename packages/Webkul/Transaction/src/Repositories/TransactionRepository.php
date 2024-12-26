<?php

namespace Webkul\Transaction\Repositories;

use Webkul\Core\Eloquent\Repository;

class TransactionRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'Webkul\Transaction\Contracts\Transaction';
    }
}