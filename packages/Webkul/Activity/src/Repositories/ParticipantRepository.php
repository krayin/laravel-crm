<?php

namespace Webkul\Activity\Repositories;

use Webkul\Core\Eloquent\Repository;

class ParticipantRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'Webkul\Activity\Contracts\Participant';
    }
}