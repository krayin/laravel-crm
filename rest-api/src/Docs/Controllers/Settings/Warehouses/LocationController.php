<?php

namespace Webkul\RestApi\Docs\Controllers\Settings\Warehouses;

class LocationController
{
    /**
     * @OA\Get(
     *      path="/api/v1/settings/locations/search",
     *      operationId="searhLocations",
     *      tags={"Warehouse"},
     *      summary="search the locations",
     *      description="search the locations",
     *      security={ {"sanctum_admin": {} }},
     *
     *      @OA\Parameter(
     *          name="query",
     *          in="query",
     *          required=false,
     *
     *          @OA\Schema(
     *              type="string",
     *              example="Los Angeles"
     *          )
     *      ),
     *
     *      @OA\Parameter(
     *          name="search",
     *          in="query",
     *          required=false,
     *
     *          @OA\Schema(
     *              type="string",
     *              example="warehouse_id:1;name:Los Angeles"
     *          )
     *      ),
     *
     *      @OA\Parameter(
     *          name="searchFields",
     *          in="query",
     *          required=false,
     *
     *          @OA\Schema(
     *              type="string",
     *              example="warehouse_id:=;name:like"
     *          )
     *      ),
     *
     *      @OA\Parameter(
     *          name="searchJoin",
     *          in="query",
     *          required=false,
     *
     *          @OA\Schema(
     *              type="string",
     *              example="and"
     *          )
     *      ),
     *
     *      @OA\Parameter(
     *          name="limit",
     *          in="query",
     *          required=false,
     *
     *          @OA\Schema(
     *              type="string",
     *              example=10
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
     *                  ref="#/components/schemas/WarehouseLocation"
     *              )
     *          )
     *      )
     * )
     */
    public function search() {}

    /**
     * @OA\Post(
     *      path="/api/v1/settings/locations",
     *      operationId="storeLocations",
     *      tags={"Warehouse"},
     *      summary="Store the locations",
     *      description="Store the locations",
     *      security={ {"sanctum_admin": {} }},

     *
     *      @OA\RequestBody(
     *          required=true,
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(
     *                  property="warehouse_id",
     *                  type="string",
     *                  description="The ID of the warehouse",
     *                  example="1"
     *              ),
     *              @OA\Property(
     *                  property="name",
     *                  type="string",
     *                  description="The name of the location",
     *                  example="San Francisco"
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
     *                  type="object",
     *                  ref="#/components/schemas/WarehouseLocation"
     *              )
     *          )
     *      )
     * )
     */
    public function store() {}

    /**
     * @OA\Put(
     *      path="/api/v1/settings/locations/{id}",
     *      operationId="updateLocation",
     *      tags={"Warehouse"},
     *      summary="Update the location",
     *      description="Update the location by ID",
     *      security={ {"sanctum_admin": {} }},
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="ID of the location to update",
     *
     *          @OA\Schema(
     *              type="integer",
     *              example=1
     *          )
     *      ),
     *
     *      @OA\RequestBody(
     *          required=true,
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(
     *                  property="warehouse_id",
     *                  type="string",
     *                  description="The ID of the warehouse",
     *                  example="1"
     *              ),
     *              @OA\Property(
     *                  property="name",
     *                  type="string",
     *                  description="The name of the location",
     *                  example="San Francisco"
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
     *                  type="object",
     *                  ref="#/components/schemas/WarehouseLocation"
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
    public function update() {}

    /**
     * @OA\Delete(
     *      path="/api/v1/settings/locations/{id}",
     *      operationId="deleteLocation",
     *      tags={"Warehouse"},
     *      summary="Delete a location",
     *      description="Delete a location by ID",
     *      security={ {"sanctum_admin": {} }},
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="ID of the location to delete",
     *
     *          @OA\Schema(
     *              type="integer",
     *              example=1
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
     *                  example="Location deleted successfully."
     *              )
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=404,
     *          description="Location not found",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(
     *                  property="error",
     *                  type="string",
     *                  example="Location not found."
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
