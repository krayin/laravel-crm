<?php

namespace Webkul\RestApi\Docs\Controllers\Product;

class ProductController
{
    /**
     * @OA\Get(
     *      path="/api/v1/products",
     *      operationId="productList",
     *      tags={"Products"},
     *      summary="Get list of products",
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
     *                  @OA\Items(ref="#/components/schemas/Product")
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
     *      path="/api/v1/products/search",
     *      operationId="searchProducts",
     *      tags={"Products"},
     *      summary="search the products",
     *      description="search the products heres the bagisto is the search keyword",
     *      security={ {"sanctum_admin": {} }},
     *
     *      @OA\Parameter(
     *          name="search",
     *          in="query",
     *          required=false,
     *
     *          @OA\Schema(
     *              type="string",
     *              example="name:bagisto;sku:bagisto;description:bagisto"
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
     *              example="name:like;sku:like;description:like;"
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
     *                  ref="#/components/schemas/Product"
     *              )
     *          )
     *      )
     * )
     */
    public function search() {}

    /**
     * @OA\Post(
     *     path="/api/v1/products/{id}/inventories/{warehouseId}",
     *     tags={"Products"},
     *     summary="Store Inventory for a Product",
     *     description="Store inventory data for a specific product in a specified warehouse. If the warehouse ID is not provided, the inventory will be stored in the default warehouse.",
     *     operationId="storeProductInventories",
     *     security={ {"sanctum_admin": {} }},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the product",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Parameter(
     *         name="warehouseId",
     *         in="path",
     *         description="ID of the warehouse (optional)",
     *         required=false,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(
     *                 property="inventories",
     *                 type="object",
     *                 @OA\Property(
     *                     property="inventory_0",
     *                     type="object",
     *                     @OA\Property(
     *                         property="warehouse_location_id",
     *                         type="integer",
     *                         example=1,
     *                         description="ID of the warehouse location"
     *                     ),
     *                     @OA\Property(
     *                         property="warehouse_id",
     *                         type="integer",
     *                         example=1,
     *                         description="ID of the warehouse"
     *                     ),
     *                     @OA\Property(
     *                         property="in_stock",
     *                         type="integer",
     *                         example=1,
     *                         description="Quantity of the product in stock"
     *                     ),
     *                     @OA\Property(
     *                         property="allocated",
     *                         type="integer",
     *                         example=11,
     *                         description="Quantity of the product allocated"
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Inventory successfully stored",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 ref="#/components/schemas/Product"
     *             ),
     *             @OA\Property(property="message", type="string", example="Inventory stored successfully.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product or warehouse not found"
     *     )
     * )
     */
    public function storeInventories() {}

    /**
     * @OA\Get(
     *     path="/api/v1/products/{id}/warehouses",
     *     tags={"Products"},
     *     summary="Get Warehouses for a Specific Product",
     *     description="Retrieve a list of warehouses associated with the specified product ID.",
     *     operationId="getProductWarehouses",
     *     security={ {"sanctum_admin": {} }},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the product",
     *         required=true,
     *
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="A list of warehouses",
     *
     *         @OA\JsonContent(
     *
     *              @OA\Property(
     *                  property="data",
     *                  type="object",
     *                  ref="#/components/schemas/Product"
     *              )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Product not found"
     *     )
     * )
     */
    public function warehouses() {}

    /**
     * @OA\Get(
     *      path="/api/v1/products/{id}",
     *      operationId="productShow",
     *      tags={"Products"},
     *      summary="Get product details",
     *      security={ {"sanctum_admin": {} }},
     *
     *      @OA\Parameter(
     *          name="id",
     *          description="Product ID",
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
     *                  type="object",
     *                  ref="#/components/schemas/Product"
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
    public function show() {}

    /**
     * @OA\Post(
     *      path="/api/v1/products",
     *      operationId="productCreate",
     *      tags={"Products"},
     *      summary="Create new product",
     *      security={ {"sanctum_admin": {} }},
     *
     *     @OA\RequestBody(
     *         required=true,
     *         description="Product details",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(
     *                 property="name",
     *                 type="string",
     *                 description="Product name",
     *                 example="lorem"
     *             ),
     *             @OA\Property(
     *                 property="description",
     *                 type="string",
     *                 description="Product Description",
     *                 example="lorem ipsum"
     *             ),
     *             @OA\Property(
     *                 property="sku",
     *                 type="string",
     *                 description="Product Sku",
     *                 example="lorem-ipsum"
     *             ),
     *             @OA\Property(
     *                 property="quantity",
     *                 type="string",
     *                 description="Product Quantity",
     *                 example="1"
     *             ),
     *             @OA\Property(
     *                 property="price",
     *                 type="string",
     *                 description="Product Price",
     *                 example="25"
     *             ),
     *             @OA\Property(
     *                 property="entity_type",
     *                 type="string",
     *                 description="Product Type",
     *                 example="products"
     *             ),
     *         )
     *     ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(
     *
     *               @OA\Property(
     *                  property="data",
     *                  type="Object",
     *                  ref="#/components/schemas/Product"
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
    public function store() {}

    /**
     * @OA\Put(
     *      path="/api/v1/products/{id}",
     *      operationId="productUpdate",
     *      tags={"Products"},
     *      summary="Update existing product",
     *      security={ {"sanctum_admin": {} }},
     *
     *      @OA\Parameter(
     *          name="id",
     *          description="Product ID",
     *          required=true,
     *          in="path",
     *
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *         description="Product details",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(
     *                 property="name",
     *                 type="string",
     *                 description="Product name",
     *                 example="lorem"
     *             ),
     *             @OA\Property(
     *                 property="description",
     *                 type="string",
     *                 description="Product Description",
     *                 example="lorem ipsum"
     *             ),
     *             @OA\Property(
     *                 property="sku",
     *                 type="string",
     *                 description="Product Sku",
     *                 example="lorem-ipsum"
     *             ),
     *             @OA\Property(
     *                 property="quantity",
     *                 type="string",
     *                 description="Product Quantity",
     *                 example="1"
     *             ),
     *             @OA\Property(
     *                 property="price",
     *                 type="string",
     *                 description="Product Price",
     *                 example="25"
     *             ),
     *             @OA\Property(
     *                 property="entity_type",
     *                 type="string",
     *                 description="Product Type",
     *                 example="products"
     *             ),
     *         )
     *     ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(
     *
     *               @OA\Property(
     *                  property="data",
     *                  type="Object",
     *                  ref="#/components/schemas/Product"
     *              )
     *          )
     *      ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *    )
     * )
     */
    public function update() {}

    /**
     * @OA\Delete(
     *      path="/api/v1/products/{id}",
     *      operationId="productDelete",
     *      tags={"Products"},
     *      summary="Delete existing product",
     *      security={ {"sanctum_admin": {} }},
     *
     *      @OA\Parameter(
     *          name="id",
     *          description="Product ID",
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
     *                  type="Object",
     *                  ref="#/components/schemas/Product"
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
    public function destroy() {}

    /**
     * @OA\Post(
     *      path="/api/v1/products/mass-destroy",
     *      operationId="productMassDestroy",
     *      tags={"Products"},
     *      summary="Delete existing products",
     *      security={ {"sanctum_admin": {} }},
     *
     *     @OA\RequestBody(
     *         required=true,
     *         description="Product details",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(
     *                 property="indices",
     *                 type="array",
     *                 description="Product IDs",
     *
     *                 @OA\Items(
     *                     type="integer",
     *                     example="1"
     *                 )
     *             ),
     *         )
     *     ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(
     *
     *               @OA\Property(
     *                  property="data",
     *                  type="Object",
     *                  ref="#/components/schemas/Product"
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
    public function massDestroy() {}
}
