<?php

namespace Webkul\Consignment\Repositories;

use Webkul\Core\Eloquent\Repository;

class ConsignmentRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'Webkul\Consignment\Contracts\Consignment';
    }
}