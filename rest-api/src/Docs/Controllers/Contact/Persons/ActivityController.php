<?php

namespace Webkul\RestApi\Docs\Controllers\Contact\Persons;

class ActivityController
{
    /**
     * @OA\Get(
     *     path="/api/v1/contacts/persons/{id}/activities",
     *     operationId="getPersonActivities",
     *     tags={"Contacts"},
     *     summary="Get list of persons",
     *     security={ {"sanctum_admin": {} }},
     *
     *      @OA\Parameter(
     *          name="id",
     *          description="Person ID",
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
