<?php

namespace Webkul\Contact\Repositories;

use Webkul\Core\Eloquent\Repository;

class PersonRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'Webkul\Contact\Contracts\Person';
    }
}