<?php

namespace Webkul\RestApi\Docs\Models\Settings;

/**
 * @OA\Schema(
 *     title="EmailTemplate",
 *     description="EmailTemplate Model",
 * )
 */
class EmailTemplate
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
     *     title="Subject",
     *     description="Subject",
     *     example="EmailTemplate Subject"
     * )
     *
     * @var string
     */
    private $subject;

    /**
     * @OA\Property(
     *     title="Content",
     *     description="Content",
     *     example="EmailTemplate Content",
     * )
     *
     * @var string
     */
    private $content;

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
