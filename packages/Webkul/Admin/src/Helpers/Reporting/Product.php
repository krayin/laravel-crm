<?php

namespace Webkul\Admin\Helpers\Reporting;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Webkul\Lead\Repositories\ProductRepository;

class Product extends AbstractReporting
{
    /**
     * Create a helper instance.
     *
     * @return void
     */
    public function __construct(
        protected ProductRepository $productRepository
    ) {
        parent::__construct();
    }

    /**
     * Gets top-selling products by revenue.
     *
     * @param  int  $limit
     */
    public function getTopSellingProductsByRevenue($limit = null): Collection
    {
        $tablePrefix = DB::getTablePrefix();

        $items = $this->productRepository
            ->resetModel()
            ->with('product')
            ->leftJoin('leads', 'lead_products.lead_id', '=', 'leads.id')
            ->leftJoin('products', 'lead_products.product_id', '=', 'products.id')
            ->select('*')
            ->addSelect(DB::raw('SUM('.$tablePrefix.'lead_products.amount) as revenue'))
            ->whereBetween('leads.closed_at', [$this->startDate, $this->endDate])
            ->having(DB::raw('SUM('.$tablePrefix.'lead_products.amount)'), '>', 0)
            ->groupBy('product_id')
            ->orderBy('revenue', 'DESC')
            ->limit($limit)
            ->get();

        $items = $items->map(function ($item) {
            return [
                'id'                => $item->product_id,
                'name'              => $item->name,
                'price'             => $item->product?->price,
                'formatted_price'   => core()->formatBasePrice($item->price),
                'revenue'           => $item->revenue,
                'formatted_revenue' => core()->formatBasePrice($item->revenue),
            ];
        });

        return $items;
    }

    /**
     * Gets top-selling products by quantity.
     *
     * @param  int  $limit
     */
    public function getTopSellingProductsByQuantity($limit = null): Collection
    {
        $tablePrefix = DB::getTablePrefix();

        $items = $this->productRepository
            ->resetModel()
            ->with('product')
            ->leftJoin('leads', 'lead_products.lead_id', '=', 'leads.id')
            ->leftJoin('products', 'lead_products.product_id', '=', 'products.id')
            ->select('*')
            ->addSelect(DB::raw('SUM('.$tablePrefix.'lead_products.quantity) as total_qty_ordered'))
            ->whereBetween('leads.closed_at', [$this->startDate, $this->endDate])
            ->having(DB::raw('SUM('.$tablePrefix.'lead_products.quantity)'), '>', 0)
            ->groupBy('product_id')
            ->orderBy('total_qty_ordered', 'DESC')
            ->limit($limit)
            ->get();

        $items = $items->map(function ($item) {
            return [
                'id'                => $item->product_id,
                'name'              => $item->name,
                'price'             => $item->product?->price,
                'formatted_price'   => core()->formatBasePrice($item->price),
                'total_qty_ordered' => $item->total_qty_ordered,
            ];
        });

        return $items;
    }
}
