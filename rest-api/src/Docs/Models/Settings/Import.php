<?php

namespace Webkul\RestApi\Docs\Models\Settings;

/**
 * @OA\Schema(
 *     title="Import",
 *     description="Import Model",
 * )
 */
class Import
{
    /**
     * @OA\Property(
     *     title="ID",
     *     description="ID",
     *     format="int64",
     *     example=1
     * )
     *
     * @var int
     */
    private $id;

    /**
     * @OA\Property(
     *     title="State",
     *     description="The current state of the import",
     *     example="pending"
     * )
     *
     * @var string
     */
    private $state;

    /**
     * @OA\Property(
     *     title="Process In Queue",
     *     description="Whether the import is processed in queue (1 = yes, 0 = no)",
     *     example=1
     * )
     *
     * @var bool
     */
    private $process_in_queue;

    /**
     * @OA\Property(
     *     title="Type",
     *     description="Type of the import",
     *     example="products"
     * )
     *
     * @var string
     */
    private $type;

    /**
     * @OA\Property(
     *     title="Action",
     *     description="Action being performed",
     *     example="create"
     * )
     *
     * @var string
     */
    private $action;

    /**
     * @OA\Property(
     *     title="Validation Strategy",
     *     description="Validation strategy used",
     *     example="strict"
     * )
     *
     * @var string
     */
    private $validation_strategy;

    /**
     * @OA\Property(
     *     title="Allowed Errors",
     *     description="Number of allowed errors before failing",
     *     example=5
     * )
     *
     * @var int
     */
    private $allowed_errors;

    /**
     * @OA\Property(
     *     title="Processed Rows Count",
     *     description="Number of processed rows",
     *     example=120
     * )
     *
     * @var int
     */
    private $processed_rows_count;

    /**
     * @OA\Property(
     *     title="Invalid Rows Count",
     *     description="Number of invalid rows",
     *     example=3
     * )
     *
     * @var int
     */
    private $invalid_rows_count;

    /**
     * @OA\Property(
     *     title="Errors Count",
     *     description="Total number of errors",
     *     example=8
     * )
     *
     * @var int
     */
    private $errors_count;

    /**
     * @OA\Property(
     *     title="Errors",
     *     description="List of errors",
     *     type="array",
     *
     *     @OA\Items(type="string"),
     * )
     *
     * @var array
     */
    private $errors;

    /**
     * @OA\Property(
     *     title="Field Separator",
     *     description="The separator used in file fields",
     *     example=","
     * )
     *
     * @var string
     */
    private $field_separator;

    /**
     * @OA\Property(
     *     title="File Path",
     *     description="Path to the imported file",
     *     example="imports/files/products.csv"
     * )
     *
     * @var string
     */
    private $file_path;

    /**
     * @OA\Property(
     *     title="Error File Path",
     *     description="Path to the error file",
     *     example="imports/files/errors/products-errors.csv"
     * )
     *
     * @var string
     */
    private $error_file_path;

    /**
     * @OA\Property(
     *     title="Summary",
     *     description="Summary of the import",
     *     type="object",
     * )
     *
     * @var array
     */
    private $summary;

    /**
     * @OA\Property(
     *     title="Started At",
     *     description="When the import started",
     *     example="2024-05-01 10:00:00",
     *     format="datetime",
     *     type="string"
     * )
     *
     * @var \DateTime
     */
    private $started_at;

    /**
     * @OA\Property(
     *     title="Completed At",
     *     description="When the import completed",
     *     example="2024-05-01 11:00:00",
     *     format="datetime",
     *     type="string"
     * )
     *
     * @var \DateTime
     */
    private $completed_at;

    /**
     * @OA\Property(
     *     title="Created at",
     *     description="Created at timestamp",
     *     example="2024-04-30 09:45:00",
     *     format="datetime",
     *     type="string"
     * )
     *
     * @var \DateTime
     */
    private $created_at;

    /**
     * @OA\Property(
     *     title="Updated at",
     *     description="Updated at timestamp",
     *     example="2024-04-30 10:00:00",
     *     format="datetime",
     *     type="string"
     * )
     *
     * @var \DateTime
     */
    private $updated_at;
}
