<?php

namespace Webkul\RestApi\Docs\Controllers\Settings;

class WorkflowController
{
    /**
     *  @OA\Get(
     *      path="/api/v1/settings/workflows",
     *      operationId="workFlowList",
     *      tags={"Workflow"},
     *      summary="Get list of Workflow",
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
     *                  @OA\Items(ref="#/components/schemas/Workflow")
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
     *  @OA\Get(
     *      path="/api/v1/settings/workflows/{id}",
     *      operationId="showWorkflow",
     *      tags={"Workflow"},
     *      summary="Get Workflow by ID",
     *      security={ {"sanctum_admin": {} }},
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="ID of the Workflow",
     *          required=true,
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
     *          @OA\JsonContent(ref="#/components/schemas/Workflow")
     *       ),
     *
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized"
     *      )
     *  )
     */
    public function show() {}

    /**
     * @OA\Post(
     *     path="/api/v1/settings/workflows",
     *     summary="Create new workflow.",
     *     description="Create new workflow.",
     *     operationId="storeWorkflow",
     *     tags={"Workflow"},
     *     security={ {"sanctum_admin": {} }},
     *
     *     @OA\RequestBody(
     *
     *          @OA\MediaType(
     *              mediaType="application/json",
     *
     *                 @OA\Schema(
     *                    schema="WorkflowSchema",
     *                    type="object",
     *
     *                    @OA\Property(
     *                        property="name",
     *                        type="string",
     *                        description="Name of the workflow",
     *                        example="Lorem Ipsum"
     *                    ),
     *                    @OA\Property(
     *                        property="description",
     *                        type="string",
     *                        description="Description of the workflow",
     *                        example="Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s."
     *                    ),
     *                    @OA\Property(
     *                        property="entity_type",
     *                        type="string",
     *                        description="The entity type for the workflow",
     *                        example="leads"
     *                    ),
     *                    @OA\Property(
     *                        property="event",
     *                        type="string",
     *                        description="The event that triggers the workflow",
     *                        example="activity.update.after"
     *                    ),
     *                    @OA\Property(
     *                        property="condition_type",
     *                        type="string",
     *                        description="The condition type for the workflow",
     *                        example="and"
     *                    ),
     *                    @OA\Property(
     *                        property="conditions",
     *                        type="array",
     *                        description="Conditions",
     *
     *                        @OA\Items(
     *
     *                            @OA\Schema(
     *                                schema="ConditionItem",
     *                                type="object",
     *                                description="A condition item object",
     *
     *                                @OA\Property(
     *                                    property="value",
     *                                    type="array",
     *
     *                                    @OA\Items(type="string"),
     *                                    description="The values associated with the condition",
     *                                    example={"call", "meeting", "lunch"}
     *                                ),
     *
     *                                @OA\Property(
     *                                    property="operator",
     *                                    type="string",
     *                                    description="The operator for the condition",
     *                                    example="{}"
     *                                ),
     *                                @OA\Property(
     *                                    property="attribute",
     *                                    type="string",
     *                                    description="The attribute for the condition",
     *                                    example="type"
     *                                ),
     *                                @OA\Property(
     *                                    property="attribute_type",
     *                                    type="string",
     *                                    description="The type of the attribute",
     *                                    example="multiselect"
     *                                )
     *                            )
     *                        ),
     *                        example={{
     *                            "value": {"call", "meeting", "lunch"},
     *                            "operator": "{}",
     *                            "attribute": "type",
     *                            "attribute_type": "multiselect"
     *                        }}
     *                    ),
     *                    @OA\Property(
     *                        property="actions",
     *                        type="array",
     *                        description="Actions",
     *
     *                        @OA\Items(
     *
     *                            @OA\Schema(
     *                                schema="ActionItem",
     *                                type="object",
     *                                description="An action item object",
     *
     *                                @OA\Property(
     *                                    property="id",
     *                                    type="string",
     *                                    description="The ID of the action"
     *                                ),
     *                                @OA\Property(
     *                                    property="value",
     *                                    type="string",
     *                                    description="The value associated with the action"
     *                                )
     *                            )
     *                        ),
     *                        example={{"id": "send_email_to_participants", "value": "2"}}
     *                    )
     *                )
     *          )
     *      ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *
     *         @OA\JsonContent(ref="#/components/schemas/Workflow")
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Bad request"
     *     )
     * )
     */
    public function store() {}

    /**
     *  @OA\Put(
     *      path="/api/v1/settings/workflows/{id}",
     *      operationId="workFlowUpdate",
     *      tags={"Workflow"},
     *      summary="Update an existing Workflow",
     *      security={ {"sanctum_admin": {} }},
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="ID of the Workflow",
     *          required=true,
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
     *                 @OA\Schema(
     *                    schema="WorkflowSchema",
     *                    type="object",
     *
     *                    @OA\Property(
     *                        property="name",
     *                        type="string",
     *                        description="Name of the workflow",
     *                        example="Lorem Ipsum"
     *                    ),
     *                    @OA\Property(
     *                        property="description",
     *                        type="string",
     *                        description="Description of the workflow",
     *                        example="Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s."
     *                    ),
     *                    @OA\Property(
     *                        property="entity_type",
     *                        type="string",
     *                        description="The entity type for the workflow",
     *                        example="leads"
     *                    ),
     *                    @OA\Property(
     *                        property="event",
     *                        type="string",
     *                        description="The event that triggers the workflow",
     *                        example="activity.update.after"
     *                    ),
     *                    @OA\Property(
     *                        property="condition_type",
     *                        type="string",
     *                        description="The condition type for the workflow",
     *                        example="and"
     *                    ),
     *                    @OA\Property(
     *                        property="conditions",
     *                        type="array",
     *                        description="Conditions",
     *
     *                        @OA\Items(
     *
     *                            @OA\Schema(
     *                                schema="ConditionItem",
     *                                type="object",
     *                                description="A condition item object",
     *
     *                                @OA\Property(
     *                                    property="value",
     *                                    type="array",
     *
     *                                    @OA\Items(type="string"),
     *                                    description="The values associated with the condition",
     *                                    example={"call", "meeting", "lunch"}
     *                                ),
     *
     *                                @OA\Property(
     *                                    property="operator",
     *                                    type="string",
     *                                    description="The operator for the condition",
     *                                    example="{}"
     *                                ),
     *                                @OA\Property(
     *                                    property="attribute",
     *                                    type="string",
     *                                    description="The attribute for the condition",
     *                                    example="type"
     *                                ),
     *                                @OA\Property(
     *                                    property="attribute_type",
     *                                    type="string",
     *                                    description="The type of the attribute",
     *                                    example="multiselect"
     *                                )
     *                            )
     *                        ),
     *                        example={{
     *                            "value": {"call", "meeting", "lunch"},
     *                            "operator": "{}",
     *                            "attribute": "type",
     *                            "attribute_type": "multiselect"
     *                        }}
     *                    ),
     *                    @OA\Property(
     *                        property="actions",
     *                        type="array",
     *                        description="Actions",
     *
     *                        @OA\Items(
     *
     *                            @OA\Schema(
     *                                schema="ActionItem",
     *                                type="object",
     *                                description="An action item object",
     *
     *                                @OA\Property(
     *                                    property="id",
     *                                    type="string",
     *                                    description="The ID of the action"
     *                                ),
     *                                @OA\Property(
     *                                    property="value",
     *                                    type="string",
     *                                    description="The value associated with the action"
     *                                )
     *                            )
     *                        ),
     *                        example={{"id": "send_email_to_participants", "value": "2"}}
     *                    )
     *                )
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(ref="#/components/schemas/Workflow")
     *      ),
     *
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized"
     *      )
     *  )
     */
    public function update() {}

    /**
     *  @OA\Delete(
     *      path="/api/v1/settings/workflows/{id}",
     *      operationId="workFlowDelete",
     *      tags={"Workflow"},
     *      summary="Delete an existing Workflow",
     *      security={ {"sanctum_admin": {} }},
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="ID of the Workflow",
     *          required=true,
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
     *          @OA\JsonContent(ref="#/components/schemas/Workflow")
     *      ),
     *
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized"
     *      )
     *  )
     */
    public function destroy() {}
}
