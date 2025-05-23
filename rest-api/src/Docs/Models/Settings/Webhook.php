<?php

namespace Webkul\RestApi\Docs\Models\Settings;

/**
 * @OA\Schema(
 *     title="Webhook",
 *     description="Webhook Model",
 * )
 */
class Webhook
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
     *     description="Name",
     *     example="EmailTemplate Name"
     * )
     *
     * @var string
     */
    private $name;

    /**
     * @OA\Property(
     *     title="description",
     *     description="Description",
     *     example="Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s."
     * )
     *
     * @var string
     */
    private $description;

    /**
     * @OA\Property(
     *     title="Entity Type",
     *     description="Entity Type",
     *     example="leads"
     * )
     *
     * @var string
     */
    private $entity_type;

    /**
     * @OA\Property(
     *     title="Event",
     *     description="Event",
     *     example="activity.update.after"
     * )
     *
     * @var string
     */
    private $event;

    /**
     * @OA\Property(
     *     title="Condition Type",
     *     description="Condition Type",
     *     example="and"
     * )
     *
     * @var string
     */
    private $condition_type;

    /**
     * @OA\Property(
     *     title="Method",
     *     description="Method",
     *     example="POST"
     * )
     *
     * @var string
     */
    private $method;

    /**
     * @OA\Property(
     *     title="End Point",
     *     description="End Point",
     *     example="http://example.com/webhook"
     * )
     *
     * @var string
     */
    private $end_point;

    /**
     * @OA\Property(
     *     title="Headers",
     *     description="Headers",
     *     example="{'Content-Type': 'application/json'}"
     * )
     *
     * @var string
     */
    private $headers;

    /**
     * @OA\Property(
     *     title="Payload",
     *     description="Payload",
     *     example="{'key': 'value'}"
     * )
     *
     * @var string
     */
    private $payload;

    /**
     * @OA\Property(
     *     title="Query Params",
     *     description="Query Params",
     *     example="{'key': 'value'}"
     * )
     *
     * @var string
     */
    private $query_params;

    /**
     * @OA\Property(
     *     title="Payload Type",
     *     description="Payload Type",
     *     example="default"
     * )
     *
     * @var string
     */
    private $payload_type;

    /**
     * @OA\Property(
     *     title="Raw Payload Type",
     *     description="Raw Payload Type",
     *     example="default"
     * )
     *
     * @var string
     */
    private $raw_payload_type;

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
