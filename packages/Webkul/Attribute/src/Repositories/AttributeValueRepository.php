<?php

namespace Webkul\Attribute\Repositories;

use Webkul\Core\Eloquent\Repository;

class AttributeValueRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'Webkul\Attribute\Contracts\AttributeValue';
    }
}