<?php

namespace Webkul\Warehouse\Repositories;

use Illuminate\Container\Container;
use Webkul\Core\Eloquent\Repository;
use Webkul\Attribute\Repositories\AttributeValueRepository;

class LocationRepository extends Repository
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
        return 'Webkul\Warehouse\Contracts\Location';
    }

    /**
     * @param array $data
     * @return \Webkul\Warehouse\Contracts\Location
     */
    public function create(array $data)
    {
        $location = parent::create($data);

        $this->attributeValueRepository->save($data, $location->id);

        return $location;
    }

    /**
     * @param array  $data
     * @param int    $id
     * @param string $attribute
     * @return \Webkul\Warehouse\Contracts\Location
     */
    public function update(array $data, $id, $attribute = "id")
    {
        $data['name'] = '';
        
        $location = parent::update($data, $id);

        $this->attributeValueRepository->save($data, $id);

        return $location;
    }

    public function searchAndGroupLocationByWarehouse($term)
    {
        $query = $this->leftJoin('warehouses', 'warehouse_locations.warehouse_id', '=', 'warehouses.id')
            ->where('warehouse_locations.name', 'like', $term . '%')
            ->select('warehouse_locations.*', 'warehouses.name as warehouse_name')
            ->get();

        $locations = [];

        foreach ($query as $location) {
            if (! isset($locations[$location->warehouse_name])) {
                $locations[$location->warehouse_name] = [];
            }

            $locations[$location->warehouse_name][] = $location;
        }

        return $locations;
    }
}