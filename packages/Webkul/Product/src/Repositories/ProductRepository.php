<?php

namespace Webkul\Product\Repositories;

use Illuminate\Container\Container;
use Illuminate\Support\Str;
use Webkul\Core\Eloquent\Repository;
use Webkul\Attribute\Repositories\AttributeValueRepository;
use Webkul\Product\Repositories\ProductInventoryRepository;

class ProductRepository extends Repository
{
    /**
     * Create a new repository instance.
     *
     * @return void
     */
    public function __construct(
        protected AttributeValueRepository $attributeValueRepository,
        protected ProductInventoryRepository $productInventoryRepository,
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
        return 'Webkul\Product\Contracts\Product';
    }

    /**
     * @param array $data
     * @return \Webkul\Product\Contracts\Product
     */
    public function create(array $data)
    {
        $product = parent::create($data);

        $this->attributeValueRepository->save($data, $product->id);

        return $product;
    }

    /**
     * @param array  $data
     * @param int    $id
     * @param string $attribute
     * @return \Webkul\Product\Contracts\Product
     */
    public function update(array $data, $id, $attribute = "id")
    {
        $product = parent::update($data, $id);

        $this->attributeValueRepository->save($data, $id);

        return $product;
    }

    /**
     * @param array  $data
     * @param int    $id
     */
    public function saveInventories(array $data, $id, $warehouseId = null)
    {
        $productInventories = $this->productInventoryRepository->where('product_id', $id);

        if ($warehouseId) {
            $productInventories = $productInventories->where('warehouse_id', $warehouseId);
        }

        $previousInventoryIds = $productInventories->pluck('id');

        if (isset($data['inventories'])) {
            foreach ($data['inventories'] as $inventoryId => $inventoryData) {
                if (Str::contains($inventoryId, 'inventory_')) {
                    $this->productInventoryRepository->create(array_merge($inventoryData, [
                        'product_id' => $id,
                        'warehouse_id' => $warehouseId,
                    ]));
                } else {
                    if (is_numeric($index = $previousInventoryIds->search($inventoryId))) {
                        $previousInventoryIds->forget($index);
                    }

                    $this->productInventoryRepository->update($inventoryData, $inventoryId);
                }
            }
        }

        foreach ($previousInventoryIds as $inventoryId) {
            $this->productInventoryRepository->delete($inventoryId);
        }
    }

    /**
     * Retrieves customers count based on date
     *
     * @return number
     */
    public function getProductCount($startDate, $endDate)
    {
        return $this
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get()
            ->count();
    }

    public function getInventoriesGroupedByWarehouse($id)
    {
        $product = $this->findOrFail($id);

        $warehouses = [];

        foreach ($product->inventories as $inventory) {
            if (! isset($warehouses[$inventory->warehouse_id])) {
                $warehouses[$inventory->warehouse_id] = [
                    'id'        => $inventory->warehouse_id,
                    'name'      => $inventory->warehouse->name,
                    'in_stock'  => $inventory->in_stock,
                    'allocated' => $inventory->allocated,
                    'on_hand'   => $inventory->on_hand,
                ];
            } else {
                $warehouses[$inventory->warehouse_id]['in_stock'] += $inventory->in_stock;
                $warehouses[$inventory->warehouse_id]['allocated'] += $inventory->allocated;
                $warehouses[$inventory->warehouse_id]['on_hand'] += $inventory->on_hand;
            }

            $warehouses[$inventory->warehouse_id]['locations'][] = [
                'id'        => $inventory->warehouse_location_id,
                'name'      => $inventory->location->name,
                'in_stock'  => $inventory->in_stock,
                'allocated' => $inventory->allocated,
                'on_hand'   => $inventory->on_hand,
            ];
        }

        return $warehouses;
    }
}