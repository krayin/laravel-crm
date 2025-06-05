<?php

namespace Webkul\RestApi\Docs\Controllers\Cofiguration;

class ConfigurationController
{
    /**
     * @OA\Post(
     *     path="/api/v1/configuration",
     *     operationId="storeConfiguration",
     *     tags={"Configuration"},
     *     summary="Create new Configuration",
     *     security={ {"sanctum_admin": {} }},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *
     *            @OA\Property(
     *                property="general",
     *                type="object",
     *                @OA\Property(
     *                   property="general",
     *                   type="object",
     *                   @OA\Property(
     *                       property="locale_settings",
     *                       type="object",
     *                       @OA\Property(
     *                           property="locale",
     *                           type="string",
     *                           description="Locale",
     *                           example="en"
     *                       )
     *                  )
     *               )
     *            )
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
     *                 ref="#/components/schemas/CoreConfig"
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
}
