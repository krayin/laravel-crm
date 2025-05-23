<?php

namespace Webkul\RestApi\Docs\Models\Settings;

/**
 * @OA\Schema(
 *     title="WarehouseLocation",
 *     description="WarehouseLocation model",
 * )
 */
class WarehouseLocation
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
     *     description="Admin user's full name",
     *     example="John Doe",
     * )
     *
     * @var string
     */
    private $name;

    /**
     * @OA\Property(
     *     title="Warehouse ID",
     *     description="Warehouse ID",
     *     format="int64",
     *     example=1
     * )
     *
     * @var int
     */
    private $warehouse_id;

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
