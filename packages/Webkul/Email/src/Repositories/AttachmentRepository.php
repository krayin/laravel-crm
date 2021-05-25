<?php

namespace Webkul\Email\Repositories;

use Webkul\Core\Eloquent\Repository;

class AttachmentRepository extends Repository
{

    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'Webkul\Email\Contracts\Attachment';
    }

    /**
     * @param array $data
     * @return \Webkul\Product\Contracts\Product
     */
    public function create(array $data)
    {
    }
}