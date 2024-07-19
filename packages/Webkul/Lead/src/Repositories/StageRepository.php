<?php

namespace Webkul\Lead\Repositories;

use Webkul\Core\Eloquent\Repository;

class StageRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    public function model()
    {
        return 'Webkul\Lead\Contracts\Stage';
    }
}
