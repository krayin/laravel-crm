<?php

namespace Webkul\Warehouse\Repositories;

use Webkul\Core\Eloquent\Repository;

class LocationRepository extends Repository
{
    /**
     * Searchable fields
     */
    protected $fieldSearchable = [
        'name',
        'warehouse_id',
    ];

    /**
     * Specify Model class name
     *
     * @return mixed
     */
    public function model()
    {
        return 'Webkul\Warehouse\Contracts\Location';
    }
}
