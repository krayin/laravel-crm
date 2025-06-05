<?php

namespace Webkul\RestApi\Docs\Models\Settings;

/**
 * @OA\Schema(
 *     title="Pipeline",
 *     description="Pipeline Model",
 * )
 */
class Pipeline
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
     *     title="name",
     *     description="Name",
     *     example="Lost",
     *     type="string"
     * )
     *
     * @var string
     */
    private $name;

    /**
     * @OA\Property(
     *     title="name",
     *     description="Rotten Days",
     *     example="30",
     *     type="string"
     * )
     *
     * @var string
     */
    private $rotten_days;

    /**
     * @OA\Property(
     *     title="name",
     *     description="Is Default",
     *     example="30",
     *     type="string"
     * )
     *
     * @var string
     */
    private $is_default;

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
