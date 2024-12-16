<?php

namespace Webkul\Expense\Repositories;

use Webkul\Core\Eloquent\Repository;
use Illuminate\Container\Container;
use Webkul\Attribute\Repositories\AttributeRepository;
use Webkul\Attribute\Repositories\AttributeValueRepository;
use Webkul\Attribute\Repositories\AttributeOptionRepository;

class ExpenseRepository extends Repository
{
    /**
     * Searchable fields.
     */

    protected $fieldSearchable = [
        'name',
        'amount',
        'description',
    ];

    /**
     * Create a new repository instance.
     *
     * @return void
     */
    public function __construct(
        protected AttributeRepository $attributeRepository,
        protected AttributeOptionRepository $attributeOptionRepository,
        protected AttributeValueRepository $attributeValueRepository,
        Container $container
    )
    {
        parent::__construct($container);
    }


    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'Webkul\Expense\Contracts\Expense';
    }

    public function create(array $data)
    {

        $data['created_by'] = auth()->user()->id;

        if (isset($data['attachments'])) {

            $data['attachments'] = request()->file('attachments')->store('expenses');
        }

        if (isset($data['is_reimburse'])) {
            $data['is_reimburse'] = implode(',', $data['is_reimburse']);
        }

        $expense = parent::create($data);

        $this->attributeValueRepository->save(array_merge($data, [
            'entity_id' => $expense->id,
        ]));

        return $expense;
    }

    public function update(array $data, $id, $attributes = [])
    {

        $data['updated_by'] = auth()->user()->id;

        if (isset($data['attachments'])) {

            $data['attachments'] = request()->file('attachments')->store('expenses');
        }

        if (isset($data['is_reimburse'])) {
            $data['is_reimburse'] = implode(',', $data['is_reimburse']);
        }

        $expense = parent::update($data, $id);


        $this->attributeValueRepository->save(array_merge($data, [
            'entity_id' => $expense->id,
        ]));

        return $expense;
    }


}
