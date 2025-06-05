<?php

namespace Webkul\RestApi\Docs\Controllers\Authentication;

class AccountController
{
    /**
     * @OA\Get(
     *      path="/api/v1/get",
     *      operationId="getAdminUser",
     *      tags={"Authentication"},
     *      summary="Get logged in admin user's details",
     *      description="Get logged in admin user's details",
     *      security={ {"sanctum_admin": {} }},
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(
     *
     *				@OA\Property(
     * 					property="data",
     * 					type="object",
     * 					ref="#/components/schemas/User"
     *				)
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     * )
     */
    public function get() {}

    /**
     * @OA\Post(
     *      path="/api/v1/update",
     *      operationId="updateAdminUser",
     *      tags={"Authentication"},
     *      summary="Update admin user's profile",
     *      description="Update admin user's profile",
     *      security={ {"sanctum_admin": {} }},
     *
     *      @OA\RequestBody(
     *
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *
     *              @OA\Schema(
     *
     *                  @OA\Property(
     *                      property="_method",
     *                      type="string",
     *                      example="PUT"
     *                  ),
     *                  @OA\Property(
     *                      property="name",
     *                      type="string",
     *                      example="Kim Thomson"
     *                  ),
     *                  @OA\Property(
     *                      property="email",
     *                      type="string",
     *                      example="example@example.com"
     *                  ),
     *                  @OA\Property(
     *                      property="image",
     *                      type="file",
     *                  ),
     *                  @OA\Property(
     *                      property="password",
     *                      type="string",
     *                      example="admin123"
     *                  ),
     *                  @OA\Property(
     *                      property="password_confirmation",
     *                      type="string",
     *                      example="admin123"
     *                  ),
     *                  @OA\Property(
     *                      property="current_password",
     *                      type="string",
     *                      example="admin123"
     *                  ),
     *                  required={"name", "current_password"}
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
     *              @OA\Property(property="message", type="string", example="Account updated successfully."),
     *              @OA\Property(property="data", type="object", ref="#/components/schemas/User")
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Error: Unprocessable Content",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Examples(example="result", value={"message":"The name field is required. (and 1 more error)"}, summary="An result object."),
     *          )
     *      )
     * )
     */
    public function update() {}

    /**
     * @OA\Delete(
     *      path="/api/v1/logout",
     *      operationId="logoutAdminUser",
     *      tags={"Authentication"},
     *      summary="Logout admin user",
     *      description="Logout admin user",
     *      security={ {"sanctum_admin": {} }},
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="message", type="string", example="Logged out successfully.")
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      )
     * )
     */
    public function logout() {}
}
