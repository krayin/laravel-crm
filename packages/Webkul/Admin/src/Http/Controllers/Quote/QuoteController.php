<?php

namespace Webkul\Admin\Http\Controllers\Quote;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Event;
use Illuminate\View\View;
use Prettus\Repository\Criteria\RequestCriteria;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Webkul\Admin\DataGrids\Quote\QuoteDataGrid;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Admin\Http\Requests\AttributeForm;
use Webkul\Admin\Http\Requests\MassDestroyRequest;
use Webkul\Admin\Http\Resources\QuoteResource;
use Webkul\Attribute\Repositories\AttributeRepository;
use Webkul\Contact\Repositories\PersonRepository;
use Webkul\Core\Traits\PDFHandler;
use Webkul\Lead\Contracts\Lead;
use Webkul\Lead\Repositories\LeadRepository;
use Webkul\Quote\Repositories\QuoteRepository;

class QuoteController extends Controller
{
    use PDFHandler;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        protected QuoteRepository $quoteRepository,
        protected LeadRepository $leadRepository,
        protected AttributeRepository $attributeRepository
    ) {
        request()->request->add(['entity_type' => 'quotes']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View|JsonResponse
    {
        if (request()->ajax()) {
            return datagrid(QuoteDataGrid::class)->process();
        }

        return view('admin::quotes.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $leadId = request('lead_id');

        $lead = $leadId ? $this->leadRepository->find($leadId) : null;

        /**
         * The form is filled from the lead — its contact, owner, billing address and product
         * pricing — so the lead is held to the same check as `leadProducts()`.
         */
        if ($lead) {
            $this->preventUnauthorizedAccess($lead->user_id);
        }

        $quote = $this->quoteRepository->getModel();

        if ($lead) {
            $quote->fill([
                'person_id' => $lead->person_id,
                'user_id' => $lead->user_id,
                'billing_address' => $lead->person->organization?->address,
                'expired_at' => $lead->expected_close_date ?? now()->toDateString(),
            ]);
        }

        $leadProducts = $this->getLeadProductsForQuote($lead);

        $lookUpEntityData = $this->attributeRepository->getLookUpEntity('leads', $leadId)?->only(['id', 'name']);

        return view('admin::quotes.create', compact('lead', 'quote', 'leadProducts', 'lookUpEntityData'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AttributeForm $request): RedirectResponse|JsonResponse
    {
        $lead = $this->requestedLead();

        $this->preventUnauthorizedQuotePerson([$lead?->person_id]);

        if (! request()->has('quick_add')) {
            $this->additionalValidation();
        }

        $this->syncShippingAddressWithBilling($request);

        Event::dispatch('quote.create.before');

        $quote = $this->quoteRepository->create($request->all());

        $lead?->quotes()->attach($quote->id);

        $leadId = $lead?->id;

        Event::dispatch('quote.create.after', $quote);

        if (request()->ajax()) {
            return response()->json([
                'data' => $quote,
                'message' => trans('admin::app.quotes.index.create-success'),
            ]);
        }

        session()->flash('success', trans('admin::app.quotes.index.create-success'));

        return request()->query('from') === 'lead' && $leadId
            ? redirect()->route('admin.leads.view', ['id' => $leadId, 'from' => 'quotes'])
            : redirect()->route('admin.quotes.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): View
    {
        $quote = $this->quoteRepository->findOrFail($id);

        $this->preventUnauthorizedAccess($quote->user_id);

        $currentLeadId = optional($quote->leads->first())->id;

        $leadId = old('lead_id') ?? $currentLeadId;

        $linkedLead = $leadId ? $this->leadRepository->find($leadId) : null;

        /**
         * `old('lead_id')` is the caller's own previous submission, flashed back when validation
         * failed, so it may name any lead. Only a lead in the caller's scope replaces the current
         * one, and only such a lead fills the items with its product pricing.
         */
        if (
            $linkedLead
            && $linkedLead->id != $currentLeadId
            && ! $this->isLeadInScope($linkedLead)
        ) {
            $leadId = $currentLeadId;

            $linkedLead = $leadId ? $this->leadRepository->find($leadId) : null;
        }

        $initialQuoteItems = $quote->items;

        if (
            $initialQuoteItems->isEmpty()
            && $linkedLead
            && $this->isLeadInScope($linkedLead)
            && $linkedLead->products?->isNotEmpty()
        ) {
            $initialQuoteItems = collect($this->getLeadProductsForQuote($linkedLead));
        }

        $lookUpEntityData = $this->attributeRepository->getLookUpEntity('leads', $leadId)?->only(['id', 'name']);

        return view('admin::quotes.edit', compact('quote', 'linkedLead', 'initialQuoteItems', 'lookUpEntityData'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AttributeForm $request, int $id): RedirectResponse
    {
        $quote = $this->quoteRepository->findOrFail($id);

        $this->preventUnauthorizedAccess($quote->user_id);

        $lead = $this->requestedLead($quote->leads->pluck('id')->all());

        $this->preventUnauthorizedQuotePerson([$quote->person_id, $lead?->person_id]);

        $this->additionalValidation();

        $this->syncShippingAddressWithBilling($request);

        Event::dispatch('quote.update.before', $id);

        $quote = $this->quoteRepository->update($request->all(), $id);

        $quote->leads()->detach();

        $lead?->quotes()->attach($quote->id);

        $leadId = $lead?->id;

        Event::dispatch('quote.update.after', $quote);

        session()->flash('success', trans('admin::app.quotes.index.update-success'));

        return request()->query('from') === 'lead' && $leadId
            ? redirect()->route('admin.leads.view', ['id' => $leadId, 'from' => 'quotes'])
            : redirect()->route('admin.quotes.index');
    }

    /**
     * Search the quotes.
     */
    public function search(): AnonymousResourceCollection
    {
        $quoteRepository = $this->quoteRepository
            ->pushCriteria(app(RequestCriteria::class));

        /**
         * Scope the result to the acting user's data scope, the same way the listing and the data
         * grid already are. Without it this endpoint returned every quote — subject, description,
         * billing and shipping address, totals, and the linked person's contact details — to any
         * user with quote access, regardless of who owned the record.
         */
        if ($userIds = bouncer()->getAuthorizedUserIds()) {
            $quotes = $quoteRepository->findWhereIn('user_id', $userIds);
        } else {
            $quotes = $quoteRepository->all();
        }

        return QuoteResource::collection($quotes);
    }

    /**
     * The lead named by the request's `lead_id`, or `null` when none is named.
     *
     * Attaching a quote files it under the lead, so the lead is held to the same check as opening
     * it. A lead already linked to the quote being updated is exempt: keeping a link grants nothing
     * new, and refusing it would silently drop the link once the lead moved out of the user's scope.
     *
     * @return Lead|null
     */
    protected function requestedLead(array $linkedLeadIds = [])
    {
        if (! $leadId = request('lead_id')) {
            return null;
        }

        $lead = $this->leadRepository->findOrFail($leadId);

        if (! in_array($lead->id, $linkedLeadIds)) {
            $this->preventUnauthorizedAccess($lead->user_id);
        }

        return $lead;
    }

    /**
     * Deny filing a quote under a contact outside the acting user's data scope.
     *
     * The quote resource returns its person in full, so a person the user could not otherwise open
     * would be read back through their own quote. The person of the quote's lead, and the one the
     * quote already has, are exempt: the form fills the first in from the lead, and keeping the
     * second grants nothing new.
     */
    protected function preventUnauthorizedQuotePerson(array $allowedPersonIds = []): void
    {
        $personId = request('person_id');

        if (
            ! $personId
            || in_array($personId, array_filter($allowedPersonIds))
        ) {
            return;
        }

        $this->preventUnauthorizedAccess(app(PersonRepository::class)->findOrFail($personId)->user_id);
    }

    /**
     * Whether the acting user may open the given lead.
     */
    protected function isLeadInScope($lead): bool
    {
        $userIds = bouncer()->getAuthorizedUserIds();

        return $userIds === null || in_array($lead->user_id, $userIds);
    }

    /**
     * Return products for the selected lead in quote payload format.
     */
    public function leadProducts(int $leadId): JsonResponse
    {
        $lead = $this->leadRepository->findOrFail($leadId);

        /**
         * The products on a lead carry that deal's pricing — product, quantity, unit price and line
         * total — so the endpoint is bound to the lead's owner rather than to quote access alone.
         */
        $this->preventUnauthorizedAccess($lead->user_id);

        return response()->json([
            'data' => $this->getLeadProductsForQuote($lead),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $this->preventUnauthorizedAccess($this->quoteRepository->findOrFail($id)->user_id);

        try {
            Event::dispatch('quote.delete.before', $id);

            $this->quoteRepository->delete($id);

            Event::dispatch('quote.delete.after', $id);

            return response()->json([
                'message' => trans('admin::app.quotes.index.delete-success'),
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => trans('admin::app.quotes.index.delete-failed'),
            ], 400);
        }
    }

    /**
     * Mass Delete the specified resources.
     */
    public function massDestroy(MassDestroyRequest $massDestroyRequest): JsonResponse
    {
        $quotes = $this->filterAuthorizedRecords(
            $this->quoteRepository->findWhereIn('id', $massDestroyRequest->input('indices'))
        );

        try {
            foreach ($quotes as $quotes) {
                Event::dispatch('quote.delete.before', $quotes->id);

                $this->quoteRepository->delete($quotes->id);

                Event::dispatch('quote.delete.after', $quotes->id);
            }

            return response()->json([
                'message' => trans('admin::app.quotes.index.delete-success'),
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => trans('admin::app.quotes.index.delete-failed'),
            ], 400);
        }
    }

    /**
     * Print and download the for the specified resource.
     */
    public function print($id): Response|StreamedResponse
    {
        $quote = $this->quoteRepository->findOrFail($id);

        $this->preventUnauthorizedAccess($quote->user_id);

        return $this->downloadPDF(
            view('admin::quotes.pdf', compact('quote'))->render(),
            'Quote_'.$quote->subject.'_'.$quote->created_at->format('d-m-Y')
        );
    }

    /**
     * Mirror the billing address into the shipping address when "same as billing" is enabled.
     */
    private function syncShippingAddressWithBilling(AttributeForm $request): void
    {
        if ($request->boolean('shipping_address_same_as_billing')) {
            $request->merge([
                'shipping_address' => $request->input('billing_address'),
            ]);
        }
    }

    /**
     * Additional validation for quote product items.
     */
    private function additionalValidation(): void
    {
        $this->validate(request(), [
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.total' => 'required|numeric|min:0',
            'items.*.discount_amount' => 'required|numeric|min:0',
            'items.*.tax_amount' => 'required|numeric|min:0',
            'items.*.final_total' => 'required|numeric|min:0',
        ]);
    }

    /**
     * Map linked lead products to quote item payload format.
     */
    private function getLeadProductsForQuote($lead): array
    {
        if (! $lead?->products?->isNotEmpty()) {
            return [];
        }

        return $lead->products
            ->map(function ($product) {
                $quantity = (float) ($product->quantity ?: 1);
                $price = (float) ($product->price ?: 0);

                return [
                    'id' => null,
                    'product_id' => $product->product_id,
                    'name' => $product->name,
                    'quantity' => $quantity,
                    'total' => $price * $quantity,
                    'price' => $price,
                    'discount_amount' => 0,
                    'tax_amount' => 0,
                ];
            })
            ->values()
            ->toArray();
    }
}
