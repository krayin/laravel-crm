<?php

namespace Webkul\PriceFinder\Repositories;

use Webkul\Core\Eloquent\Repository;

class PriceFinderRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'Webkul\PriceFinder\Contracts\PriceFinder';
    }
}