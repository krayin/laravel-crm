<?php

namespace Webkul\Admin\Http\Controllers\Lead;

use ArPHP\I18N\Arabic;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Mpdf\Mpdf;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Admin\Notifications\Common;
use Webkul\Lead\Repositories\LeadRepository;
use Webkul\Quote\Repositories\QuoteRepository;

class QuoteController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        protected LeadRepository $leadRepository,
        protected QuoteRepository $quoteRepository
    ) {}

    /**
     * Store a newly created resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function store($id)
    {
        Event::dispatch('leads.quote.create.before');

        $lead = $this->leadRepository->find($id);

        if (! $lead->quotes->contains(request('id'))) {
            $lead->quotes()->attach(request('id'));
        }

        Event::dispatch('leads.quote.create.after', $lead);

        return response()->json([
            'message' => trans('admin::app.leads.quote-create-success'),
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $leadId
     * @param  int  $tagId
     * @return Response
     */
    public function delete($leadId)
    {
        Event::dispatch('leads.quote.delete.before', $leadId);

        $lead = $this->leadRepository->find($leadId);

        $lead->quotes()->detach(request('quote_id'));

        Event::dispatch('leads.quote.delete.after', $lead);

        return response()->json([
            'message' => trans('admin::app.leads.view.quotes.destroy-success'),
        ], 200);
    }

    /**
     * Send quote PDF to lead person email.
     *
     * @param  int  $leadId
     * @param  int  $quoteId
     * @return Response
     */
    public function mail($quoteId)
    {
        $quote = $this->quoteRepository->findOrFail($quoteId);

        $lead = $quote->leads->first();

        if (! $quote) {
            return response()->json([
                'message' => trans('admin::app.leads.view.quotes.quote-not-found'),
            ], 404);
        }

        $to = data_get($lead->person?->emails, '0.value');

        if (! $to) {
            return response()->json([
                'message' => trans('admin::app.leads.view.quotes.person-email-unavailable'),
            ], 422);
        }

        try {
            $pdfContent = $this->renderQuotePdfContent($quote);

            Mail::send(new Common([
                'to' => [$to],
                'subject' => trans('admin::app.leads.view.quotes.mail-subject', ['subject' => $quote->subject]),
                'body' => trans('admin::app.leads.view.quotes.mail-body'),
                'attachments' => [[
                    'content' => $pdfContent,
                    'name' => 'Quote_'.$quote->id.'.pdf',
                    'mime' => 'application/pdf',
                ]],
            ]));

            return response()->json([
                'message' => trans('admin::app.mail.create-success'),
            ], 200);
        } catch (\Throwable $exception) {
            return response()->json([
                'message' => trans('admin::app.leads.view.quotes.mail-send-failed'),
            ], 500);
        }
    }

    /**
     * Render quote PDF and return raw content.
     */
    private function renderQuotePdfContent($quote): string
    {
        $html = view('admin::quotes.pdf', compact('quote'))->render();

        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');

        if (in_array($direction = app()->getLocale(), ['ar', 'he'])) {
            $mPDF = new Mpdf([
                'margin_left' => 0,
                'margin_right' => 0,
                'margin_top' => 0,
                'margin_bottom' => 0,
            ]);

            $mPDF->SetDirectionality($direction);

            $mPDF->SetDisplayMode('fullpage');

            $mPDF->WriteHTML($this->adjustArabicAndPersianContent($html));

            return $mPDF->Output('', 'S');
        }

        return Pdf::loadHTML($this->adjustArabicAndPersianContent($html))
            ->setPaper('A4', 'portrait')
            ->set_option('defaultFont', 'Courier')
            ->output();
    }

    /**
     * Adjust arabic and persian content.
     */
    private function adjustArabicAndPersianContent(string $html): string
    {
        $arabic = new Arabic;

        $parts = $arabic->arIdentify($html);

        for ($i = count($parts) - 1; $i >= 0; $i -= 2) {
            $utf8ar = $arabic->utf8Glyphs(substr($html, $parts[$i - 1], $parts[$i] - $parts[$i - 1]));
            $html = substr_replace($html, $utf8ar, $parts[$i - 1], $parts[$i] - $parts[$i - 1]);
        }

        return $html;
    }
}
