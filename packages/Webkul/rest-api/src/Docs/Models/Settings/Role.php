<?php

namespace Webkul\RestApi\Docs\Models\Settings;

/**
 * @OA\Schema(
 *     title="Role",
 *     description="Role model",
 * )
 */
class Role
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
     *     description="Role name",
     *     example="Administrator",
     * )
     *
     * @var string
     */
    private $name;

    /**
     * @OA\Property(
     *     title="Description",
     *     description="Role description",
     *     example="Administrator role, i.e. User may have full admin access",
     * )
     *
     * @var string
     */
    private $description;

    /**
     * @OA\Property(
     *     title="Permission Type",
     *     description="Permission type",
     *     example="custom",
     *     enum={"all", "custom"}
     * )
     *
     * @var string
     */
    private $permission_type;

    /**
     * @OA\Property(
     *     title="Permission",
     *     description="Role's permission",
     *     type="array",
     *     example={
     *          "dashboard",
     *          "catalog",
     *          "catalog.products",
     *          "catalog.products.create",
     *          "catalog.products.copy",
     *          "catalog.products.edit",
     *          "catalog.products.delete",
     *          "catalog.products.mass-update",
     *          "catalog.products.mass-delete",
     *          "catalog.categories",
     *          "catalog.attributes",
     *          "catalog.families",
     *          "settings",
     *          "settings.users",
     *          "settings.users.users",
     *          "settings.users.users.edit"
     *     },
     *
     *     @OA\Items(
     *
     *          @OA\Property(property="dashboard", type="string"),
     *          @OA\Property(property="catalog", type="string"),
     *          @OA\Property(property="catalog.products", type="string"),
     *          @OA\Property(property="catalog.categories", type="string"),
     *          @OA\Property(property="catalog.attributes", type="string"),
     *          @OA\Property(property="catalog.families", type="string"),
     *          @OA\Property(property="settings", type="string"),
     *          @OA\Property(property="settings.users", type="string"),
     *          @OA\Property(property="settings.users.users", type="string"),
     *          @OA\Property(property="settings.users.users.edit", type="string")
     *     )
     * )
     *
     * @var array
     */
    private $permission;

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
