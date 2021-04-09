<?php

namespace Webkul\Contact\Repositories;

use Webkul\Core\Eloquent\Repository;

class OrganizationRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'Webkul\Contact\Contracts\Organization';
    }
}