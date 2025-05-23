<?php

namespace Webkul\RestApi\Docs\Models\CoreConfig;

/**
 * @OA\Schema(
 *     title="CoreConfig",
 *     description="CoreConfig Model"
 * )
 */
class CoreConfig
{
    /**
     * @OA\Property(
     *     title="ID",
     *     description="ID of the lead",
     *     format="int64",
     *     example=1
     * )
     *
     * @var string
     */
    private $id;

    /**
     * @OA\Property(
     *     title="Code",
     *     description="Code",
     *     example="general"
     * )
     *
     * @var string
     */
    private $code;

    /**
     * @OA\Property(
     *     title="Value",
     *     description="Value",
     *     example="1"
     * )
     *
     * @var string
     */
    private $value;

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
