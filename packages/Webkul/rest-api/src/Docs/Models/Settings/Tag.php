<?php

namespace Webkul\RestApi\Docs\Models\Settings;

/**
 * @OA\Schema(
 *     title="Tag",
 *     description="Tag Model",
 * )
 */
class Tag
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
     *     description="Name of the tag",
     *     example="Active",
     *     type="string"
     * )
     *
     * @var string
     */
    private $name;

    /**
     * @OA\Property(
     *     title="color",
     *     description="Color of the tag",
     *     example="#FEBF00",
     *     type="string"
     * )
     *
     * @var string
     */
    private $color;

    /**
     * @OA\Property(
     *     title="user_id",
     *     description="User ID",
     * )
     *
     * @var \Webkul\RestApi\Docs\Models\Settings\User
     */
    private $user_id;

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
