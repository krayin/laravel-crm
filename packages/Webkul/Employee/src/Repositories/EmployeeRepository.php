<?php

namespace Webkul\Employee\Repositories;

use Webkul\Core\Eloquent\Repository;

class EmployeeRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'Webkul\Employee\Contracts\Employee';
    }

    public function create(array $data)
    {

        if(isset($data['personal_mobile'])){
            $data['personal_mobile'] = json_encode($data['personal_mobile']);
        }
        if(isset($data['official_mobile'])){
            $data['official_mobile'] = json_encode($data['official_mobile']);
        }


        $employee = $this->model->create($data);

        return $employee;
    }

    public function update(array $data, $id)
    {
        if(isset($data['personal_mobile'])){
            $data['personal_mobile'] = implode(',', $data['personal_mobile']);
        }
        if(isset($data['official_mobile'])){
            $data['official_mobile'] = implode(',', $data['official_mobile']);
        }

        $employee = $this->find($id);

        $employee->update($data);

        return $employee;
    }
}
