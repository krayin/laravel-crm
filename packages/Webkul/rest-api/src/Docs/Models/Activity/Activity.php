<?php

namespace Webkul\RestApi\Docs\Models\Activity;

/**
 * @OA\Schema(
 *     title="Activity",
 *     description="Activity Model",
 * )
 */
class Activity
{
    /**
     * @OA\Property(
     *     title="ID",
     *     description="Organization ID",
     *     format="int64",
     *     example="1"
     * )
     *
     * @var string
     */
    private $id;

    /**
     * @OA\Property(
     *     title="Title",
     *     description="Title of the task",
     *     example="Meeting with client"
     * )
     *
     * @var string
     */
    private $title;

    /**
     * @OA\Property(
     *     title="Type",
     *     description="Type of the task",
     *     example="Meeting"
     * )
     *
     * @var string
     */
    private $type;

    /**
     * @OA\Property(
     *     title="Location",
     *     description="Location of the task",
     *     example="Office"
     * )
     *
     * @var string
     */
    private $location;

    /**
     * @OA\Property(
     *     title="Comment",
     *     description="Comment on the task",
     *     example="Discuss project details"
     * )
     *
     * @var string
     */
    private $comment;

    /**
     * @OA\Property(
     *     title="Additional",
     *     description="Additional information about the task",
     *     example="Bring all project documents"
     * )
     *
     * @var string
     */
    private $additional;

    /**
     * @OA\Property(
     *     title="Schedule From",
     *     description="Start time of the task",
     *     format="date-time",
     *     example="2024-06-01T09:00:00Z"
     * )
     *
     * @var string
     */
    private $schedule_from;

    /**
     * @OA\Property(
     *     title="Schedule To",
     *     description="End time of the task",
     *     format="date-time",
     *     example="2024-06-01T10:00:00Z"
     * )
     *
     * @var string
     */
    private $schedule_to;

    /**
     * @OA\Property(
     *     title="Is Done",
     *     description="Status of the task",
     *     example="0"
     * )
     *
     * @var bool
     */
    private $is_done;

    /**
     * @OA\Property(
     *     title="User ID",
     *     description="ID of the user assigned to the task",
     *     format="int64",
     *     example=1
     * )
     *
     * @var int
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
