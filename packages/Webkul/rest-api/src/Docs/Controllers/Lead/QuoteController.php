<?php

namespace Webkul\RestApi\Docs\Controllers\Lead;

class QuoteController
{
    /**
     * @OA\Delete(
     *     path="/api/v1/leads/{id}/quotes/{quote_id}",
     *     operationId="deleteQuoteFromLead",
     *     tags={"Leads"},
     *     summary="Delete a Quote from a Lead",
     *     description="Remove a specific quote associated with a lead.",
     *     security={ {"sanctum_admin": {}} },
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Lead ID",
     *         required=true,
     *
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *
     *     @OA\Parameter(
     *         name="quote_id",
     *         in="path",
     *         description="Quote ID",
     *         required=true,
     *
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Quote deleted successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Quote deleted successfully.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Quote not found"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function destroy() {}
}
