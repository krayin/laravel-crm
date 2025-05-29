<?php

namespace Webkul\RestApi\Docs\Models\Product;

/**
 * @OA\Schema(
 *     title="Product",
 *     description="Product model",
 * )
 */
class Product
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
     *     title="Name",
     *     description="Product name",
     *     example="Apple iPhone 12 Pro Max",
     * )
     *
     * @var string
     */
    private $name;

    /**
     * @OA\Property(
     *     title="Description",
     *     description="Product description",
     *     example="Apple iPhone 12 Pro Max 256GB",
     * )
     *
     * @var string
     */
    private $description;

    /**
     * @OA\Property(
     *     title="SKU",
     *     description="Product SKU",
     *     example="iphone-12-pro-max-256gb",
     * )
     *
     * @var string
     */
    private $sku;

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
