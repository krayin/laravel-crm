<?php

namespace Webkul\RestApi\Docs\Controllers\Settings;

class WebhookController
{
    /**
     * @OA\Get(
     *      path="/api/v1/settings/webhooks",
     *      operationId="webhooks",
     *      tags={"Webhook"},
     *      summary="Get list of Webhooks",
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
     *                  @OA\Items(ref="#/components/schemas/Webhook")
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
     *      path="/api/v1/settings/webhooks/{id}",
     *      operationId="WebhookFetch",
     *      tags={"Webhook"},
     *      summary="Get source by id",
     *      security={ {"sanctum_admin": {} }},
     *
     *      @OA\Parameter(
     *          name="id",
     *          description="Source Id",
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
     *                  @OA\Items(ref="#/components/schemas/Webhook")
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
    public function show() {}

    /**
     * @OA\Post(
     *     path="/api/v1/settings/webhooks",
     *     operationId="storeWebhook",
     *     tags={"Webhook"},
     *     summary="Create a new webhook",
     *     description="Stores a new webhook resource.",
     *     security={ {"sanctum_admin": {} }},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(
     *                 property="name",
     *                 type="string",
     *                 description="Name of the webhook",
     *                 example="test"
     *             ),
     *             @OA\Property(
     *                 property="entity_type",
     *                 type="string",
     *                 description="Entity type associated with the webhook",
     *                 example="leads"
     *             ),
     *             @OA\Property(
     *                 property="description",
     *                 type="string",
     *                 description="Description of the webhook",
     *                 example="test"
     *             ),
     *             @OA\Property(
     *                 property="method",
     *                 type="string",
     *                 description="HTTP method to be used by the webhook",
     *                 example="post"
     *             ),
     *             @OA\Property(
     *                 property="end_point",
     *                 type="string",
     *                 description="Endpoint URL for the webhook",
     *                 example="http://test.com?lead_title={%leads.title%}"
     *             ),
     *             @OA\Property(
     *                 property="query_params",
     *                 type="array",
     *                 description="Query parameters for the webhook",
     *
     *                 @OA\Items(
     *
     *                     @OA\Property(property="key", type="string", example="lead_title"),
     *                     @OA\Property(property="value", type="string", example="{%leads.title%}")
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="headers",
     *                 type="array",
     *                 description="Headers to be sent with the webhook",
     *
     *                 @OA\Items(
     *
     *                     @OA\Property(property="key", type="string", example="Content Type"),
     *                     @OA\Property(property="value", type="string", example="text/plain;charset=UTF")
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="payload_type",
     *                 type="string",
     *                 description="Type of payload sent by the webhook",
     *                 example="default"
     *             ),
     *             @OA\Property(
     *                 property="raw_payload_type",
     *                 type="string",
     *                 description="Type of raw payload sent by the webhook",
     *                 example=""
     *             ),
     *             @OA\Property(
     *                 property="payload",
     *                 type="string",
     *                 description="Payload content sent by the webhook",
     *                 example="test"
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Webhook created successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 ref="#/components/schemas/Webhook"
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Bad request"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function store() {}

    /**
     * @OA\Put(
     *     path="/api/v1/settings/webhooks/{id}",
     *     operationId="updateWebhook",
     *     tags={"Webhook"},
     *     summary="Update an existing webhook",
     *     description="Updates the specified webhook resource.",
     *     security={ {"sanctum_admin": {} }},
     *
     *     @OA\Parameter(
     *         name="id",
     *         description="ID of the webhook to update",
     *         required=true,
     *         in="path",
     *
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         )
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(
     *                 property="name",
     *                 type="string",
     *                 description="Name of the webhook",
     *                 example="test"
     *             ),
     *             @OA\Property(
     *                 property="entity_type",
     *                 type="string",
     *                 description="Entity type associated with the webhook",
     *                 example="leads"
     *             ),
     *             @OA\Property(
     *                 property="description",
     *                 type="string",
     *                 description="Description of the webhook",
     *                 example="test"
     *             ),
     *             @OA\Property(
     *                 property="method",
     *                 type="string",
     *                 description="HTTP method to be used by the webhook",
     *                 example="post"
     *             ),
     *             @OA\Property(
     *                 property="end_point",
     *                 type="string",
     *                 description="Endpoint URL for the webhook",
     *                 example="http://test.com?lead_title={%leads.title%}"
     *             ),
     *             @OA\Property(
     *                 property="query_params",
     *                 type="array",
     *                 description="Query parameters for the webhook",
     *
     *                 @OA\Items(
     *
     *                     @OA\Property(property="key", type="string", example="lead_title"),
     *                     @OA\Property(property="value", type="string", example="{%leads.title%}")
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="headers",
     *                 type="array",
     *                 description="Headers to be sent with the webhook",
     *
     *                 @OA\Items(
     *
     *                     @OA\Property(property="key", type="string", example="Content Type"),
     *                     @OA\Property(property="value", type="string", example="text/plain;charset=UTF")
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="payload_type",
     *                 type="string",
     *                 description="Type of payload sent by the webhook",
     *                 example="default"
     *             ),
     *             @OA\Property(
     *                 property="raw_payload_type",
     *                 type="string",
     *                 description="Type of raw payload sent by the webhook",
     *                 example=""
     *             ),
     *             @OA\Property(
     *                 property="payload",
     *                 type="string",
     *                 description="Payload content sent by the webhook",
     *                 example="test"
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Webhook updated successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 ref="#/components/schemas/Webhook"
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Bad request"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found"
     *     )
     * )
     */
    public function update() {}

    /**
     * @OA\Delete(
     *     path="/api/v1/settings/webhooks/{id}",
     *     operationId="deleteWebhook",
     *     tags={"Webhook"},
     *     summary="Delete a webhook",
     *     description="Deletes the specified webhook resource.",
     *     security={ {"sanctum_admin": {} }},
     *
     *     @OA\Parameter(
     *         name="id",
     *         description="ID of the webhook to delete",
     *         required=true,
     *         in="path",
     *
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Webhook deleted successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Webhook deleted successfully."
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found"
     *     )
     * )
     */
    public function destroy() {}
}
