<?php

namespace Webkul\RestApi\Docs\Controllers\Lead;

class EmailController
{
    /**
     * @OA\Post(
     *      path="/api/v1/leads/{id}/emails",
     *      operationId="sendEmailToLead",
     *      tags={"Leads"},
     *      summary="Send an Email to a Lead",
     *      description="Send an Email to a Lead with attachments",
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
     *                      property="lead_id",
     *                      type="integer",
     *                      description="Lead ID associated with the email",
     *                      example=1
     *                  ),
     *                  @OA\Property(
     *                      property="reply_to[]",
     *                      type="array",
     *                      description="List of email addresses to reply to",
     *
     *                      @OA\Items(
     *                          type="string",
     *                          format="email",
     *                          example="test@mail.com"
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
     *                  required={"type", "lead_id", "subject", "reply"}
     *              )
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Email sent successfully",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="message", type="string", example="Email sent successfully.")
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated"
     *      )
     * )
     */
    public function store() {}

    /**
     * @OA\Delete(
     *      path="/api/v1/leads/{id}/emails",
     *      operationId="detachEmailFromLead",
     *      tags={"Leads"},
     *      summary="Detach an Email from a Lead",
     *      description="Delete an association of an email with a specific lead.",
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
     *              type="object",
     *
     *              @OA\Property(
     *                  property="email_id",
     *                  type="integer",
     *                  description="ID of the email to be detached",
     *                  example=1
     *              )
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Email detached successfully",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="message", type="string", example="Email detached successfully.")
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Lead or Email not found"
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal server error"
     *      )
     * )
     */
    public function detach() {}
}
