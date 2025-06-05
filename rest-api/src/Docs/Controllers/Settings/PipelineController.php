<?php

namespace Webkul\RestApi\Docs\Controllers\Settings;

class PipelineController
{
    /**
     * @OA\Get(
     *     path="/api/v1/settings/pipelines",
     *     operationId="getPipelines",
     *     tags={"Pipeline"},
     *     summary="Get all pipelines",
     *     security={ {"sanctum_admin": {} }},
     *
     *     @OA\Parameter(
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
     *     @OA\Response(
     *         response=200,
     *         description="Pipelines fetched successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *
     *                 @OA\Items(ref="#/components/schemas/Pipeline")
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
    public function index() {}

    /**
     * @OA\Post(
     *     path="/api/v1/settings/pipelines",
     *     operationId="createPipeline",
     *     tags={"Pipeline"},
     *     summary="Create a new pipeline",
     *     security={ {"sanctum_admin": {} }},
     *
     *     @OA\RequestBody(
     *         required=true,
     *         description="Pipeline details",
     *
     *         @OA\JsonContent(
     *             required={"name", "rotten_days", "is_default", "stages"},
     *
     *             @OA\Property(
     *                 property="name",
     *                 type="string",
     *                 description="Name of the pipeline",
     *                 example="Test"
     *             ),
     *             @OA\Property(
     *                 property="rotten_days",
     *                 type="integer",
     *                 description="Number of days after which the pipeline is considered rotten",
     *                 example=30
     *             ),
     *             @OA\Property(
     *                 property="is_default",
     *                 type="string",
     *                 description="Indicates if the pipeline is the default one",
     *                 example="on"
     *             ),
     *             @OA\Property(
     *                 property="stages",
     *                 type="object",
     *                 description="Stages of the pipeline",
     *                 @OA\Property(
     *                     property="stage_1",
     *                     type="object",
     *                     @OA\Property(property="code", type="string", example="new"),
     *                     @OA\Property(property="name", type="string", example="New"),
     *                     @OA\Property(property="sort_order", type="integer", example=1),
     *                     @OA\Property(property="probability", type="integer", example=100)
     *                 ),
     *                 @OA\Property(
     *                     property="stage_2",
     *                     type="object",
     *                     @OA\Property(property="code", type="string", example="test"),
     *                     @OA\Property(property="name", type="string", example="test"),
     *                     @OA\Property(property="sort_order", type="integer", example=2),
     *                     @OA\Property(property="probability", type="integer", example=100)
     *                 ),
     *                 @OA\Property(
     *                     property="stage_99",
     *                     type="object",
     *                     @OA\Property(property="code", type="string", example="won"),
     *                     @OA\Property(property="name", type="string", example="Won"),
     *                     @OA\Property(property="sort_order", type="integer", example=3),
     *                     @OA\Property(property="probability", type="integer", example=100)
     *                 ),
     *                 @OA\Property(
     *                     property="stage_100",
     *                     type="object",
     *                     @OA\Property(property="code", type="string", example="lost"),
     *                     @OA\Property(property="name", type="string", example="Lost"),
     *                     @OA\Property(property="sort_order", type="integer", example=4),
     *                     @OA\Property(property="probability", type="integer", example=0)
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Pipeline created successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 ref="#/components/schemas/Pipeline"
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
     * @OA\Put(
     *     path="/api/v1/settings/pipelines/{id}",
     *     operationId="updatePipeline",
     *     tags={"Pipeline"},
     *     summary="Update a pipeline",
     *     security={ {"sanctum_admin": {} }},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the pipeline",
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *         description="Pipeline details",
     *
     *         @OA\JsonContent(
     *             required={"name", "rotten_days", "is_default", "stages"},
     *
     *             @OA\Property(
     *                 property="name",
     *                 type="string",
     *                 description="Name of the pipeline",
     *                 example="Test"
     *             ),
     *             @OA\Property(
     *                 property="rotten_days",
     *                 type="integer",
     *                 description="Number of days after which the pipeline is considered rotten",
     *                 example=30
     *             ),
     *             @OA\Property(
     *                 property="is_default",
     *                 type="string",
     *                 description="Indicates if the pipeline is the default one",
     *                 example="on"
     *             ),
     *             @OA\Property(
     *                 property="stages",
     *                 type="object",
     *                 description="Stages of the pipeline",
     *                 @OA\Property(
     *                     property="7",
     *                     type="object",
     *                     @OA\Property(property="code", type="string", example="new"),
     *                     @OA\Property(property="name", type="string", example="New"),
     *                     @OA\Property(property="sort_order", type="integer", example=1),
     *                     @OA\Property(property="probability", type="integer", example=100)
     *                 ),
     *                 @OA\Property(
     *                     property="8",
     *                     type="object",
     *                     @OA\Property(property="code", type="string", example="test"),
     *                     @OA\Property(property="name", type="string", example="test"),
     *                     @OA\Property(property="sort_order", type="integer", example=2),
     *                     @OA\Property(property="probability", type="integer", example=100)
     *                 ),
     *                 @OA\Property(
     *                     property="9",
     *                     type="object",
     *                     @OA\Property(property="code", type="string", example="won"),
     *                     @OA\Property(property="name", type="string", example="Won"),
     *                     @OA\Property(property="sort_order", type="integer", example=3),
     *                     @OA\Property(property="probability", type="integer", example=100)
     *                 ),
     *                 @OA\Property(
     *                     property="10",
     *                     type="object",
     *                     @OA\Property(property="code", type="string", example="lost"),
     *                     @OA\Property(property="name", type="string", example="Lost"),
     *                     @OA\Property(property="sort_order", type="integer", example=4),
     *                     @OA\Property(property="probability", type="integer", example=0)
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Pipeline updated successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 ref="#/components/schemas/Pipeline"
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
    public function update() {}

    /**
     * @OA\Get(
     *     path="/api/v1/settings/pipelines/{id}",
     *     operationId="getPipeline",
     *     tags={"Pipeline"},
     *     summary="Get a pipeline",
     *     security={ {"sanctum_admin": {} }},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the pipeline",
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Pipeline fetched successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 ref="#/components/schemas/Pipeline"
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
    public function show() {}

    /**
     * @OA\Delete(
     *     path="/api/v1/settings/pipelines/{id}",
     *     operationId="deletePipeline",
     *     tags={"Pipeline"},
     *     summary="Delete a pipeline",
     *     security={ {"sanctum_admin": {} }},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the pipeline",
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Pipeline deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Default pipeline cannot be deleted"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function destroy() {}
}
