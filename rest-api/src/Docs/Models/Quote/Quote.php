<?php

namespace Webkul\RestApi\Docs\Models\Quote;

/**
 * @OA\Schema(
 *     title="Quote",
 *     description="Quote Model",
 * )
 */
class Quote
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
     *     title="Description",
     *     description="Quote Description",
     *     example="This is a quote description.",
     * )
     *
     * @var string
     */
    private $description;

    /**
     *  @OA\Property(
     *      property="billing_address",
     *      description="Billing address details",
     *      @OA\Property(
     *          property="address",
     *          type="string",
     *          description="Street address",
     *          example="123 Main St"
     *      ),
     *      @OA\Property(
     *          property="country",
     *          type="string",
     *          description="Country code",
     *          example="US"
     *      ),
     *      @OA\Property(
     *          property="state",
     *          type="string",
     *          description="State code",
     *          example="CA"
     *      ),
     *      @OA\Property(
     *          property="city",
     *          type="string",
     *          description="City name",
     *          example="Los Angeles"
     *      ),
     *      @OA\Property(
     *          property="postcode",
     *          type="string",
     *          description="Postal code",
     *          example="90001"
     *      )
     *  ),
     *
     * @var string
     */
    private $billing_address;

    /**
     *  @OA\Property(
     *      property="shipping_address",
     *      description="Shipping address details",
     *      @OA\Property(
     *          property="address",
     *          type="string",
     *          description="Street address",
     *          example="123 Main St"
     *      ),
     *      @OA\Property(
     *          property="country",
     *          type="string",
     *          description="Country code",
     *          example="US"
     *      ),
     *      @OA\Property(
     *          property="state",
     *          type="string",
     *          description="State code",
     *          example="CA"
     *      ),
     *      @OA\Property(
     *          property="city",
     *          type="string",
     *          description="City name",
     *          example="Los Angeles"
     *      ),
     *      @OA\Property(
     *          property="postcode",
     *          type="string",
     *          description="Postal code",
     *          example="90001"
     *      )
     *  ),
     *
     * @var string
     */
    private $shipping_address;

    /**
     * @OA\Property(
     *     title="Discount Percent",
     *     description="Quote Discount Percent",
     *     example="10",
     * )
     *
     * @var string
     */
    private $discount_percent;

    /**
     * @OA\Property(
     *     title="Discount Amount",
     *     description="Quote Discount Amount",
     *     example="10",
     * )
     *
     * @var string
     */
    private $discount_amount;

    /**
     * @OA\Property(
     *     title="Tax Amount",
     *     description="Quote Tax Amount",
     *     example="10",
     * )
     *
     * @var string
     */
    private $tax_amount;

    /**
     * @OA\Property(
     *     title="Adjustment Amount",
     *     description="Quote Adjustment Amount",
     *     example="10",
     * )
     *
     * @var string
     */
    private $adjustment_amount;

    /**
     * @OA\Property(
     *     title="Sub Total",
     *     description="Quote Sub Total",
     *     example="90",
     * )
     *
     * @var string
     */
    private $sub_total;

    /**
     * @OA\Property(
     *     title="Grand Total",
     *     description="Quote Grand Total",
     *     example="90",
     * )
     *
     * @var string
     */
    private $grand_total;

    /**
     * @OA\Property(
     *     title="Expired At",
     *     description="Quote Expired At",
     *     example="2024-05-27T12:34:56Z",
     * )
     *
     * @var string
     */
    private $expired_at;

    /**
     * @OA\Property(
     *     title="User ID",
     *     description="Quote User ID",
     *     example="1",
     * )
     *
     * @var string
     */
    private $user_id;

    /**
     * @OA\Property(
     *     title="Person ID",
     *     description="Quote Person ID",
     *     example="1",
     * )
     *
     * @var string
     */
    private $person_id;

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
