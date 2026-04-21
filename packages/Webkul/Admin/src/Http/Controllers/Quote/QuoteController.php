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
use Webkul\Core\Traits\PDFHandler;
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

        $lookUpEntityData = $this->attributeRepository->getLookUpEntity('leads', $leadId);

        return view('admin::quotes.create', compact('lead', 'quote', 'leadProducts', 'lookUpEntityData'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AttributeForm $request): RedirectResponse
    {
        $this->additionalValidation();

        Event::dispatch('quote.create.before');

        $quote = $this->quoteRepository->create($request->all());

        $leadId = request('lead_id');

        if ($leadId) {
            $lead = $this->leadRepository->find($leadId);

            $lead->quotes()->attach($quote->id);
        }

        Event::dispatch('quote.create.after', $quote);

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

        $leadId = old('lead_id') ?? optional($quote->leads->first())->id;

        $linkedLead = $leadId ? $this->leadRepository->find($leadId) : null;

        $initialQuoteItems = $quote->items;

        if ($initialQuoteItems->isEmpty() && $linkedLead?->products?->isNotEmpty()) {
            $initialQuoteItems = collect($this->getLeadProductsForQuote($linkedLead));
        }

        $lookUpEntityData = $this->attributeRepository->getLookUpEntity('leads', $leadId);

        return view('admin::quotes.edit', compact('quote', 'linkedLead', 'initialQuoteItems', 'lookUpEntityData'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AttributeForm $request, int $id): RedirectResponse
    {
        $this->additionalValidation();

        Event::dispatch('quote.update.before', $id);

        $quote = $this->quoteRepository->update($request->all(), $id);

        $quote->leads()->detach();

        $leadId = request('lead_id');

        if ($leadId) {
            $lead = $this->leadRepository->find($leadId);

            $lead->quotes()->attach($quote->id);
        }

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
        $quotes = $this->quoteRepository
            ->pushCriteria(app(RequestCriteria::class))
            ->all();

        return QuoteResource::collection($quotes);
    }

    /**
     * Return products for the selected lead in quote payload format.
     */
    public function leadProducts(int $leadId): JsonResponse
    {
        $lead = $this->leadRepository->findOrFail($leadId);

        return response()->json([
            'data' => $this->getLeadProductsForQuote($lead),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $this->quoteRepository->findOrFail($id);

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
        $quotes = $this->quoteRepository->findWhereIn('id', $massDestroyRequest->input('indices'));

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

        return $this->downloadPDF(
            view('admin::quotes.pdf', compact('quote'))->render(),
            'Quote_'.$quote->subject.'_'.$quote->created_at->format('d-m-Y')
        );
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
