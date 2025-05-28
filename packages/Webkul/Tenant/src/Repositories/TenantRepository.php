<?php

namespace Webkul\Tenant\Repositories;

use Webkul\Core\Eloquent\Repository;
use Webkul\Tenant\Contracts\Tenant;

class TenantRepository extends Repository
{
    /**
     * Searchable fields
     */
    protected $fieldSearchable = [
        'id',
        'data',
    ];

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Tenant::class;
    }
}
