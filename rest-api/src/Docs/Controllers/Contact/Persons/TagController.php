<?php

namespace Webkul\RestApi\Docs\Controllers\Contact\Persons;

class TagController
{
    /**
     * @OA\Delete(
     *      path="/api/v1/contacts/persons/{id}/tags",
     *      operationId="detachPersonTags",
     *      tags={"Contacts"},
     *      summary="Dettached tags to the Persons",
     *      description="Dettached tags to the Persons",
     *      security={ {"sanctum_admin": {} }},
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
     *      path="/api/v1/contacts/persons/{id}/tags",
     *      operationId="attachPersonTags",
     *      tags={"Contacts"},
     *      summary="Attached tags to the Persons",
     *      description="Attached tags to the Persons",
     *      security={ {"sanctum_admin": {} }},
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
