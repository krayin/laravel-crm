<?php

namespace Webkul\RestApi\Docs\Models\Lead;

/**
 * @OA\Schema(
 *     title="Lead",
 *     description="Lead Model",
 * )
 */
class Lead
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
     *     title="Title",
     *     description="Title of the lead",
     *     example="Lead From Phone",
     * )
     *
     * @var string
     */
    private $title;

    /**
     * @OA\Property(
     *     title="Description",
     *     description="Description of the Lead",
     *     example="Lead comes form via phone",
     * )
     *
     * @var string
     */
    private $description;

    /**
     * @OA\Property(
     *     title="Lead Value",
     *     description="Lead Value",
     *     example="$45,255,25",
     * )
     *
     * @var string
     */
    private $lead_value;

    /**
     * @OA\Property(
     *     title="Lead Status",
     *     description="Lead Status",
     *     example=1,
     *     enum={0, 1}
     * )
     *
     * @var bool
     */
    private $status;

    /**
     * @OA\Property(
     *     title="Lead Reason",
     *     description="Lost Reason",
     *     example="Not interesting",
     * )
     *
     * @var string
     */
    private $lost_reason;

    /**
     * @OA\Property(
     *     title="Expected Close at",
     *     description="Expected Close at",
     *     example="2020-01-27 17:50:45",
     *     format="datetime",
     *     type="string"
     * )
     *
     * @var \DateTime
     */
    private $expected_close_date;

    /**
     * @OA\Property(
     *     title="Close at",
     *     description="Close at",
     *     example="2020-01-27 17:50:45",
     *     format="datetime",
     *     type="string"
     * )
     *
     * @var \DateTime
     */
    private $close_at;

    /**
     * @OA\Property(
     *     title="Person Id",
     *     description="Person Id",
     * )
     *
     * @var \Webkul\RestApi\Docs\Models\Contact\Person
     */
    private $person;

    /**
     * @OA\Property(
     *     title="User ID",
     *     description="User ID",
     * )
     *
     * @var \Webkul\RestApi\Docs\Models\Settings\User
     */
    private $user;

    /**
     * @OA\Property(
     *     title="Lead Products",
     *     description="Lead Products",
     *     type="array",
     *
     *     @OA\Items(
     *         type="object",
     *         ref="#/components/schemas/LeadProduct"
     *     )
     * )
     *
     * @var \Webkul\RestApi\Docs\Models\Lead\LeadProduct[]
     */
    private $lead_products;

    /**
     * @OA\Property(
     *     title="Lead source ID",
     *     description="Lead source ID",
     *     example="1",
     * )
     *
     * @var int
     */
    private $lead_source_id;

    /**
     * @OA\Property(
     *     title="Lead Type ID",
     *     description="Lead Type ID",
     *     example="1",
     * )
     *
     * @var int
     */
    private $lead_type_id;

    /**
     * @OA\Property(
     *     title="Lead Pipeline ID",
     *     description="Lead Pipeline ID",
     *     example="1",
     * )
     *
     * @var int
     */
    private $lead_pipeline_id;

    /**
     * @OA\Property(
     *     title="Lead Pipeline stage ID",
     *     description="Lead Pipeline stage ID",
     *     example="1",
     * )
     *
     * @var int
     */
    private $lead_pipeline_stage_id;

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
