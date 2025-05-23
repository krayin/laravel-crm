<?php

namespace Webkul\RestApi\Http\Controllers\V1\Lead;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Event;
use Webkul\Lead\Repositories\LeadRepository;
use Webkul\RestApi\Http\Controllers\V1\Controller;

class QuoteController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(protected LeadRepository $leadRepository) {}

    /**
     * Remove the specified resource from storage.
     */
    public function delete(int $leadId): JsonResponse
    {
        Event::dispatch('leads.quote.delete.before', $leadId);

        $lead = $this->leadRepository->find($leadId);

        $lead->quotes()->detach(request('quote_id'));

        Event::dispatch('leads.quote.delete.after', $lead);

        return response()->json([
            'message' => trans('rest-api::app.leads.view.quotes.delete-success'),
        ], 200);
    }
}
