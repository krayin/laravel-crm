<?php

namespace Webkul\Activity\Repositories;

use Webkul\Core\Eloquent\Repository;

class FileRepository extends Repository
{
    /**
     * Specify model class name.
     *
     * @return mixed
     */
    public function model()
    {
        return \Webkul\Activity\Contracts\File::class;
    }
}
