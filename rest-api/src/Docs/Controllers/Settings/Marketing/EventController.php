<?php

namespace Webkul\RestApi\Docs\Controllers\Settings\Marketing;

class EventController
{
    /**
     * @OA\Get(
     *      path="/api/v1/settings/marketing/events",
     *      operationId="marketingList",
     *      tags={"MarketingEvent"},
     *      summary="Get list of Marketing Events",
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
     *                  @OA\Items(ref="#/components/schemas/Event")
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
     * @OA\Post(
     *      path="/api/v1/settings/marketing/events",
     *      operationId="marketingCreate",
     *      tags={"MarketingEvent"},
     *      summary="Create new Marketing Event",
     *      security={ {"sanctum_admin": {} }},
     *
     *      @OA\RequestBody(
     *
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *
     *              @OA\Schema(
     *
     *                  @OA\Property(
     *                      property="name",
     *                      description="Marketing Event Name",
     *                      type="string",
     *                      example="Marketing Name"
     *                  ),
     *                  @OA\Property(
     *                      property="description",
     *                      description="Write marketing event description here",
     *                      type="string",
     *                      example="Marketing event Description",
     *                  ),
     *                  @OA\Property(
     *                      property="date",
     *                      description="Date",
     *                      type="string",
     *                      format="date",
     *                      example="2025-05-01"
     *                  ),
     *                  required={"name", "description", "date"},
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
     *              @OA\Property(
     *                  property="data",
     *                  type="Object",
     *                  ref="#/components/schemas/Event"
     *              )
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=422,
     *          description="Validation error"
     *      )
     * )
     */
    public function store() {}

    /**
     * @OA\Get(
     *      path="/api/v1/settings/marketing/events/{id}",
     *      operationId="marketingRead",
     *      tags={"MarketingEvent"},
     *      summary="Get Marketing based on id",
     *      security={ {"sanctum_admin": {} }},
     *
     *      @OA\Parameter(
     *          name="id",
     *          description="Marketing Id",
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
     *              @OA\Property(
     *                  property="data",
     *                  type="Object",
     *                  ref="#/components/schemas/Event"
     *              )
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=404,
     *          description="Marketing not found"
     *      )
     * )
     */
    public function show() {}

    /**
     * @OA\Put(
     *      path="/api/v1/settings/marketing/events/{id}",
     *      operationId="marketingUpdate",
     *      tags={"MarketingEvent"},
     *      summary="Update existing Marketing Event",
     *      security={ {"sanctum_admin": {} }},
     *
     *      @OA\Parameter(
     *          name="id",
     *          description="Marketing Id",
     *          required=true,
     *          in="path",
     *
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *
     *      @OA\RequestBody(
     *
     *          @OA\MediaType(
     *              mediaType="application/json",
     *
     *              @OA\Schema(
     *
     *                  @OA\Property(
     *                      property="name",
     *                      description="Marketing event Name",
     *                      type="string",
     *                      example="Marketing Name"
     *                  ),
     *                  @OA\Property(
     *                      property="description",
     *                      description="Write marketing event description here",
     *                      type="string",
     *                      example="Marketing Description",
     *                  ),
     *                  @OA\Property(
     *                      property="date",
     *                      description="Date",
     *                      type="string",
     *                      format="date",
     *                      example="2025-05-01"
     *                  ),
     *                  required={"name", "description", "date"},
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
     *              @OA\Property(
     *                  property="data",
     *                  type="Object",
     *                  ref="#/components/schemas/Event"
     *              )
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=422,
     *          description="Validation error"
     *      )
     * )
     */
    public function update() {}

    /**
     * @OA\Delete(
     *      path="/api/v1/settings/marketing/events/{id}",
     *      operationId="marketingDelete",
     *      tags={"MarketingEvent"},
     *      summary="Delete existing Marketing event",
     *      security={ {"sanctum_admin": {} }},
     *
     *      @OA\Parameter(
     *          name="id",
     *          description="Marketing Id",
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
     *              @OA\Property(
     *                  property="message",
     *                  type="string",
     *                  example="Marketing event deleted successfully"
     *              )
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=404,
     *          description="Marketing event not found"
     *      )
     * )
     */
    public function destroy() {}

    /**
     * @OA\Post(
     *      path="/api/v1/settings/marketing/events/mass-destroy",
     *      operationId="marketingMassDestroy",
     *      tags={"MarketingEvent"},
     *      summary="Delete multiple records of Marketing Event",
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
     *                  @OA\Items(ref="#/components/schemas/Event")
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
