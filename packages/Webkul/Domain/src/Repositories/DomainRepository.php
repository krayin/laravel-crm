<?php

namespace Webkul\Domain\Repositories;

use Webkul\Core\Eloquent\Repository;
use Webkul\Domain\Contracts\Domain;

class DomainRepository extends Repository
{
    /**
     * Searchable fields
     */
    protected $fieldSearchable = [
        'id',
        'domain',
        'tenant_id',
    ];

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Domain::class;
    }
}
