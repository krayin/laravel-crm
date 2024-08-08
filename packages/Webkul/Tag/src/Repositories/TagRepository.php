<?php

namespace Webkul\Tag\Repositories;

use Webkul\Core\Eloquent\Repository;

class TagRepository extends Repository
{
    /**
     * Searchable fields
     */
    protected $fieldSearchable = [
        'name',
        'color',
        'user_id',
    ];

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
