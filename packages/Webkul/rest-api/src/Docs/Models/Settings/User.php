<?php

namespace Webkul\RestApi\Docs\Models\Settings;

/**
 * @OA\Schema(
 *     title="User",
 *     description="User model",
 * )
 */
class User
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
     *     title="Email",
     *     description="Admin user's email",
     *     example="admin@example.com",
     * )
     *
     * @var string
     */
    private $email;

    /**
     * @OA\Property(
     *     title="Status",
     *     description="Admin user's status",
     *     example=1,
     *     enum={0, 1}
     * )
     *
     * @var bool
     */
    private $status;

    /**
     * @OA\Property(
     *     title="Role",
     *     description="Admin user's role"
     * )
     *
     * @var \Webkul\RestApi\Docs\Models\Settings\Role
     */
    private $role;

    /**
     * @OA\Property(
     *     title="Token",
     *     description="Admin user's token",
     *     example="29|i0hx5mbtzQ7T8Ny33ciCHOCCbSAUqEXD7RZn2iII"
     * )
     *
     * @var string
     */
    private $token;

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
