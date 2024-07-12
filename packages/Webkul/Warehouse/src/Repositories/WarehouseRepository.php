<?php

namespace Webkul\Warehouse\Repositories;

use Illuminate\Container\Container;
use Webkul\Core\Eloquent\Repository;
use Webkul\Attribute\Repositories\AttributeValueRepository;

class WarehouseRepository extends Repository
{
    /**
     * Create a new repository instance.
     *
     * @return void
     */
    public function __construct(
        protected AttributeValueRepository $attributeValueRepository,
        Container $container
    ) {
        parent::__construct($container);
    }

    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'Webkul\Warehouse\Contracts\Warehouse';
    }

    /**
     * @param array $data
     * @return \Webkul\Warehouse\Contracts\Warehouse
     */
    public function create(array $data)
    {
        $warehouse = parent::create($data);

        $this->attributeValueRepository->save($data, $warehouse->id);

        return $warehouse;
    }

    /**
     * @param array  $data
     * @param int    $id
     * @param string $attribute
     * @return \Webkul\Warehouse\Contracts\Warehouse
     */
    public function update(array $data, $id, $attribute = "id")
    {
        $warehouse = parent::update($data, $id);

        $this->attributeValueRepository->save($data, $id);

        return $warehouse;
    }
}