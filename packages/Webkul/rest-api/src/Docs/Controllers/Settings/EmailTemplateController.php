<?php

namespace Webkul\RestApi\Docs\Controllers\Settings;

class EmailTemplateController
{
    /**
     * @OA\Get(
     *      path="/api/v1/settings/email-templates",
     *      operationId="emailTemplateList",
     *      tags={"EmailTemplate"},
     *      summary="Get list of email templates",
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
     *                  @OA\Items(ref="#/components/schemas/EmailTemplate")
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
     *      path="/api/v1/settings/email-templates/{id}",
     *      operationId="emailTemplateFetch",
     *      tags={"EmailTemplate"},
     *      summary="Get email template by id",
     *      security={ {"sanctum_admin": {} }},
     *
     *      @OA\Parameter(
     *          name="id",
     *          description="EmailTemplate Id",
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
     *          @OA\JsonContent(ref="#/components/schemas/EmailTemplate")
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
     * @OA\Post(
     *      path="/api/v1/settings/email-templates",
     *      operationId="emailTemplateCreate",
     *      tags={"EmailTemplate"},
     *      summary="Create new email template",
     *      security={ {"sanctum_admin": {} }},
     *
     *     @OA\RequestBody(
     *
     *          @OA\MediaType(
     *              mediaType="application/json",
     *
     *              @OA\Schema(
     *
     *                  @OA\Property(
     *                      property="name",
     *                      description="name",
     *                      type="string",
     *                      example="Activity Updated"
     *                  ),
     *                  @OA\Property(
     *                      property="subject",
     *                      description="Subject of the Email",
     *                      type="string",
     *                      example="Activity updated: {%activities.title%}"
     *                  ),
     *                  @OA\Property(
     *                      property="content",
     *                      description="Content of the Email",
     *                      type="string",
     *                      example="content",
     *                  ),
     *              )
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(ref="#/components/schemas/EmailTemplate")
     *       ),
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
     *      path="/api/v1/settings/email-templates/{id}",
     *      operationId="emailTemplateUpdate",
     *      tags={"EmailTemplate"},
     *      summary="Update existing email template",
     *      security={ {"sanctum_admin": {} }},
     *
     *      @OA\Parameter(
     *          name="id",
     *          description="EmailTemplate Id",
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
     *                      description="name",
     *                      type="string",
     *                      example="Activity Updated"
     *                  ),
     *                  @OA\Property(
     *                      property="subject",
     *                      description="Subject of the Email",
     *                      type="string",
     *                      example="Activity updated: {%activities.title%}"
     *                  ),
     *                  @OA\Property(
     *                      property="content",
     *                      description="Content of the Email",
     *                      type="string",
     *                      example="content",
     *                  ),
     *              )
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(ref="#/components/schemas/EmailTemplate")
     *       ),
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
     *      path="/api/v1/settings/email-templates/{id}",
     *      operationId="emailTemplateDelete",
     *      tags={"EmailTemplate"},
     *      summary="Delete one email template",
     *      security={ {"sanctum_admin": {} }},
     *
     *      @OA\Parameter(
     *          name="id",
     *          description="EmailTemplate Id",
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
     *          @OA\JsonContent(ref="#/components/schemas/EmailTemplate")
     *       ),
     *
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized"
     *      )
     * )
     */
    public function destroy() {}
}
