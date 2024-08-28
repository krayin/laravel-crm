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
        protected LeadRepository $leadRepository
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
        $lead = $this->leadRepository->find(request('id'));

        return view('admin::quotes.create', compact('lead'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AttributeForm $request): RedirectResponse
    {
        Event::dispatch('quote.create.before');

        $quote = $this->quoteRepository->create($request->all());

        if (request('lead_id')) {
            $lead = $this->leadRepository->find(request('lead_id'));

            $lead->quotes()->attach($quote->id);
        }

        Event::dispatch('quote.create.after', $quote);

        session()->flash('success', trans('admin::app.quotes.index.create-success'));

        return redirect()->route('admin.quotes.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): View
    {
        $quote = $this->quoteRepository->findOrFail($id);

        return view('admin::quotes.edit', compact('quote'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AttributeForm $request, int $id): RedirectResponse
    {
        Event::dispatch('quote.update.before', $id);

        $quote = $this->quoteRepository->update($request->all(), $id);

        $quote->leads()->detach();

        if (request('lead_id')) {
            $lead = $this->leadRepository->find(request('lead_id'));

            $lead->quotes()->attach($quote->id);
        }

        Event::dispatch('quote.update.after', $quote);

        session()->flash('success', trans('admin::app.quotes.index.update-success'));

        return redirect()->route('admin.quotes.index');
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
}
