<?php

namespace Webkul\RestApi\Docs\Controllers\Contact\Organizations;

class OrganizationController
{
    /**
     * @OA\Get(
     *     path="/api/v1/contacts/organizations",
     *     operationId="organizationList",
     *     tags={"Contacts"},
     *     summary="Get list of organizations",
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
     *                 @OA\Items(ref="#/components/schemas/Organization")
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
     *     path="/api/v1/contacts/organizations/{id}",
     *     operationId="organizationShow",
     *     tags={"Contacts"},
     *     summary="Get organization by id",
     *     security={ {"sanctum_admin": {} }},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Organization ID",
     *         required=true,
     *
     *         @OA\Schema(
     *             type="integer"
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
     *                 ref="#/components/schemas/Organization"
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
    public function show() {}

    /**
     * @OA\Post(
     *     path="/api/v1/contacts/organizations",
     *     summary="Store a new organization",
     *     description="Create a new organization with the provided details",
     *     operationId="storeOrganization",
     *     security={ {"sanctum_admin": {} }},
     *     tags={"Contacts"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *         description="Organization details",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(
     *                 property="name",
     *                 type="string",
     *                 description="Organization name",
     *                 example="Amazon"
     *             ),
     *             @OA\Property(
     *                  property="address",
     *                  type="object",
     *                  @OA\Property(property="city", type="string", example="Los Angeles"),
     *                  @OA\Property(property="state", type="string", example="CA"),
     *                  @OA\Property(property="address", type="string", example="123 Main St"),
     *                  @OA\Property(property="country", type="string", example="US"),
     *                  @OA\Property(property="postcode", type="string", example="201309")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Organization created successfully",
     *
     *         @OA\JsonContent(
     *             ref="#/components/schemas/Organization"
     *         )
     *     ),
     * )
     */
    public function store() {}

    /**
     * @OA\Put(
     *     path="/api/v1/contacts/organizations/{id}",
     *     summary="Update an organization",
     *     description="Update an organization with the provided details",
     *     operationId="updateOrganization",
     *     security={ {"sanctum_admin": {} }},
     *     tags={"Contacts"},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Organization ID",
     *         required=true,
     *
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *         description="Organization details",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(
     *                 property="name",
     *                 type="string",
     *                 description="Organization name",
     *                 example="Amazon"
     *             ),
     *             @OA\Property(
     *                  property="address",
     *                  type="object",
     *                  @OA\Property(property="city", type="string", example="Los Angeles"),
     *                  @OA\Property(property="state", type="string", example="CA"),
     *                  @OA\Property(property="address", type="string", example="123 Main St"),
     *                  @OA\Property(property="country", type="string", example="US"),
     *                  @OA\Property(property="postcode", type="string", example="201309")
     *             )
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
     *                 ref="#/components/schemas/Organization"
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
     *     path="/api/v1/contacts/organizations/{id}",
     *     summary="Delete an organization",
     *     description="Delete an organization by id",
     *     operationId="deleteOrganization",
     *     security={ {"sanctum_admin": {} }},
     *     tags={"Contacts"},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Organization ID",
     *         required=true,
     *
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=204,
     *         description="Organization deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function destroy() {}

    /**
     * @OA\Post(
     *     path="/api/v1/contacts/organizations/mass-destroy",
     *     summary="Delete multiple organizations",
     *     description="Delete multiple organizations by id",
     *     operationId="massDeleteOrganization",
     *     security={ {"sanctum_admin": {} }},
     *     tags={"Contacts"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *         description="Organization IDs",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(
     *                 property="indices",
     *                 type="array",
     *                 description="Organization IDs",
     *
     *                 @OA\Items(
     *                     type="integer",
     *                     example=1
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=204,
     *         description="Organizations deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function massDestroy() {}
}
