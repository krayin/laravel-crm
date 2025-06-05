<?php

namespace Webkul\RestApi\Docs\Controllers\Settings\Warehouses;

class ActivityController
{
    /**
     * @OA\Get(
     *     path="/api/v1/settings/warehouses/{id}/activities",
     *     operationId="getWarehouseActivities",
     *     tags={"Warehouse"},
     *     summary="Get list of warehouse activities",
     *     security={ {"sanctum_admin": {} }},
     *
     *      @OA\Parameter(
     *          name="id",
     *          description="Warehouse ID",
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
     *                  @OA\Items(ref="#/components/schemas/Activity")
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
}
