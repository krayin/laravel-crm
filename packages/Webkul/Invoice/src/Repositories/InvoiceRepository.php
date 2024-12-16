<?php

namespace Webkul\Invoice\Repositories;

use Webkul\Core\Eloquent\Repository;

class InvoiceRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'Webkul\Invoice\Contracts\Invoice';
    }

    /**
     * @param  array  $data
     * @return \Webkul\Invoice\Contracts\Invoice
     */
    public function create(array $data)
    {
        $data['created_by'] = auth()->user()->id;

        $invoice = $this->model->create($data);

        return $invoice;
    }

    /**
     * @param  array  $data
     * @param  int  $id
     * @return \Webkul\Invoice\Contracts\Invoice
     */
    public function update(array $data, $id)
    {
        $invoice = $this->find($id);

        $data['updated_by'] = auth()->user()->id;

        $invoice->update($data);

        return $invoice;
    }

    public function delete($id)
    {
        $invoice = $this->find($id);

        $invoice->update(['deleted_by' => auth()->user()->id]);

        return $invoice->delete();
    }
}
