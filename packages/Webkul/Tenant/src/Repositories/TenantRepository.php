<?php

namespace Webkul\Tenant\Repositories;
use Webkul\Core\Eloquent\Repository;
use Webkul\Tenant\Contracts\Tenant;

class TenantRepository extends Repository
{
    protected $model;

    public function __construct(Tenant $model)
    {
        $this->model = $model;
    }
    
     /**
     * Searchable fields
     */
    protected $fieldSearchable = [
        'id',
        'created_at',
        'updated_at',
        'data',
    ];

    /**
     * Specify Model class name
     *
     * @return mixed
     */
    public function model()
    {
        return 'Webkul\Tenant\Contracts\Tenant';
    }
}