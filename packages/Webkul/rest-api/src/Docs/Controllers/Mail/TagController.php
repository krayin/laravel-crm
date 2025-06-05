<?php

namespace Webkul\RestApi\Docs\Controllers\Mail;

class TagController
{
    /**
     * @OA\Delete(
     *      path="/api/v1/mails/{id}/tags",
     *      operationId="detachEmailTags",
     *      tags={"Mail"},
     *      summary="Dettached tags to the mail",
     *      description="Dettached tags to the mail",
     *      security={ {"sanctum_admin": {} }},
     *
     *      @OA\Parameter(
     *          name="id",
     *          description="Mail ID",
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
     *      path="/api/v1/mails/{id}/tags",
     *      operationId="attachEmailTags",
     *      tags={"Mail"},
     *      summary="Attached tags to the mail",
     *      description="Attached tags to the mail",
     *      security={ {"sanctum_admin": {} }},
     *
     *      @OA\Parameter(
     *          name="id",
     *          description="Mail ID",
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
