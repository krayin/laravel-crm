<?php

namespace Webkul\Tag\Repositories;

use Webkul\Core\Eloquent\Repository;

class TagRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    public function model()
    {
        return 'Webkul\Tag\Contracts\Tag';
    }
}
