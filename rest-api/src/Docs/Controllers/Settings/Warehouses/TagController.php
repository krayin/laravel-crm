<?php

namespace Webkul\RestApi\Docs\Controllers\Settings\Warehouses;

class TagController
{
    /**
     * @OA\Delete(
     *      path="/api/v1/settings/warehouses/{id}/tags",
     *      operationId="dettachWarehouseTags",
     *      tags={"Warehouse"},
     *      summary="Dettached tags to the Warehouses",
     *      description="Dettached tags to the Warehouses",
     *      security={ {"sanctum_admin": {} }},
     *
     *      @OA\Parameter(
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
     *      @OA\RequestBody(
     *          required=true,
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(
     *                  property="tag_id",
     *                  type="integer",
     *                  description="Tag Id",
     *                  example=1
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
     *                  example="Tag attached successfully."),
     *              )
     *          )
     *      )
     * )
     */
    public function detach() {}

    /**
     * @OA\Post(
     *      path="/api/v1/settings/warehouses/{id}/tags",
     *      operationId="attachWarehouseTags",
     *      tags={"Warehouse"},
     *      summary="Attached tags to the Warehouses",
     *      description="Attached tags to the Warehouses",
     *      security={ {"sanctum_admin": {} }},
     *
     *      @OA\Parameter(
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
     *      @OA\RequestBody(
     *          required=true,
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(
     *                  property="tag_id",
     *                  type="integer",
     *                  description="Tag Id",
     *                  example=1
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
     *                  example="Tag attached successfully."),
     *              )
     *          )
     *      )
     * )
     */
    public function attach() {}
}
