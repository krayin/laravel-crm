<?php

namespace Webkul\RestApi\Docs\Controllers\Activity;

class ActivityController
{
    /**
     * @OA\Get(
     *     path="/api/v1/activities",
     *     operationId="activityList",
     *     tags={"Activity"},
     *     summary="Get list of activities",
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
     *         description="Successful operation",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *
     *                 @OA\Items(ref="#/components/schemas/Activity")
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
     * @OA\Get(
     *     path="/api/v1/activities/{id}",
     *     operationId="activityFetch",
     *     tags={"Activity"},
     *     summary="Fetch activity",
     *     security={ {"sanctum_admin": {} }},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Activity Id",
     *         required=true,
     *
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(
     *                 property="data",
     *                 type="Object",
     *                 ref="#/components/schemas/Activity"
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
     * @OA\Post(
     *     path="/api/v1/activities",
     *     operationId="activityStore",
     *     tags={"Activity"},
     *     summary="Create activity",
     *     security={{"sanctum_admin": {}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\MediaType(
     *             mediaType="application/json",
     *
     *             @OA\Schema(
     *
     *                 @OA\Property(
     *                     property="lead_id",
     *                     title="Lead ID",
     *                     description="ID of the Activity",
     *                     example="1",
     *                     type="integer"
     *                 ),
     *                 @OA\Property(
     *                     property="title",
     *                     title="Title",
     *                     description="Title of the Activity",
     *                     example="Lorem Ipsum",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="type",
     *                     title="Type",
     *                     description="Type of the Activity",
     *                     example="meeting",
     *                     enum={"call", "meeting", "lunch", "file", "note"},
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="schedule_from",
     *                     title="Schedule From",
     *                     description="Schedule From of the Activity",
     *                     example="2025-09-01 10:00:00",
     *                     type="string",
     *                     format="date-time"
     *                 ),
     *                 @OA\Property(
     *                     property="schedule_to",
     *                     title="Schedule To",
     *                     description="Schedule To of the Activity",
     *                     example="2025-11-01 10:00:00",
     *                     type="string",
     *                     format="date-time"
     *                 ),
     *                 @OA\Property(
     *                     property="location",
     *                     title="Location",
     *                     description="Location of the Activity",
     *                     example="New York",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="comment",
     *                     title="Comment",
     *                     description="Comment of the Activity",
     *                     example="Lorem Ipsum",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="participants",
     *                     type="object",
     *                     @OA\Property(
     *                         property="persons",
     *                         type="array",
     *
     *                         @OA\Items(
     *                             type="string",
     *                             example="1"
     *                         ),
     *                         description="List of person IDs"
     *                     ),
     *
     *                     @OA\Property(
     *                         property="users",
     *                         type="array",
     *
     *                         @OA\Items(
     *                             type="string",
     *                             example="1"
     *                         ),
     *                         description="List of user IDs"
     *                     ),
     *                     description="Participants object containing users"
     *                 )
     *             )
     *         ),
     *
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *
     *             @OA\Schema(
     *
     *                 @OA\Property(
     *                     property="lead_id",
     *                     title="Lead ID",
     *                     description="ID of the Activity",
     *                     example="1",
     *                     type="integer"
     *                 ),
     *                 @OA\Property(
     *                     property="title",
     *                     title="Title",
     *                     description="Title of the Activity",
     *                     example="Lorem Ipsum",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="type",
     *                     title="Type",
     *                     description="Type of the Activity",
     *                     example="meeting",
     *                     enum={"call", "meeting", "lunch", "file", "note"},
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                      property="file",
     *                      type="file",
     *                      description="When you upload file type must be file."
     *                 ),
     *                 @OA\Property(
     *                     property="schedule_from",
     *                     title="Schedule From",
     *                     description="Schedule From of the Activity",
     *                     example="2025-09-01 10:00:00",
     *                     type="string",
     *                     format="date-time"
     *                 ),
     *                 @OA\Property(
     *                     property="schedule_to",
     *                     title="Schedule To",
     *                     description="Schedule To of the Activity",
     *                     example="2025-11-01 10:00:00",
     *                     type="string",
     *                     format="date-time"
     *                 ),
     *                 @OA\Property(
     *                     property="location",
     *                     title="Location",
     *                     description="Location of the Activity",
     *                     example="New York",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="comment",
     *                     title="Comment",
     *                     description="Comment of the Activity",
     *                     example="Lorem Ipsum",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="participants[persons][]",
     *                     type="array",
     *
     *                     @OA\Items(
     *                         type="string",
     *                         example="1"
     *                     ),
     *                     description="List of person IDs"
     *                 ),
     *
     *                 @OA\Property(
     *                     property="participants[users][]",
     *                     type="array",
     *
     *                     @OA\Items(
     *                         type="string",
     *                         example="1"
     *                     ),
     *                     description="List of user IDs"
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 ref="#/components/schemas/Activity"
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
     *     path="/api/v1/activities/{id}",
     *     operationId="activityUpdate",
     *     tags={"Activity"},
     *     summary="Update activity",
     *     security={ {"sanctum_admin": {} }},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Activity Id",
     *         required=true,
     *
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\MediaType(
     *             mediaType="application/json",
     *
     *             @OA\Schema(
     *
     *                 @OA\Property(
     *                     property="lead_id",
     *                     title="Lead ID",
     *                     description="ID of the Activity",
     *                     example="1",
     *                     type="integer"
     *                 ),
     *                 @OA\Property(
     *                     property="title",
     *                     title="Title",
     *                     description="Title of the Activity",
     *                     example="Lorem Ipsum",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="type",
     *                     title="Type",
     *                     description="Type of the Activity",
     *                     example="meeting",
     *                     enum={"call", "meeting", "lunch", "file", "note"},
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="schedule_from",
     *                     title="Schedule From",
     *                     description="Schedule From of the Activity",
     *                     example="2025-09-01 10:00:00",
     *                     type="string",
     *                     format="date-time"
     *                 ),
     *                 @OA\Property(
     *                     property="schedule_to",
     *                     title="Schedule To",
     *                     description="Schedule To of the Activity",
     *                     example="2025-11-01 10:00:00",
     *                     type="string",
     *                     format="date-time"
     *                 ),
     *                 @OA\Property(
     *                     property="location",
     *                     title="Location",
     *                     description="Location of the Activity",
     *                     example="New York",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="comment",
     *                     title="Comment",
     *                     description="Comment of the Activity",
     *                     example="Lorem Ipsum",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="participants",
     *                     type="object",
     *                     @OA\Property(
     *                         property="persons",
     *                         type="array",
     *
     *                         @OA\Items(
     *                             type="string",
     *                             example="1"
     *                         ),
     *                         description="List of person IDs"
     *                     ),
     *
     *                     @OA\Property(
     *                         property="users",
     *                         type="array",
     *
     *                         @OA\Items(
     *                             type="string",
     *                             example="1"
     *                         ),
     *                         description="List of user IDs"
     *                     ),
     *                     description="Participants object containing users"
     *                 )
     *             )
     *         ),
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 ref="#/components/schemas/Activity"
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
     *     path="/api/v1/activities/file-download/{id}",
     *     operationId="activityDownload",
     *     tags={"Activity"},
     *     summary="Download file",
     *     security={ {"sanctum_admin": {} }},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Activity File Id",
     *         required=true,
     *
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="File downloaded successfully",
     *
     *         @OA\MediaType(
     *             mediaType="application/octet-stream",
     *
     *             @OA\Schema(
     *                 type="string",
     *                 format="binary"
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
    public function download() {}

    /**
     * @OA\Delete(
     *     path="/api/v1/activities/{id}",
     *     operationId="activityDelete",
     *     tags={"Activity"},
     *     summary="Delete activity",
     *     security={ {"sanctum_admin": {} }},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Activity Id",
     *         required=true,
     *
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function destroy() {}

    /**
     * @OA\Post(
     *     path="/api/v1/activities/mass-update",
     *     operationId="activityMassUpdate",
     *     tags={"Activity"},
     *     summary="Mass update activities",
     *     security={ {"sanctum_admin": {} }},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(
     *                 property="indices",
     *                 type="array",
     *                 description="IDs of the Activities to be updated",
     *
     *                 @OA\Items(
     *                     type="integer",
     *                     example=1
     *                 )
     *             ),
     *
     *             @OA\Property(
     *                 property="value",
     *                 type="string",
     *                 description="Value to be update",
     *                 example="1"
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function massUpdate() {}

    /**
     * @OA\Post(
     *     path="/api/v1/activities/mass-destroy",
     *     operationId="activityMassDestroy",
     *     tags={"Activity"},
     *     summary="Mass destroy activities",
     *     security={ {"sanctum_admin": {} }},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(
     *                 property="indices",
     *                 type="array",
     *                 description="IDs of the Activities to be deleted",
     *
     *                 @OA\Items(
     *                     type="integer",
     *                     example=1
     *                 )
     *             ),
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function massDestroy() {}
}
