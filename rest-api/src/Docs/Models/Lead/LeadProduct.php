<?php

namespace Webkul\RestApi\Docs\Models\Lead;

/**
 * @OA\Schema(
 *     title="Lead Product",
 *     description="Product model",
 * )
 */
class LeadProduct
{
    /**
     * @OA\Property(
     *     title="ID",
     *     description="ID",
     *     format="int64",
     *     example=1
     * )
     *
     * @var int
     */
    private $id;

    /**
     * @OA\Property(
     *     title="Product",
     *     description="Product Quantity",
     *     example=1,
     * )
     *
     * @var float
     */
    private $quantity;

    /**
     * @OA\Property(
     *     title="Price",
     *     description="Product Price",
     *     example=1099.99,
     * )
     *
     * @var float
     */
    private $price;

    /**
     * @OA\Property(
     *     title="Amount",
     *     description="Product Amount",
     *     example=1099.99,
     * )
     *
     * @var float
     */
    private $amount;

    /**
     * @OA\Property(
     *     title="Lead Id",
     *     description="Lead Id",
     *     example=1099.99,
     * )
     *
     * @var float
     */
    private $lead_id;

    /**
     * @OA\Property(
     *     title="Product Id",
     *     description="Product Id",
     *     example=1099.99,
     * )
     *
     * @var \Webkul\RestApi\Docs\Models\Product\Product
     */
    private $product;

    /**
     * @OA\Property(
     *     title="Created at",
     *     description="Created at",
     *     example="2020-01-27 17:50:45",
     *     format="datetime",
     *     type="string"
     * )
     *
     * @var \DateTime
     */
    private $created_at;

    /**
     * @OA\Property(
     *     title="Updated at",
     *     description="Updated at",
     *     example="2020-01-27 17:50:45",
     *     format="datetime",
     *     type="string"
     * )
     *
     * @var \DateTime
     */
    private $updated_at;
}
