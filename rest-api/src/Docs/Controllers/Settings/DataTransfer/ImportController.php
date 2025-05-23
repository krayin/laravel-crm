<?php

namespace Webkul\RestApi\Docs\Controllers\Settings\DataTransfer;

class ImportController
{
    /**
     * @OA\Get(
     *      path="/api/v1/settings/data-transfer/imports",
     *      operationId="importList",
     *      tags={"DataTransfer"},
     *      summary="Get list of Import",
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
     *                  @OA\Items(ref="#/components/schemas/Import")
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
     *      path="/api/v1/settings/data-transfer/imports/download-sample/{sample}",
     *      operationId="importSample",
     *      tags={"DataTransfer"},
     *      summary="Download sample import file",
     *      security={ {"sanctum_admin": {} }},
     *
     *      @OA\Parameter(
     *          name="sample",
     *          in="path",
     *          required=true,
     *          description="Type of sample file to download",
     *
     *          @OA\Schema(
     *              type="string",
     *              enum={"persons", "products", "leads"}
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\MediaType(
     *              mediaType="application/csv",
     *
     *              @OA\Schema(
     *                  type="string",
     *                  format="binary"
     *              )
     *          )
     *      )
     * )
     */
    public function downloadSample() {}

    /**
     * @OA\Post(
     *      path="/api/v1/settings/data-transfer/imports",
     *      operationId="importCreate",
     *      tags={"DataTransfer"},
     *      summary="Create new Import",
     *      security={ {"sanctum_admin": {} }},
     *
     *      @OA\RequestBody(
     *          required=true,
     *
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *
     *              @OA\Schema(
     *                  type="object",
     *                  required={"type", "action", "validation_strategy", "allowed_errors", "field_separator", "file"},
     *
     *                  @OA\Property(
     *                      property="type",
     *                      description="Type of import data",
     *                      type="string",
     *                      enum={"persons", "products", "leads"},
     *                      example="persons"
     *                  ),
     *                  @OA\Property(
     *                      property="action",
     *                      description="Action to perform",
     *                      type="string",
     *                      enum={"append", "delete"},
     *                      example="create/update"
     *                  ),
     *                  @OA\Property(
     *                      property="validation_strategy",
     *                      description="Validation strategy",
     *                      type="string",
     *                      enum={"stop-on-errors", "skip-erros"},
     *                      example="stop-on-errors"
     *                  ),
     *                  @OA\Property(
     *                      property="allowed_errors",
     *                      description="Number of allowed errors before stopping",
     *                      type="integer",
     *                      example=10
     *                  ),
     *                  @OA\Property(
     *                      property="field_separator",
     *                      description="Field separator character in file",
     *                      type="string",
     *                      example=","
     *                  ),
     *                  @OA\Property(
     *                      property="process_in_queue",
     *                      description="Whether to process in queue (optional)",
     *                      type="string",
     *                      example="on"
     *                  ),
     *                  @OA\Property(
     *                      property="file",
     *                      description="File to import",
     *                      type="string",
     *                      format="binary"
     *                  )
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
     *                  property="data",
     *                  ref="#/components/schemas/Import",
     *                  type="object"
     *              )
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=422,
     *          description="Validation error"
     *      )
     * )
     */
    public function store() {}

    /**
     * @OA\Get(
     *      path="/api/v1/settings/data-transfer/imports/validate/{importId}",
     *      operationId="validateImport",
     *      tags={"DataTransfer"},
     *      summary="Validate import file",
     *      security={ {"sanctum_admin": {} }},
     *
     *      @OA\Parameter(
     *          name="importId",
     *          in="path",
     *          required=true,
     *          description="ID of the import",
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
     *              type="object",
     *
     *              @OA\Property(property="is_valid", type="boolean", example=false),
     *              @OA\Property(
     *                  property="import",
     *                  type="object",
     *                  @OA\Property(property="id", type="integer", example=2),
     *                  @OA\Property(property="state", type="string", example="validated"),
     *                  @OA\Property(property="process_in_queue", type="integer", example=1),
     *                  @OA\Property(property="type", type="string", example="persons"),
     *                  @OA\Property(property="action", type="string", example="append"),
     *                  @OA\Property(property="validation_strategy", type="string", example="stop-on-errors"),
     *                  @OA\Property(property="allowed_errors", type="integer", example=10),
     *                  @OA\Property(property="processed_rows_count", type="integer", example=2),
     *                  @OA\Property(property="invalid_rows_count", type="integer", example=2),
     *                  @OA\Property(property="errors_count", type="integer", example=2),
     *                  @OA\Property(
     *                      property="errors",
     *                      type="array",
     *
     *                      @OA\Items(type="string", example="Row(s) 1, 2: The selected organization id is invalid.")
     *                  ),
     *
     *                  @OA\Property(property="field_separator", type="string", example=","),
     *                  @OA\Property(property="file_path", type="string", example="imports/1746077606-persons.csv"),
     *                  @OA\Property(property="error_file_path", type="string", example="imports/1746077857-error-report.csv"),
     *                  @OA\Property(property="summary", type="string", nullable=true, example=null),
     *                  @OA\Property(property="started_at", type="string", format="date-time", nullable=true, example=null),
     *                  @OA\Property(property="completed_at", type="string", format="date-time", nullable=true, example=null),
     *                  @OA\Property(property="created_at", type="string", format="date-time", example="2025-05-01T05:33:26.000000Z"),
     *                  @OA\Property(property="updated_at", type="string", format="date-time", example="2025-05-01T05:37:37.000000Z")
     *              )
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=404,
     *          description="Import record not found"
     *      )
     * )
     */
    public function validateImport() {}

    /**
     * @OA\Get(
     *      path="/api/v1/settings/data-transfer/imports/start/{importId}",
     *      operationId="startImport",
     *      tags={"DataTransfer"},
     *      summary="Start importing data",
     *      security={ {"sanctum_admin": {} }},
     *
     *      @OA\Parameter(
     *          name="importId",
     *          in="path",
     *          required=true,
     *          description="ID of the import",
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
     *              type="object",
     *
     *              @OA\Property(
     *                  property="stats",
     *                  type="object",
     *                  @OA\Property(
     *                      property="batches",
     *                      type="object",
     *                      @OA\Property(property="total", type="integer", example=1),
     *                      @OA\Property(property="completed", type="integer", example=0),
     *                      @OA\Property(property="remaining", type="integer", example=1)
     *                  ),
     *                  @OA\Property(property="progress", type="integer", example=0),
     *                  @OA\Property(
     *                      property="summary",
     *                      type="object",
     *                      @OA\Property(property="created", type="integer", example=0),
     *                      @OA\Property(property="updated", type="integer", example=0),
     *                      @OA\Property(property="deleted", type="integer", example=0)
     *                  )
     *              ),
     *              @OA\Property(
     *                  property="import",
     *                  type="object",
     *                  @OA\Property(property="id", type="integer", example=4),
     *                  @OA\Property(property="state", type="string", example="processing"),
     *                  @OA\Property(property="process_in_queue", type="integer", example=1),
     *                  @OA\Property(property="type", type="string", example="persons"),
     *                  @OA\Property(property="action", type="string", example="append"),
     *                  @OA\Property(property="validation_strategy", type="string", example="stop-on-errors"),
     *                  @OA\Property(property="allowed_errors", type="integer", example=10),
     *                  @OA\Property(property="processed_rows_count", type="integer", example=2),
     *                  @OA\Property(property="invalid_rows_count", type="integer", example=0),
     *                  @OA\Property(property="errors_count", type="integer", example=0),
     *                  @OA\Property(
     *                      property="errors",
     *                      type="array",
     *
     *                      @OA\Items(type="string", example="")
     *                  ),
     *
     *                  @OA\Property(property="field_separator", type="string", example=","),
     *                  @OA\Property(property="file_path", type="string", example="imports/1746077680-persons.csv"),
     *                  @OA\Property(property="error_file_path", type="string", nullable=true, example=null),
     *                  @OA\Property(
     *                      property="summary",
     *                      type="array",
     *
     *                      @OA\Items(type="string")
     *                  ),
     *
     *                  @OA\Property(property="started_at", type="string", format="date-time", example="2025-05-01T05:40:16.000000Z"),
     *                  @OA\Property(property="completed_at", type="string", format="date-time", nullable=true, example=null),
     *                  @OA\Property(property="created_at", type="string", format="date-time", example="2025-05-01T05:34:40.000000Z"),
     *                  @OA\Property(property="updated_at", type="string", format="date-time", example="2025-05-01T05:40:16.000000Z")
     *              )
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=404,
     *          description="Import record not found"
     *      )
     * )
     */
    public function start() {}

    /**
     * @OA\Get(
     *      path="/api/v1/settings/data-transfer/imports/stats/{importId}/{state}",
     *      operationId="getStats",
     *      tags={"DataTransfer"},
     *      summary="Get import stats",
     *      security={ {"sanctum_admin": {} }},
     *
     *      @OA\Parameter(
     *          name="importId",
     *          in="path",
     *          required=true,
     *          description="ID of the import",
     *
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *
     *      @OA\Parameter(
     *          name="state",
     *          in="path",
     *          required=false,
     *          description="State of the import (leave empty for 'processed').",
     *
     *          @OA\Schema(
     *              type="string",
     *              enum={
     *                  "pending",
     *                  "validated",
     *                  "processing",
     *                  "processed",
     *                  "linking",
     *                  "linked",
     *                  "indexing",
     *                  "indexed",
     *                  "completed"
     *              },
     *              default="processed"
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(
     *              type="object",
     *
     *              @OA\Property(
     *                  property="stats",
     *                  type="object",
     *                  @OA\Property(
     *                      property="batches",
     *                      type="object",
     *                      @OA\Property(property="total", type="integer", example=1),
     *                      @OA\Property(property="completed", type="integer", example=0),
     *                      @OA\Property(property="remaining", type="integer", example=1)
     *                  ),
     *                  @OA\Property(property="progress", type="integer", example=0),
     *                  @OA\Property(
     *                      property="summary",
     *                      type="object",
     *                      @OA\Property(property="created", type="integer", example=0),
     *                      @OA\Property(property="updated", type="integer", example=0),
     *                      @OA\Property(property="deleted", type="integer", example=0)
     *                  )
     *              ),
     *              @OA\Property(
     *                  property="import",
     *                  type="object",
     *                  @OA\Property(property="id", type="integer", example=9),
     *                  @OA\Property(property="state", type="string", example="processing"),
     *                  @OA\Property(property="process_in_queue", type="integer", example=1),
     *                  @OA\Property(property="type", type="string", example="products"),
     *                  @OA\Property(property="action", type="string", example="append"),
     *                  @OA\Property(property="validation_strategy", type="string", example="stop-on-errors"),
     *                  @OA\Property(property="allowed_errors", type="integer", example=10),
     *                  @OA\Property(property="processed_rows_count", type="integer", example=2),
     *                  @OA\Property(property="invalid_rows_count", type="integer", example=0),
     *                  @OA\Property(property="errors_count", type="integer", example=0),
     *                  @OA\Property(
     *                      property="errors",
     *                      type="array",
     *
     *                      @OA\Items(type="string", example="")
     *                  ),
     *
     *                  @OA\Property(property="field_separator", type="string", example=","),
     *                  @OA\Property(property="file_path", type="string", example="imports/1746079436-products.csv"),
     *                  @OA\Property(property="error_file_path", type="string", nullable=true, example=null),
     *                  @OA\Property(
     *                      property="summary",
     *                      type="array",
     *
     *                      @OA\Items(type="string")
     *                  ),
     *
     *                  @OA\Property(property="started_at", type="string", format="date-time", example="2025-05-01T06:04:09.000000Z"),
     *                  @OA\Property(property="completed_at", type="string", format="date-time", nullable=true, example=null),
     *                  @OA\Property(property="created_at", type="string", format="date-time", example="2025-05-01T06:00:52.000000Z"),
     *                  @OA\Property(property="updated_at", type="string", format="date-time", example="2025-05-01T06:04:09.000000Z")
     *              )
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=404,
     *          description="Import record not found"
     *      )
     * )
     */
    public function stats() {}

    /**
     * @OA\Get(
     *      path="/api/v1/settings/data-transfer/imports/download-error-report/{importId}",
     *      operationId="downloadErrorReport",
     *      tags={"DataTransfer"},
     *      summary="Download the error report CSV file for the import",
     *      security={ {"sanctum_admin": {} }},
     *
     *      @OA\Parameter(
     *          name="importId",
     *          in="path",
     *          required=true,
     *          description="ID of the import",
     *
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="CSV file download",
     *          content={
     *
     *              @OA\MediaType(
     *                  mediaType="text/csv",
     *
     *                  @OA\Schema(
     *                      type="string",
     *                      format="binary"
     *                  )
     *              )
     *          }
     *      ),
     *
     *      @OA\Response(
     *          response=404,
     *          description="Import not found or no error report available"
     *      )
     * )
     */
    public function downloadErrorReport() {}

    /**
     * @OA\Post(
     *      path="/api/v1/settings/data-transfer/imports/{id}",
     *      operationId="importUpdate",
     *      tags={"DataTransfer"},
     *      summary="Update existing Import",
     *      security={ {"sanctum_admin": {} }},
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="ID of import to update",
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
     *                  type="object",
     *                  required={"_method", "type", "action", "validation_strategy", "allowed_errors", "field_separator", "file"},
     *
     *                  @OA\Property(
     *                      property="_method",
     *                      description="HTTP method override",
     *                      type="string",
     *                      example="PUT"
     *                  ),
     *                  @OA\Property(
     *                      property="type",
     *                      description="Type of import data",
     *                      type="string",
     *                      enum={"persons", "products", "leads"},
     *                      example="persons"
     *                  ),
     *                  @OA\Property(
     *                      property="action",
     *                      description="Action to perform",
     *                      type="string",
     *                      enum={"create/update", "append", "delete"},
     *                      example="append"
     *                  ),
     *                  @OA\Property(
     *                      property="validation_strategy",
     *                      description="Validation strategy",
     *                      type="string",
     *                      enum={"stop-on-errors", "skip-erros"},
     *                      example="stop-on-errors"
     *                  ),
     *                  @OA\Property(
     *                      property="allowed_errors",
     *                      description="Number of allowed errors before stopping",
     *                      type="integer",
     *                      example=10
     *                  ),
     *                  @OA\Property(
     *                      property="field_separator",
     *                      description="Field separator character in file",
     *                      type="string",
     *                      example=","
     *                  ),
     *                  @OA\Property(
     *                      property="process_in_queue",
     *                      description="Whether to process in queue (optional)",
     *                      type="string",
     *                      example="on"
     *                  ),
     *                  @OA\Property(
     *                      property="file",
     *                      description="File to import",
     *                      type="string",
     *                      format="binary"
     *                  )
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
     *                  property="data",
     *                  ref="#/components/schemas/Import",
     *                  type="object"
     *              )
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=404,
     *          description="Import not found"
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation error"
     *      )
     * )
     */
    public function update() {}

    /**
     * @OA\Get(
     *      path="/api/v1/settings/data-transfer/imports/{id}",
     *      operationId="showImport",
     *      tags={"DataTransfer"},
     *      summary="Get details of a specific Import",
     *      security={ {"sanctum_admin": {} }},
     *
     *      @OA\Parameter(
     *          name="id",
     *          description="Import Id",
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
     *              @OA\Property(
     *                  property="data",
     *                  type="Object",
     *                  ref="#/components/schemas/Import"
     *              )
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=404,
     *          description="Import not found"
     *      )
     * )
     */
    public function show() {}

    /**
     * @OA\Delete(
     *      path="/api/v1/settings/data-transfer/imports/{id}",
     *      operationId="importDelete",
     *      tags={"DataTransfer"},
     *      summary="Delete one record of Data Transfer",
     *      security={ {"sanctum_admin": {} }},
     *
     *      @OA\Parameter(
     *          name="id",
     *          description="Import Id",
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
     *                  @OA\Items(ref="#/components/schemas/Import")
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
    public function destroy() {}
}
