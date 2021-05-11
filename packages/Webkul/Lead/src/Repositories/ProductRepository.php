<?php

namespace Webkul\Lead\Repositories;

use Webkul\Core\Eloquent\Repository;

class ProductRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'Webkul\Lead\Contracts\Product';
    }
}