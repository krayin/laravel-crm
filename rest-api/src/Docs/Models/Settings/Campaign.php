<?php

namespace Webkul\RestApi\Docs\Models\Settings;

/**
 * @OA\Schema(
 *     title="Campaign",
 *     description="Campaign Model",
 * )
 */
class Campaign
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
     *     example="Campaign Name"
     * )
     *
     * @var string
     */
    private $name;

    /**
     * @OA\Property(
     *     title="Subject",
     *     description="Subject",
     *     example="Subject of the Campaign",
     * )
     *
     * @var string
     */
    private $subject;

    /**
     * @OA\Property(
     *     title="Status",
     *     description="Campaign status",
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
     * @var \Webkul\RestApi\Docs\Models\Settings\EmailTemplate
     */
    private $marketing_email_template;

    /**
     * @OA\Property(
     *     title="Role",
     *     description="Admin user's role"
     * )
     *
     * @var \Webkul\RestApi\Docs\Models\Settings\Event
     */
    private $marketing_event;

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
