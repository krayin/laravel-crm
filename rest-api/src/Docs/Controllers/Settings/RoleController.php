<?php

namespace Webkul\RestApi\Docs\Controllers\Settings;

class RoleController
{
    /**
     * @OA\Get(
     *      path="/api/v1/settings/roles",
     *      operationId="roleList",
     *      tags={"Role"},
     *      summary="Get list of roles",
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
     *                  @OA\Items(ref="#/components/schemas/Role")
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
     *      path="/api/v1/settings/roles/{id}",
     *      operationId="roleFetch",
     *      tags={"Role"},
     *      summary="Get role details",
     *      security={ {"sanctum_admin": {} }},
     *
     *      @OA\Parameter(
     *          name="id",
     *          description="Role ID",
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
     *          @OA\JsonContent(ref="#/components/schemas/Role")
     *       ),
     *
     *      @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function show() {}

    /**
     * @OA\Post(
     *      path="/api/v1/settings/roles",
     *      operationId="roleCreate",
     *      tags={"Role"},
     *      summary="Create new role",
     *      security={ {"sanctum_admin": {} }},
     *
     *      @OA\RequestBody(
     *           required=true,
     *           description="Role details",
     *
     *           @OA\JsonContent(
     *
     *               @OA\Property(
     *                   property="name",
     *                   type="string",
     *                   description="Role name",
     *                   example="Sales Manager"
     *               ),
     *               @OA\Property(
     *                   property="description",
     *                   type="string",
     *                   description="Role Description",
     *                   example="Sales Manager"
     *               ),
     *               @OA\Property(
     *                   property="permission_type",
     *                   type="string",
     *                   description="Role type permission",
     *                   example="custom"
     *               ),
     *               @OA\Property(
     *                   property="permissions",
     *                   description="Role permissions",
     *                   type="array",
     *                   description="List of permissions",
     *
     *                   @OA\Items(
     *                       type="string",
     *                       example="dashboard"
     *                   ),
     *               )
     *           )
     *       ),
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
     *                  ref="#/components/schemas/Role"
     *              )
     *          )
     *      ),
     *
     *      @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function store() {}

    /**
     * @OA\Put(
     *      path="/api/v1/settings/roles/{id}",
     *      operationId="roleUpdate",
     *      tags={"Role"},
     *      summary="Update existing role",
     *      security={ {"sanctum_admin": {} }},
     *
     *      @OA\Parameter(
     *          name="id",
     *          description="Role ID",
     *          required=true,
     *          in="path",
     *
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *
     *      @OA\RequestBody(
     *           required=true,
     *           description="Role details",
     *
     *           @OA\JsonContent(
     *
     *               @OA\Property(
     *                   property="name",
     *                   type="string",
     *                   description="Role name",
     *                   example="Sales Manager"
     *               ),
     *               @OA\Property(
     *                   property="description",
     *                   type="string",
     *                   description="Role Description",
     *                   example="Sales Manager"
     *               ),
     *               @OA\Property(
     *                   property="permission_type",
     *                   type="string",
     *                   description="Role type permission",
     *                   example="custom"
     *               ),
     *               @OA\Property(
     *                   property="permissions",
     *                   description="Role permissions",
     *                   type="array",
     *                   description="List of permissions",
     *
     *                   @OA\Items(
     *                       type="string",
     *                       example="dashboard"
     *                   ),
     *               )
     *           )
     *       ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(ref="#/components/schemas/Role")
     *       ),
     *
     *      @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function update() {}

    /**
     * @OA\Delete(
     *      path="/api/v1/settings/roles/{id}",
     *      operationId="roleDelete",
     *      tags={"Role"},
     *      summary="Delete existing role",
     *      security={ {"sanctum_admin": {} }},
     *
     *      @OA\Parameter(
     *          name="id",
     *          description="Role ID",
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
     *          @OA\JsonContent(ref="#/components/schemas/Role")
     *       ),
     *
     *      @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function destroy() {}
}
