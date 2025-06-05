<?php

namespace Webkul\RestApi\Docs\Controllers\Mail;

class EmailController
{
    /**
     * @OA\Get(
     *      path="/api/v1/mails",
     *      operationId="mailList",
     *      tags={"Mail"},
     *      summary="Get list of mails",
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
     *                  @OA\Items(ref="#/components/schemas/Email")
     *              )
     *          )
     *     ),
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
     *     path="/api/v1/mails",
     *     tags={"Mail"},
     *     summary="Store an email",
     *     description="Store an email with the provided data",
     *     operationId="storeEmail",
     *     security={ {"sanctum_admin": {} }},
     *
     *     @OA\RequestBody(
     *          required=true,
     *
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *
     *              @OA\Schema(
     *
     *                  @OA\Property(
     *                      property="type",
     *                      type="string",
     *                      description="Type of the email",
     *                      example="email"
     *                  ),
     *                  @OA\Property(
     *                      property="is_draft",
     *                      type="boolean",
     *                      description="Indicates if the email is a draft or not",
     *                      example=true
     *                  ),

     *                  @OA\Property(
     *                      property="reply_to[]",
     *                      type="array",
     *                      description="List of email addresses to reply to",
     *
     *                      @OA\Items(
     *                          type="string",
     *                          format="email",
     *                          example="example@mail.com"
     *                      )
     *                  ),
     *
     *                  @OA\Property(
     *                      property="cc[]",
     *                      type="array",
     *                      description="List of email addresses to cc",
     *
     *                      @OA\Items(
     *                          type="string",
     *                          format="email",
     *                          example="example@mail.com"
     *                      )
     *                  ),
     *
     *                  @OA\Property(
     *                      property="bcc[]",
     *                      type="array",
     *                      description="List of email addresses to bcc",
     *
     *                      @OA\Items(
     *                          type="string",
     *                          format="email",
     *                          example="example@mail.com"
     *                      )
     *                  ),
     *
     *                  @OA\Property(
     *                      property="subject",
     *                      type="string",
     *                      description="Subject of the email",
     *                      example="subject"
     *                  ),
     *                  @OA\Property(
     *                      property="reply",
     *                      type="string",
     *                      description="Message content of the email",
     *                      example="message"
     *                  ),
     *                  @OA\Property(
     *                      property="attachments[]",
     *                      type="array",
     *                      description="Attachments of the email",
     *
     *                      @OA\Items(
     *                          type="file",
     *                      )
     *                  ),
     *              )
     *          )
     *      ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *
     *         @OA\JsonContent(
     *
     *              @OA\Property(
     *                 property="data",
     *                 type="array",
     *
     *                 @OA\Items(ref="#/components/schemas/Email")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function store() {}

    /**
     * @OA\Get(
     *      path="/api/v1/mails/{id}",
     *      operationId="mailGet",
     *      tags={"Mail"},
     *      summary="Get mail information",
     *      security={ {"sanctum_admin": {} }},
     *
     *      @OA\Parameter(
     *          name="id",
     *          description="Mail Id",
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
     *          @OA\Property(
     *              property="data",
     *              type="Object",
     *              ref="#/components/schemas/Email"
     *          )
     *      ),
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
     *      path="/api/v1/mails/{id}",
     *      operationId="mailUpdate",
     *      tags={"Mail"},
     *      summary="Update existing mail",
     *      security={ {"sanctum_admin": {} }},
     *
     *      @OA\Parameter(
     *          name="id",
     *          description="Mail Id",
     *          required=true,
     *          in="path",
     *
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *
     *     @OA\RequestBody(
     *          required=true,
     *
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *
     *              @OA\Schema(
     *
     *                  @OA\Property(
     *                      property="type",
     *                      type="string",
     *                      description="Type of the email",
     *                      example="email"
     *                  ),
     *                  @OA\Property(
     *                      property="is_draft",
     *                      type="boolean",
     *                      description="Indicates if the email is a draft or not",
     *                      example=true
     *                  ),
     *                  @OA\Property(
     *                      property="_method",
     *                      type="string",
     *                      example="PUT",
     *                      description="Method to be used for the request"
     *                  ),
     *                  @OA\Property(
     *                      property="reply_to[]",
     *                      type="array",
     *                      description="List of email addresses to reply to",
     *
     *                      @OA\Items(
     *                          type="string",
     *                          format="email",
     *                          example="example@mail.com"
     *                      )
     *                  ),
     *
     *                  @OA\Property(
     *                      property="cc[]",
     *                      type="array",
     *                      description="List of email addresses to cc",
     *
     *                      @OA\Items(
     *                          type="string",
     *                          format="email",
     *                          example="example@mail.com"
     *                      )
     *                  ),
     *
     *                  @OA\Property(
     *                      property="bcc[]",
     *                      type="array",
     *                      description="List of email addresses to bcc",
     *
     *                      @OA\Items(
     *                          type="string",
     *                          format="email",
     *                          example="example@mail.com"
     *                      )
     *                  ),
     *
     *                  @OA\Property(
     *                      property="subject",
     *                      type="string",
     *                      description="Subject of the email",
     *                      example="subject"
     *                  ),
     *                  @OA\Property(
     *                      property="reply",
     *                      type="string",
     *                      description="Message content of the email",
     *                      example="message"
     *                  ),
     *                  @OA\Property(
     *                      property="attachments[]",
     *                      type="array",
     *                      description="Attachments of the email",
     *
     *                      @OA\Items(
     *                          type="file",
     *                      )
     *                  ),
     *              )
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(ref="#/components/schemas/Email")
     *      ),
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
     *      path="/api/v1/mails/{id}",
     *      operationId="mailDelete",
     *      tags={"Mail"},
     *      summary="Delete existing mail",
     *      security={ {"sanctum_admin": {} }},
     *
     *      @OA\Parameter(
     *          name="id",
     *          description="Mail Id",
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
     *             @OA\Property(
     *                  property="type",
     *                  type="string",
     *                  description="Type of delete",
     *                  example="trash"
     *             ),
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(ref="#/components/schemas/Email")
     *      ),
     *
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized"
     *      )
     * )
     */
    public function destroy() {}

    /**
     * @OA\Post(
     *      path="/api/v1/mails/mass-update",
     *      operationId="mailMassUpdate",
     *      tags={"Mail"},
     *      summary="Mass update mails",
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
     *                      type="integer"
     *                  )
     *              ),
     *
     *              @OA\Property(
     *                  property="value",
     *                  type="string",
     *                  example="NA"
     *              ),
     *              @OA\Property(
     *                  property="folders",
     *                  type="array",
     *
     *                  @OA\Items(
     *                      type="string",
     *                      example="inbox"
     *                  )
     *              ),
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
     *                  type="array",
     *
     *                  @OA\Items(ref="#/components/schemas/Email")
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
    public function massUpdate() {}

    /**
     * @OA\Post(
     *      path="/api/v1/mails/mass-destroy",
     *      operationId="mailMassDestroy",
     *      tags={"Mail"},
     *      summary="Mass delete mails",
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
     *              ),
     *
     *              @OA\Property(
     *                  property="type",
     *                  type="string",
     *                  example=""
     *              ),
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
     *                  type="array",
     *
     *                  @OA\Items(ref="#/components/schemas/Email")
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

    /**
     * @OA\Get(
     *      path="/api/v1/mails/attachment-download/{id}",
     *      operationId="mailAttachmentDownload",
     *      tags={"Mail"},
     *      summary="Download attachment",
     *      security={ {"sanctum_admin": {} }},
     *
     *      @OA\Parameter(
     *          name="id",
     *          description="Attachment Id",
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
     *          @OA\Property(
     *              property="data",
     *              type="file",
     *              format="binary"
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized"
     *      )
     * )
     */
    public function download() {}
}
