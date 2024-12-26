<?php

namespace Webkul\Announcement\Repositories;

use Webkul\Core\Eloquent\Repository;

class AnnouncementRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'Webkul\Announcement\Contracts\Announcement';
    }
}