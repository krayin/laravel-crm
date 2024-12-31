<?php

namespace Webkul\Inventory\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Webkul\Inventory\Models\Inventory;

class InventoryExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $inventories = Inventory::all();
        $inventoryData = [];
        foreach ($inventories as $key => $inventory) {
            $inventoryData[] = [
                $key + 1,
                $inventory->product_name
            ];
        }
        return collect($inventoryData);
    }
    /**

     * Write code on Method

     *

     * @return response()

     */

    public function headings(): array

    {

        return ["Sr No.", "Product Name", "Product SKU", "Product Type", "Product Price", "Product Quantity", "Product Status"];

    }
}
