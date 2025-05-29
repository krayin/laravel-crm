<?php

namespace Webkul\RestApi\Docs\Models\Settings;

/**
 * @OA\Schema(
 *     title="Warehouse",
 *     description="Warehouse model",
 * )
 */
class Warehouse
{
    /**
     * @OA\Property(
     *     title="ID",
     *     description="Warehouse ID",
     *     format="int64",
     *     example="1"
     * )
     *
     * @var string
     */
    private $id;

    /**
     * @OA\Property(
     *     property="name",
     *     type="string",
     *     description="Warehouse Name",
     *     example="Warehouse Name"
     * ),
     *
     * @var string
     */
    private $name;

    /**
     * @OA\Property(
     *     property="contact_name",
     *     type="string",
     *     description="Contact Name",
     *     example="Jane Doe"
     * ),
     *
     * @var string
     */
    private $contact_name;

    /**
     * @OA\Property(
     *     property="description",
     *     type="string",
     *     description="Description",
     *     example="Description"
     * ),
     *
     * @var string
     */
    private $description;

    /**
     * @OA\Property(
     *    property="emails",
     *    type="array",
     *    description="Contact Emails",
     *
     *    @OA\Items(
     *        type="object",
     *
     *        @OA\Property(
     *            property="label",
     *            type="string",
     *            description="Label for the contact emails (e.g., 'work', 'home')",
     *            example="work"
     *        ),
     *        @OA\Property(
     *            property="value",
     *            type="string",
     *            description="The contact email",
     *            example="example2@gmail.com"
     *        )
     *    ),
     *    example={
     *        {"label": "work", "value": "example1@gmail.com"},
     *        {"label": "home", "value": "example2@gmail.com"}
     *    }
     * )
     */
    private $emails;

    /**
     * @OA\Property(
     *    property="contact_numbers",
     *    type="array",
     *    description="Contact Numbers",
     *
     *    @OA\Items(
     *        type="object",
     *
     *        @OA\Property(
     *            property="label",
     *            type="string",
     *            description="Label for the contact number (e.g., 'work', 'home')",
     *            example="work"
     *        ),
     *        @OA\Property(
     *            property="value",
     *            type="string",
     *            description="The contact number",
     *            example="9999999999"
     *        )
     *    ),
     *    example={
     *        {"label": "work", "value": "9999999999"},
     *        {"label": "home", "value": "8888888888"}
     *    }
     * )
     */
    private $contact_numbers;

    /**
     * @OA\Property(
     *     property="address",
     *     type="array",
     *     description="Warehouse Address",
     *
     *     @OA\Items(
     *
     *        @OA\Property(
     *            property="city",
     *            type="string",
     *            description="City name",
     *            example="Los Angeles"
     *        ),
     *        @OA\Property(
     *            property="state",
     *            type="string",
     *            description="State name",
     *            example="CA"
     *        ),
     *        @OA\Property(
     *            property="address",
     *            type="string",
     *            description="Street address",
     *            example="123 Main St"
     *        ),
     *        @OA\Property(
     *            property="country",
     *            type="string",
     *            description="Country code",
     *            example="US"
     *        ),
     *        @OA\Property(
     *            property="postcode",
     *            type="string",
     *            description="Postal code",
     *            example="90001"
     *       ),
     *     ),
     * )
     */
    private $address;

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
