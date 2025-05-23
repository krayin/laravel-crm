<?php

namespace Webkul\RestApi\Docs\Controllers\Settings;

class AttributeController
{
    /**
     * @OA\Get(
     *      path="/api/v1/settings/attributes",
     *      operationId="attributeList",
     *      tags={"Attribute"},
     *      summary="Get list of Attribute",
     *      security={ {"sanctum_admin": {} }},
     *
     *      @OA\Parameter(
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
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(
     *
     *               @OA\Property(
     *                  property="data",
     *                  type="array",
     *
     *                  @OA\Items(ref="#/components/schemas/Attribute")
     *              )
     *          )
     *      ),
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
     *      path="/api/v1/settings/attributes/{id}",
     *      operationId="attributeShow",
     *      tags={"Attribute"},
     *      summary="Get Attribute",
     *      security={ {"sanctum_admin": {} }},
     *
     *      @OA\Parameter(
     *          name="id",
     *          description="Attribute Id",
     *          required=true,
     *          in="path",
     *
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(ref="#/components/schemas/Attribute")
     *       ),
     *
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized"
     *      )
     * )
     */
    public function show() {}

    /**
     * @OA\Get(
     *      path="/api/v1/settings/attributes/lookup/{lookup}",
     *      operationId="attributeLookup",
     *      tags={"Attribute"},
     *      summary="Search attribute lookup results",
     *      security={ {"sanctum_admin": {} }},
     *
     *      @OA\Parameter(
     *          name="lookup",
     *          description="Attribute Lookup",
     *          required=true,
     *          in="path",
     *
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *
     *      @OA\Parameter(
     *          name="query",
     *          description="Query",
     *          required=true,
     *          in="query",
     *
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(
     *
     *               @OA\Property(
     *                  property="data",
     *                  type="array",
     *
     *                  @OA\Items(ref="#/components/schemas/Attribute")
     *              )
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized"
     *      )
     * )
     */
    public function lookup() {}

    /**
     * @OA\Get(
     *      path="/api/v1/settings/attributes/lookup-entity/{lookup}",
     *      operationId="attributeEntityLookup",
     *      tags={"Attribute"},
     *      summary="Search attribute lookup results",
     *      security={ {"sanctum_admin": {} }},
     *
     *      @OA\Parameter(
     *          name="lookup",
     *          description="Attribute Lookup",
     *          required=true,
     *          in="path",
     *
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *
     *      @OA\Parameter(
     *          name="query",
     *          description="query",
     *          required=true,
     *          in="query",
     *
     *          @OA\Schema(
     *              type="string"
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
     *                  property="message",
     *                  type="file",
     *              )
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized"
     *      )
     * )
     */
    public function download() {}

    /**
     * @OA\Post(
     *      path="/api/v1/settings/attributes",
     *      operationId="attributeCreate",
     *      tags={"Attribute"},
     *      summary="Create new Attribute",
     *      security={ {"sanctum_admin": {} }},
     *
     *      @OA\RequestBody(
     *
     *          @OA\MediaType(
     *              mediaType="application/json",
     *
     *              @OA\Schema(
     *
     *                  @OA\Property(
     *                      property="code",
     *                      description="Code",
     *                      type="string",
     *                      example="tax"
     *                  ),
     *                  @OA\Property(
     *                      property="name",
     *                      description="Name of the Attribute",
     *                      type="string",
     *                      example="Tax"
     *                  ),
     *                  @OA\Property(
     *                      property="type",
     *                      description="Type of the Attribute",
     *                      type="string",
     *                      example="select"
     *                  ),
     *                  @OA\Property(
     *                      property="lookup_type",
     *                      description="Lookup Type",
     *                      type="string",
     *                      example="lead_types"
     *                  ),
     *                  @OA\Property(
     *                      property="entity_type",
     *                      description="Entity Type",
     *                      type="string",
     *                      example="persons"
     *                  ),
     *                  @OA\Property(
     *                      property="sort_order",
     *                      description="Order of the Attribute",
     *                      type="string",
     *                      example="1"
     *                  ),
     *                  @OA\Property(
     *                      property="validation",
     *                      description="Validation",
     *                      type="string",
     *                      example=""
     *                  ),
     *                  @OA\Property(
     *                      property="is_required",
     *                      description="Is Required",
     *                      type="string",
     *                      example="1"
     *                  ),
     *                  @OA\Property(
     *                      property="is_unique",
     *                      description="Is Unique",
     *                      type="string",
     *                      example="1"
     *                  ),
     *                  @OA\Property(
     *                      property="quick_add",
     *                      description="Quick Add",
     *                      type="string",
     *                      example="1"
     *                  ),
     *                  @OA\Property(
     *                      property="is_user_defined",
     *                      description="Is User defined Attribute",
     *                      type="string",
     *                      example="1"
     *                  ),
     *                  @OA\Property(
     *                      property="option_type",
     *                      description="Options Type",
     *                      type="string",
     *                      example="options"
     *                  ),
     *              )
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(
     *
     *               @OA\Property(
     *                  property="data",
     *                  type="array",
     *
     *                  @OA\Items(ref="#/components/schemas/Attribute")
     *              )
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized"
     *      )
     * )
     */
    public function store() {}

    /**
     * @OA\Put(
     *      path="/api/v1/settings/attributes/{id}",
     *      operationId="attributeUpdate",
     *      tags={"Attribute"},
     *      summary="Update an existing Attribute",
     *      security={ {"sanctum_admin": {} }},
     *
     *      @OA\Parameter(
     *          name="id",
     *          description="Attribute Id",
     *          required=true,
     *          in="path",
     *
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *
     *       @OA\RequestBody(
     *
     *          @OA\MediaType(
     *              mediaType="application/json",
     *
     *              @OA\Schema(
     *
     *                  @OA\Property(
     *                      property="code",
     *                      description="Code",
     *                      type="string",
     *                      example="tax"
     *                  ),
     *                  @OA\Property(
     *                      property="name",
     *                      description="Name of the Attribute",
     *                      type="string",
     *                      example="Tax"
     *                  ),
     *                  @OA\Property(
     *                      property="type",
     *                      description="Type of the Attribute",
     *                      type="string",
     *                      example="select"
     *                  ),
     *                  @OA\Property(
     *                      property="lookup_type",
     *                      description="Lookup Type",
     *                      type="string",
     *                      example="lead_types"
     *                  ),
     *                  @OA\Property(
     *                      property="entity_type",
     *                      description="Entity Type",
     *                      type="string",
     *                      example="persons"
     *                  ),
     *                  @OA\Property(
     *                      property="sort_order",
     *                      description="Order of the Attribute",
     *                      type="string",
     *                      example="1"
     *                  ),
     *                  @OA\Property(
     *                      property="validation",
     *                      description="Validation",
     *                      type="string",
     *                      example=""
     *                  ),
     *                  @OA\Property(
     *                      property="is_required",
     *                      description="Is Required",
     *                      type="string",
     *                      example="1"
     *                  ),
     *                  @OA\Property(
     *                      property="is_unique",
     *                      description="Is Unique",
     *                      type="string",
     *                      example="1"
     *                  ),
     *                  @OA\Property(
     *                      property="quick_add",
     *                      description="Quick Add",
     *                      type="string",
     *                      example="1"
     *                  ),
     *                  @OA\Property(
     *                      property="is_user_defined",
     *                      description="Is User defined Attribute",
     *                      type="string",
     *                      example="1"
     *                  ),
     *                  @OA\Property(
     *                      property="option_type",
     *                      description="Options Type",
     *                      type="string",
     *                      example="options"
     *                  ),
     *              )
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(
     *
     *               @OA\Property(
     *                  property="data",
     *                  type="array",
     *
     *                  @OA\Items(ref="#/components/schemas/Attribute")
     *              )
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized"
     *      )
     * )
     */
    public function update() {}

    /**
     * @OA\Delete(
     *      path="/api/v1/settings/attributes/{id}",
     *      operationId="attributeDelete",
     *      tags={"Attribute"},
     *      summary="Delete one record of Attribute",
     *      security={ {"sanctum_admin": {} }},
     *
     *      @OA\Parameter(
     *          name="id",
     *          description="Attribute Id",
     *          required=true,
     *          in="path",
     *
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(
     *
     *               @OA\Property(
     *                  property="data",
     *                  type="array",
     *
     *                  @OA\Items(ref="#/components/schemas/Attribute")
     *              )
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized"
     *      )
     * )
     */
    public function destroy() {}

    /**
     * @OA\Post(
     *      path="/api/v1/settings/attributes/mass-destroy",
     *      operationId="attributeMassDestroy",
     *      tags={"Attribute"},
     *      summary="Delete multiple records of Attribute",
     *      security={ {"sanctum_admin": {} }},
     *
     *      @OA\RequestBody(
     *          required=true,
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(
     *                  property="indices",
     *                  type="array",
     *
     *                  @OA\Items(
     *                      type="integer",
     *                      example=1
     *                  )
     *              )
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(
     *
     *               @OA\Property(
     *                  property="data",
     *                  type="array",
     *
     *                  @OA\Items(ref="#/components/schemas/Attribute")
     *              )
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized"
     *      )
     * )
     */
    public function massDestroy() {}
}
