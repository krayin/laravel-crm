<?php

namespace Webkul\RestApi\Docs\Models\Settings;

/**
 * @OA\Schema(
 *     title="Attribute",
 *     description="Attribute Model",
 * )
 */
class Attribute
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
     *     title="Code",
     *     description="Code",
     *     example="tax"
     * )
     *
     * @var string
     */
    private $code;

    /**
     * @OA\Property(
     *     title="Name",
     *     description="Name",
     *     example="Tax"
     * )
     *
     * @var string
     */
    private $name;

    /**
     * @OA\Property(
     *     title="Type",
     *     description="Type",
     *     example="select",
     *     enum={"text", "textarea", "price", "boolean", "select", "multiselect", "checkbox", "email", "address", "phone", "lookup", "datetime", "date", "image", "file"}
     * )
     *
     * @var string
     */
    private $type;

    /**
     * @OA\Property(
     *     title="Lookup Type",
     *     description="Lookup Type",
     *     example="lead_types",
     *     enum={"leads", "lead_sources", "lead_types", "lead_pipelines", "lead_pipeline_stages", "users", "organizations", "persons",}
     * )
     *
     * @var string
     */
    private $lookup_type;

    /**
     * @OA\Property(
     *     title="Entity Type",
     *     description="Entity Type",
     *     example="persons",
     *     enum={"leads", "persons", "organizations", "products", "quotes"}
     * )
     *
     * @var string
     */
    private $entity_type;

    /**
     * @OA\Property(
     *     title="Sort Order",
     *     description="Sort Order",
     *     example="1",
     * )
     *
     * @var string
     */
    private $sort_order;

    /**
     * @OA\Property(
     *     title="Entity Type",
     *     description="Entity Type",
     *     example="",
     *     enum={"", "numeric", "email", "decimal", "url"}
     * )
     *
     * @var string
     */
    private $validation;

    /**
     * @OA\Property(
     *     title="Is Required",
     *     description="Is Required",
     *     example="0",
     * )
     *
     * @var string
     */
    private $is_required;

    /**
     * @OA\Property(
     *     title="Is Unique",
     *     description="Is Unique",
     *     example="0",
     * )
     *
     * @var bool
     */
    private $is_unique;

    /**
     * @OA\Property(
     *     title="Quick Add",
     *     description="Quick Add",
     *     example="0",
     * )
     *
     * @var bool
     */
    private $quick_add;

    /**
     * @OA\Property(
     *     title="Is User Defined",
     *     description="Is User Defined",
     *     example="1",
     * )
     *
     * @var bool
     */
    private $is_user_defined;

    /**
     * @OA\Property(
     *     title="Options Type",
     *     description="Options Type",
     *     example="options",
     *     enum={"lookup", "options"}
     * )
     *
     * @var string
     */
    private $option_type;

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
