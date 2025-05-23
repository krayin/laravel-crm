<?php

namespace Webkul\RestApi\Docs\Controllers\Lead;

class TagController
{
    /**
     * @OA\Delete(
     *      path="/api/v1/leads/{id}/tags",
     *      operationId="dettachTags",
     *      tags={"Leads"},
     *      summary="Dettached tags to the Leads",
     *      description="Dettached tags to the Leads",
     *      security={ {"sanctum_admin": {} }},
     *
     *      @OA\Parameter(
     *          name="id",
     *          description="Lead ID",
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
     *      path="/api/v1/leads/{id}/tags",
     *      operationId="attachTags",
     *      tags={"Leads"},
     *      summary="Attached tags to the Leads",
     *      description="Attached tags to the Leads",
     *      security={ {"sanctum_admin": {} }},
     *
     *      @OA\Parameter(
     *          name="id",
     *          description="Lead ID",
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
