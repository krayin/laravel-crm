<?php

namespace Webkul\RestApi\Docs\Controllers\Settings\Warehouses;

class WarehouseController
{
    /**
     * @OA\Get(
     *     path="/api/v1/settings/warehouses",
     *     operationId="warehouses",
     *     tags={"Warehouse"},
     *     summary="Get list of warehouses",
     *     security={ {"sanctum_admin": {} }},
     *
     *     @OA\Parameter(
     *          name="sort",
     *          description="Sort column",
     *          example="id",
     *          required=false,
     *          in="query",
     *
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *
     *      @OA\Parameter(
     *          name="order",
     *          description="Sort order",
     *          required=false,
     *          in="query",
     *
     *          @OA\Schema(
     *              type="string",
     *              enum={"desc", "asc"}
     *          )
     *      ),
     *
     *      @OA\Parameter(
     *          name="page",
     *          description="Page number",
     *          required=false,
     *          in="query",
     *
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *
     *      @OA\Parameter(
     *          name="limit",
     *          description="Limit",
     *          in="query",
     *
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *
     *                 @OA\Items(ref="#/components/schemas/Warehouse")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function index() {}

    /**
     * @OA\Get(
     *      path="/api/v1/settings/warehouses/search",
     *      operationId="warehouseSearch",
     *      tags={"Warehouse"},
     *      summary="search the warehouse",
     *      description="search the warehouse",
     *      security={ {"sanctum_admin": {} }},
     *
     *      @OA\Parameter(
     *          name="search",
     *          in="query",
     *          required=false,
     *
     *          @OA\Schema(
     *              type="string",
     *              example="name:Warehouse One - California;contact_name:Oliver Queen"
     *          )
     *      ),
     *
     *      @OA\Parameter(
     *          name="searchFields",
     *          in="query",
     *          required=false,
     *
     *          @OA\Schema(
     *              type="string",
     *              example="name:like;contact_name:like"
     *          )
     *      ),
     *
     *      @OA\Parameter(
     *          name="limit",
     *          in="query",
     *          required=false,
     *
     *          @OA\Schema(
     *              type="string",
     *              example=10
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(
     *                  property="data",
     *                  type="object",
     *                  ref="#/components/schemas/Warehouse"
     *              )
     *          )
     *      )
     * )
     */
    public function search() {}

    /**
     * @OA\Get(
     *      path="/api/v1/settings/warehouses/view/{id}",
     *      operationId="viewWarehouse",
     *      tags={"Warehouse"},
     *      summary="View the warehouse",
     *      description="View the warehouse",
     *      security={ {"sanctum_admin": {} }},
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *
     *          @OA\Schema(
     *              type="string",
     *              example="1"
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(
     *                  property="data",
     *                  type="object",
     *                  ref="#/components/schemas/Warehouse"
     *              )
     *          )
     *      )
     * )
     */
    public function view() {}

    /**
     * @OA\Post(
     *     path="/api/v1/settings/warehouses",
     *     operationId="createWarehouse",
     *     tags={"Warehouse"},
     *     summary="Create new warehouse",
     *     security={ {"sanctum_admin": {} }},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *
     *            @OA\Property(
     *                   property="name",
     *                   type="string",
     *                   description="Name of the person",
     *                   example="Warehouse One - California"
     *            ),
     *           @OA\Property(
     *                   property="entity_type",
     *                   type="string",
     *                   description="Make sure while creating the warehouse, entity_type should be warehouses",
     *                   example="warehouses"
     *            ),
     *            @OA\Property(
     *                   property="contact_name",
     *                   type="string",
     *                   description="Name of the contact person",
     *                   example="Jhon Doe"
     *            ),
     *            @OA\Property(
     *                   property="description",
     *                   type="string",
     *                   description="Description of the warehouse",
     *                   example=""
     *            ),
     *            @OA\Property(
     *                 property="contact_emails",
     *                 type="array",
     *                 description="Email addresses of the person",
     *
     *                 @OA\Items(
     *                     required={"value", "label"},
     *
     *                     @OA\Property(property="value", type="string", description="Email address", example="jhon.doe@mail.com"),
     *                     @OA\Property(property="label", type="string", description="Label for the email address", example="work")
     *                 )
     *            ),
     *            @OA\Property(
     *                property="contact_numbers",
     *                type="array",
     *                description="Contact numbers of the person",
     *
     *                @OA\Items(
     *                    required={"value", "label"},
     *
     *                    @OA\Property(property="value", type="string", description="Contact number", example="12345678902"),
     *                    @OA\Property(property="label", type="string", description="Label for the contact number", example="work")
     *                )
     *            ),
     *            @OA\Property(
     *                  property="contact_address",
     *                  type="object",
     *                  @OA\Property(property="city", type="string", example="Los Angeles"),
     *                  @OA\Property(property="state", type="string", example="CA"),
     *                  @OA\Property(property="address", type="string", example="123 Main St"),
     *                  @OA\Property(property="country", type="string", example="US"),
     *                  @OA\Property(property="postcode", type="string", example="201309")
     *             ),
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(
     *                 property="data",
     *                 type="Object",
     *                 ref="#/components/schemas/Warehouse"
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function store() {}

    /**
     * @OA\Put(
     *     path="/api/v1/settings/warehouses/{id}",
     *     operationId="updateWarehouse",
     *     tags={"Warehouse"},
     *     summary="Update warehouse",
     *     security={ {"sanctum_admin": {} }},
     *
     *     @OA\Parameter(
     *          name="id",
     *          description="Warehouse Id",
     *          required=true,
     *          in="path",
     *
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *
     *            @OA\Property(
     *                   property="name",
     *                   type="string",
     *                   description="Name of the person",
     *                   example="Warehouse One - California"
     *            ),
     *           @OA\Property(
     *                   property="entity_type",
     *                   type="string",
     *                   description="Make sure while creating the warehouse, entity_type should be warehouses",
     *                   example="warehouses"
     *            ),
     *            @OA\Property(
     *                   property="contact_name",
     *                   type="string",
     *                   description="Name of the contact person",
     *                   example="Jhon Doe"
     *            ),
     *            @OA\Property(
     *                   property="description",
     *                   type="string",
     *                   description="Description of the warehouse",
     *                   example=""
     *            ),
     *            @OA\Property(
     *                 property="contact_emails",
     *                 type="array",
     *                 description="Email addresses of the person",
     *
     *                 @OA\Items(
     *                     required={"value", "label"},
     *
     *                     @OA\Property(property="value", type="string", description="Email address", example="jhon.doe@mail.com"),
     *                     @OA\Property(property="label", type="string", description="Label for the email address", example="work")
     *                 )
     *            ),
     *            @OA\Property(
     *                property="contact_numbers",
     *                type="array",
     *                description="Contact numbers of the person",
     *
     *                @OA\Items(
     *                    required={"value", "label"},
     *
     *                    @OA\Property(property="value", type="string", description="Contact number", example="12345678902"),
     *                    @OA\Property(property="label", type="string", description="Label for the contact number", example="work")
     *                )
     *            ),
     *            @OA\Property(
     *                  property="contact_address",
     *                  type="object",
     *                  @OA\Property(property="city", type="string", example="Los Angeles"),
     *                  @OA\Property(property="state", type="string", example="CA"),
     *                  @OA\Property(property="address", type="string", example="123 Main St"),
     *                  @OA\Property(property="country", type="string", example="US"),
     *                  @OA\Property(property="postcode", type="string", example="201309")
     *             ),
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(
     *                 property="data",
     *                 type="Object",
     *                 ref="#/components/schemas/Warehouse"
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function update() {}

    /**
     * @OA\Delete(
     *     path="/api/v1/settings/warehouses/{id}",
     *     operationId="deleteWarehouse",
     *     tags={"Warehouse"},
     *     summary="Delete warehouse",
     *     description="Delete a specific warehouse by its ID",
     *     security={ {"sanctum_admin": {} }},
     *
     *     @OA\Parameter(
     *          name="id",
     *          description="Warehouse Id",
     *          required=true,
     *          in="path",
     *
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Warehouse deleted successfully."
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Warehouse not found",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="Warehouse not found."
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function destroy() {}
}
