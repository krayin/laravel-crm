<?php

namespace Webkul\User\Repositories;

use Webkul\Core\Eloquent\Repository;

class UserTenantRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    public function model()
    {
        return 'Webkul\User\Contracts\UserTenant';
    }
}