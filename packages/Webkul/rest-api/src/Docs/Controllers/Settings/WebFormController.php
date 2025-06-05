<?php

namespace Webkul\RestApi\Docs\Controllers\Settings;

class WebFormController
{
    /**
     *  @OA\Get(
     *      path="/api/v1/settings/web-forms",
     *      operationId="webFormList",
     *      tags={"WebForm"},
     *      summary="Get list of WebForm",
     *      security={ {"sanctum_admin": {} }},
     *
     *         @OA\Parameter(
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
     *                  @OA\Items(ref="#/components/schemas/WebForm")
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
     *      path="/api/v1/settings/web-forms/{id}",
     *      operationId="webFormFind",
     *      tags={"WebForm"},
     *      summary="Find WebForm by ID",
     *      security={ {"sanctum_admin": {} }},
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="ID of WebForm to return",
     *          required=true,
     *
     *          @OA\Schema(
     *              type="integer",
     *              format="int64"
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
     *                  type="object",
     *                  ref="#/components/schemas/WebForm"
     *              )
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=404,
     *          description="WebForm not found"
     *      )
     *  )
     */
    public function show() {}

    /**
     *  @OA\Post(
     *      path="/api/v1/settings/web-forms",
     *      operationId="webFormCreate",
     *      tags={"WebForm"},
     *      summary="Create WebForm",
     *      security={ {"sanctum_admin": {} }},
     *
     *      @OA\RequestBody(
     *          required=true,
     *
     *          @OA\JsonContent(
     *
     *                @OA\Property(
     *                    property="title",
     *                    type="string",
     *                    description="Web form title",
     *                    example="Web form title"
     *                ),
     *                @OA\Property(
     *                    property="description",
     *                    type="string",
     *                    description="Web form description",
     *                    example="webform description"
     *                ),
     *                @OA\Property(
     *                    property="submit_button_label",
     *                    type="string",
     *                    description="Label for the submit button",
     *                    example="Submit Now"
     *                ),
     *                @OA\Property(
     *                    property="submit_success_action",
     *                    type="string",
     *                    description="Action to take upon successful submission",
     *                    example="message"
     *                ),
     *                @OA\Property(
     *                    property="submit_success_content",
     *                    type="string",
     *                    description="Content to show upon successful submission",
     *                    example="This is sample test message"
     *                ),
     *                @OA\Property(
     *                    property="create_lead",
     *                    type="string",
     *                    description="Create lead option",
     *                    example="on"
     *                ),
     *                @OA\Property(
     *                    property="background_color",
     *                    type="string",
     *                    description="Background color",
     *                    example="#F7F8F9"
     *                ),
     *                @OA\Property(
     *                    property="form_background_color",
     *                    type="string",
     *                    description="Form background color",
     *                    example="#FFFFFF"
     *                ),
     *                @OA\Property(
     *                    property="form_title_color",
     *                    type="string",
     *                    description="Form title color",
     *                    example="#263238"
     *                ),
     *                @OA\Property(
     *                    property="form_submit_button_color",
     *                    type="string",
     *                    description="Form submit button color",
     *                    example="#0E90D9"
     *                ),
     *                @OA\Property(
     *                    property="attribute_label_color",
     *                    type="string",
     *                    description="Attribute label color",
     *                    example="#546E7A"
     *                ),
     *                @OA\Property(
     *                    property="attributes",
     *                    type="object",
     *                    @OA\Property(
     *                        property="attribute_0",
     *                        type="object",
     *                        @OA\Property(
     *                            property="attribute_id",
     *                            type="string",
     *                            description="ID of the attribute",
     *                            example="9"
     *                        ),
     *                        @OA\Property(
     *                            property="name",
     *                            type="string",
     *                            description="Name of the attribute",
     *                            example="Name"
     *                        ),
     *                        @OA\Property(
     *                            property="sort_order",
     *                            type="integer",
     *                            description="Sort order of the attribute",
     *                            example=1
     *                        ),
     *                        @OA\Property(
     *                            property="placeholder",
     *                            type="string",
     *                            nullable=true,
     *                            description="Placeholder for the attribute",
     *                            example=null
     *                        ),
     *                        @OA\Property(
     *                            property="is_required",
     *                            type="boolean",
     *                            description="Is the attribute required",
     *                            example=true
     *                        )
     *                    ),
     *                    description="Attributes of the web form"
     *                )
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
     *                  type="object",
     *                  ref="#/components/schemas/WebForm"
     *              )
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=422,
     *          description="Validation error"
     *      )
     *  )
     */
    public function store() {}

    /**
     *  @OA\Put(
     *      path="/api/v1/settings/web-forms/{id}",
     *      operationId="webFormUpdate",
     *      tags={"WebForm"},
     *      summary="Update WebForm",
     *      security={ {"sanctum_admin": {} }},
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="ID of WebForm to return",
     *          required=true,
     *
     *          @OA\Schema(
     *              type="integer",
     *              format="int64"
     *          )
     *      ),
     *
     *      @OA\RequestBody(
     *          required=true,
     *
     *          @OA\JsonContent(
     *
     *                @OA\Property(
     *                    property="title",
     *                    type="string",
     *                    description="Web form title",
     *                    example="Web form title"
     *                ),
     *                @OA\Property(
     *                    property="description",
     *                    type="string",
     *                    description="Web form description",
     *                    example="webform description"
     *                ),
     *                @OA\Property(
     *                    property="submit_button_label",
     *                    type="string",
     *                    description="Label for the submit button",
     *                    example="Submit Now"
     *                ),
     *                @OA\Property(
     *                    property="submit_success_action",
     *                    type="string",
     *                    description="Action to take upon successful submission",
     *                    example="message"
     *                ),
     *                @OA\Property(
     *                    property="submit_success_content",
     *                    type="string",
     *                    description="Content to show upon successful submission",
     *                    example="This is sample test message"
     *                ),
     *                @OA\Property(
     *                    property="create_lead",
     *                    type="string",
     *                    description="Create lead option",
     *                    example="on"
     *                ),
     *                @OA\Property(
     *                    property="background_color",
     *                    type="string",
     *                    description="Background color",
     *                    example="#F7F8F9"
     *                ),
     *                @OA\Property(
     *                    property="form_background_color",
     *                    type="string",
     *                    description="Form background color",
     *                    example="#FFFFFF"
     *                ),
     *                @OA\Property(
     *                    property="form_title_color",
     *                    type="string",
     *                    description="Form title color",
     *                    example="#263238"
     *                ),
     *                @OA\Property(
     *                    property="form_submit_button_color",
     *                    type="string",
     *                    description="Form submit button color",
     *                    example="#0E90D9"
     *                ),
     *                @OA\Property(
     *                    property="attribute_label_color",
     *                    type="string",
     *                    description="Attribute label color",
     *                    example="#546E7A"
     *                ),
     *                @OA\Property(
     *                    property="attributes",
     *                    type="object",
     *                    @OA\Property(
     *                        property="attribute_0",
     *                        type="object",
     *                        @OA\Property(
     *                            property="attribute_id",
     *                            type="string",
     *                            description="ID of the attribute",
     *                            example="9"
     *                        ),
     *                        @OA\Property(
     *                            property="name",
     *                            type="string",
     *                            description="Name of the attribute",
     *                            example="Name"
     *                        ),
     *                        @OA\Property(
     *                            property="sort_order",
     *                            type="integer",
     *                            description="Sort order of the attribute",
     *                            example=1
     *                        ),
     *                        @OA\Property(
     *                            property="placeholder",
     *                            type="string",
     *                            nullable=true,
     *                            description="Placeholder for the attribute",
     *                            example=null
     *                        ),
     *                        @OA\Property(
     *                            property="is_required",
     *                            type="boolean",
     *                            description="Is the attribute required",
     *                            example=true
     *                        )
     *                    ),
     *                    description="Attributes of the web form"
     *                )
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
     *                  type="object",
     *                  ref="#/components/schemas/WebForm"
     *              )
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=404,
     *          description="WebForm not found"
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation error"
     *      )
     *  )
     */
    public function update() {}

    /**
     *  @OA\Delete(
     *      path="/api/v1/settings/web-forms/{id}",
     *      operationId="webFormDelete",
     *      tags={"WebForm"},
     *      summary="Delete WebForm",
     *      security={ {"sanctum_admin": {} }},
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="ID of WebForm to return",
     *          required=true,
     *
     *          @OA\Schema(
     *              type="integer",
     *              format="int64"
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
     *                  example="Web form deleted successfully",
     *              )
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=404,
     *          description="WebForm not found"
     *      )
     *  )
     */
    public function destroy() {}
}
