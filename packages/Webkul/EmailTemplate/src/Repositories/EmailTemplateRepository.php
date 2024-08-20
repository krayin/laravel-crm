<?php

namespace Webkul\EmailTemplate\Repositories;

use Webkul\Core\Eloquent\Repository;

class EmailTemplateRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    public function model()
    {
        return 'Webkul\EmailTemplate\Contracts\EmailTemplate';
    }
}
