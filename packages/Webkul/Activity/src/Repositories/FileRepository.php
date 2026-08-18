<?php

namespace Webkul\Activity\Repositories;

use Webkul\Activity\Contracts\File;
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
        return File::class;
    }
}
