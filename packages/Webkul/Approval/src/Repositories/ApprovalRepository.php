<?php

namespace Webkul\Approval\Repositories;

use Webkul\Core\Eloquent\Repository;

class ApprovalRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'Webkul\Approval\Contracts\Approval';
    }

    public function create(array $data)
    {
        $data['created_by'] = auth()->user()->id;

        return parent::create($data);
    }

    public function update(array $data, $id)
    {
        $data['updated_by'] = auth()->user()->id;

        return parent::update($data, $id);
    }
}
