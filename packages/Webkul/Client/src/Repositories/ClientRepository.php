<?php

namespace Webkul\Client\Repositories;

use Webkul\Core\Eloquent\Repository;

class ClientRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'Webkul\Client\Contracts\Client';
    }


    public function create(array $data)
    {
        $data['created_by'] = auth()->user()->id;

        if (isset($data['logo'])) {

            $data['logo'] = request()->file('logo')->store('clients');
        }



        return parent::create($data);
    }

    public function update(array $data, $id)
    {
        $data['updated_by'] = auth()->user()->id;

        if (isset($data['logo'])) {

            $data['logo'] = request()->file('logo')->store('clients');
        }

        return parent::update($data, $id);
    }
}
