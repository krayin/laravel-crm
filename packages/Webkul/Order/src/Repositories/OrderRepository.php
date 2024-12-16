<?php

namespace Webkul\Order\Repositories;

use Webkul\Core\Eloquent\Repository;

class OrderRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'Webkul\Order\Contracts\Order';
    }

    public function create(array $data)
    {

        if (isset($data['po_attachment'])) {

            $data['po_attachment'] = request()->file('po_attachment')->store('Orders');

        }

        $data['created_by'] = auth()->user()->id;


        $order = $this->model->create($data);

        return $order;
    }

    public function update(array $data, $id)
    {
        $order = $this->find($id);

        if (isset($data['po_attachment'])) {

            $data['po_attachment'] = request()->file('po_attachment')->store('Orders');

        }

        $data['updated_by'] = auth()->user()->id;

        $order->update($data);

        return $order;
    }
}
