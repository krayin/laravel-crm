<?php

namespace Webkul\Asset\Repositories;

use Webkul\Core\Eloquent\Repository;

class AssetRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'Webkul\Asset\Contracts\Asset';
    }
}
