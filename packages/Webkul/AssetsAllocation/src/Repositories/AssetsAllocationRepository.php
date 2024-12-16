<?php

namespace Webkul\AssetsAllocation\Repositories;

use Webkul\Core\Eloquent\Repository;

class AssetsAllocationRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'Webkul\AssetsAllocation\Contracts\AssetsAllocation';
    }

    /**
     * @param  array  $data
     * @return \Webkul\AssetsAllocation\Contracts\AssetsAllocation
     */
    public function create(array $data)
    {
        $data['created_by'] = auth()->user()->id;

        return parent::create($data);
    }

    /**
     * @param  array  $data
     * @param  int  $id
     * @return \Webkul\AssetsAllocation\Contracts\AssetsAllocation
     */

    public function update(array $data, $id)
    {
        $data['updated_by'] = auth()->user()->id;

        return parent::update($data, $id);
    }

    /**
     * @param  int  $id
     * @return bool
     */
    
}
