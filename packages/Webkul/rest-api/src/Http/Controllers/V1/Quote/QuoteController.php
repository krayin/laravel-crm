<?php

namespace Webkul\RestApi\Http\Controllers\V1\Quote;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Event;
use Prettus\Repository\Criteria\RequestCriteria;
use Webkul\Admin\Http\Requests\AttributeForm;
use Webkul\Lead\Repositories\LeadRepository;
use Webkul\Quote\Repositories\QuoteRepository;
use Webkul\RestApi\Http\Controllers\V1\Controller;
use Webkul\RestApi\Http\Request\MassDestroyRequest;
use Webkul\RestApi\Http\Resources\V1\Quote\QuoteResource;

class QuoteController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        protected QuoteRepository $quoteRepository,
        protected LeadRepository $leadRepository
    ) {
        $this->addEntityTypeInRequest('quotes');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $quotes = $this->allResources($this->quoteRepository);

        return QuoteResource::collection($quotes);
    }

    /**
     * Display the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        $quote = $this->quoteRepository->find($id);

        return new QuoteResource($quote);
    }

    /**
     * Store a newly created quote in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(AttributeForm $request)
    {
        Event::dispatch('quote.create.before');

        $quote = $this->quoteRepository->create($request->all());

        if ($leadId = request()->input('lead_id')) {

            $lead = $this->leadRepository->find($leadId);

            $lead->quotes()->attach($quote->id);
        }

        Event::dispatch('quote.create.after', $quote);

        return new JsonResource([
            'data'    => new QuoteResource($quote),
            'message' => trans('rest-api::app.quotes.create-success'),
        ]);
    }

    /**
     * Update the specified quote in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AttributeForm $request, $id)
    {
        Event::dispatch('quote.update.before', $id);

        $quote = $this->quoteRepository->update($request->all(), $id);

        $quote->leads()->detach();

        if ($leadId = request()->input('lead_id')) {
            $lead = $this->leadRepository->find($leadId);

            $lead->quotes()->attach($quote->id);
        }

        Event::dispatch('quote.update.after', $quote);

        return new JsonResource([
            'data'    => new QuoteResource($quote),
            'message' => trans('rest-api::app.quotes.update-success'),
        ]);
    }

    /**
     * Remove the specified qoute from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->quoteRepository->findOrFail($id);

        try {
            Event::dispatch('quote.delete.before', $id);

            $this->quoteRepository->delete($id);

            Event::dispatch('quote.delete.after', $id);

            return new JsonResource([
                'message' => trans('rest-api::app.quotes.delete-success'),
            ]);
        } catch (\Exception $exception) {
            return new JsonResource([
                'message' => trans('rest-api::app.quotes.delete-failed'),
            ], 500);
        }
    }

    /**
     * Search the quotes.
     */
    public function search(): AnonymousResourceCollection
    {
        $quotes = $this->quoteRepository
            ->pushCriteria(app(RequestCriteria::class))
            ->limit(request()->input('limit', 10))
            ->all();

        return QuoteResource::collection($quotes);
    }

    /**
     * Mass delete the specified qoutes.
     *
     * @return \Illuminate\Http\Response
     */
    public function massDestroy(MassDestroyRequest $massDestroyRequest)
    {
        $quoteIds = $massDestroyRequest->input('indices', []);

        foreach ($quoteIds as $quoteId) {
            $quote = $this->quoteRepository->find($quoteId);

            if (! $quote) {
                continue;
            }

            Event::dispatch('quote.delete.before', $quoteId);

            $quote->delete($quoteId);

            Event::dispatch('quote.delete.after', $quoteId);
        }

        return new JsonResource([
            'message' => trans('rest-api::app.quotes.delete-success'),
        ]);
    }
}
