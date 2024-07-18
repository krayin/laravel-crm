<?php

namespace Webkul\WebForm\Repositories;

use Webkul\Core\Eloquent\Repository;

class WebFormAttributeRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    public function model()
    {
        return 'Webkul\WebForm\Contracts\WebFormAttribute';
    }
}
