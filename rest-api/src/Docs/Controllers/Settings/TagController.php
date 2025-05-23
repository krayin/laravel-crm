<?php

namespace Webkul\RestApi\Docs\Controllers\Settings;

class TagController
{
    /**
     * @OA\Get(
     *      path="/api/v1/settings/tags",
     *      operationId="tagList",
     *      tags={"Tag"},
     *      summary="Get list of tags",
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
     *                  @OA\Items(ref="#/components/schemas/Tag")
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
     *      path="/api/v1/settings/tags",
     *      operationId="storeTag",
     *      tags={"Tag"},
     *      summary="Store the Tags",
     *      description="Store the Tags",
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
     *                      description="Tag Name",
     *                      type="string",
     *                      example="Active"
     *                  ),
     *                  @OA\Property(
     *                      property="color",
     *                      description="Select the color for the tag",
     *                      type="string",
     *                      example="#FEBF00",
     *                      enum={"#FEE2E2", "#FFEDD5", "#FEF3C7", "#FEF9C3", "#ECFCCB", "#DCFCE7"}
     *                  ),
     *                  required={"name"},
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
     *                  property="message",
     *                  type="string",
     *                  example="Tags added successfully.",
     *              )
     *          )
     *      )
     * )
     */
    public function store() {}

    /**
     * @OA\Get(
     *      path="/api/v1/settings/tags/{id}",
     *      operationId="showTag",
     *      tags={"Tag"},
     *      summary="Get Tag by id",
     *      description="Get Tag by id",
     *      security={ {"sanctum_admin": {} }},
     *
     *      @OA\Parameter(
     *          name="id",
     *          description="Tag Id",
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
     *                  type="object",
     *                  ref="#/components/schemas/Tag"
     *              )
     *          )
     *      )
     * )
     */
    public function show() {}

    /**
     * @OA\Put(
     *      path="/api/v1/settings/tags/{id}",
     *      operationId="updateTag",
     *      tags={"Tag"},
     *      summary="Update the Tags",
     *      description="Update the Tags",
     *      security={ {"sanctum_admin": {} }},
     *
     *      @OA\Parameter(
     *          name="id",
     *          description="Tag Id",
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
     *                      description="Tag Name",
     *                      type="string",
     *                      example="Active"
     *                  ),
     *                  @OA\Property(
     *                      property="color",
     *                      description="Select the color for the tag",
     *                      type="string",
     *                      example="#FEE2E2",
     *                      enum={"#FEE2E2", "#FFEDD5", "#FEF3C7", "#FEF9C3", "#ECFCCB", "#DCFCE7"}
     *                  ),
     *                  required={"name"},
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
     *                  property="message",
     *                  type="string",
     *                  example="Tags updated successfully.",
     *              )
     *          )
     *      )
     * )
     */
    public function update() {}

    /**
     * @OA\Get(
     *      path="/api/v1/settings/tags/search",
     *      operationId="SearchTags",
     *      tags={"Tag"},
     *      summary="search the tags",
     *      description="search the tags",
     *      security={ {"sanctum_admin": {} }},
     *
     *      @OA\Parameter(
     *          name="search",
     *          in="query",
     *          required=false,
     *
     *          @OA\Schema(
     *              type="string",
     *              example="name:active"
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
     *              example="name:like"
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
     *                  ref="#/components/schemas/Tag"
     *              )
     *          )
     *      )
     * )
     */
    public function search() {}

    /**
     * @OA\Delete(
     *      path="/api/v1/settings/tags/{id}",
     *      operationId="deleteTag",
     *      tags={"Tag"},
     *      summary="Delete the Tags",
     *      description="Delete the Tags",
     *      security={ {"sanctum_admin": {} }},
     *
     *      @OA\Parameter(
     *          name="id",
     *          description="Tag Id",
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
     *                  example="Tags deleted successfully.",
     *              )
     *          )
     *      )
     * )
     */
    public function destroy() {}

    /**
     * @OA\Post(
     *      path="/api/v1/settings/tags/mass-destroy",
     *      operationId="massDestroyTag",
     *      tags={"Tag"},
     *      summary="Mass Delete the Tags",
     *      description="Mass Delete the Tags",
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
     *                      property="indices",
     *                      description="Tag Ids",
     *                      type="array",
     *
     *                      @OA\Items(
     *                          type="integer",
     *                          example="1"
     *                      )
     *                  ),
     *                  required={"indices"},
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
     *                  property="message",
     *                  type="string",
     *                  example="Tags deleted successfully.",
     *              )
     *          )
     *      )
     * )
     */
    public function massDestroy() {}
}
