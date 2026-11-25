<?php

namespace Webkul\Admin\Http\Controllers\Lead;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Firma\Models\SignatureRequest;
use Webkul\Firma\Services\FirmaApiException;
use Webkul\Firma\Services\FirmaSettings;
use Webkul\Firma\Services\SignatureRequestSender;
use Webkul\Lead\Repositories\LeadRepository;

class FirmaController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        protected LeadRepository $leadRepository,
        protected FirmaSettings $firmaSettings,
        protected SignatureRequestSender $signatureRequestSender,
    ) {}

    /**
     * The lead's signature requests, newest first.
     */
    public function index(int $id): JsonResponse
    {
        $lead = $this->authorizedLead($id);

        return new JsonResponse([
            'data' => SignatureRequest::query()
                ->where('lead_id', $lead->id)
                ->latest('id')
                ->get()
                ->map(fn (SignatureRequest $request) => $this->present($request)),
        ]);
    }

    /**
     * Every Firma template, for the send dialog. Templates with more than one role are listed
     * but cannot be chosen.
     */
    public function templates(): JsonResponse
    {
        try {
            $listing = $this->firmaSettings->client()->listTemplates();
        } catch (FirmaApiException $exception) {
            return new JsonResponse(['message' => $exception->getMessage()], 422);
        }

        return new JsonResponse([
            'data' => array_map(fn (array $template) => [
                'id' => $template['id'],
                'name' => $template['name'],
                'supported' => $template['supported'],
            ], $listing['templates']),
            'complete' => $listing['complete'],
        ]);
    }

    /**
     * Send a template or one of the lead's quotes for signature.
     */
    public function store(Request $request, int $id): JsonResponse
    {
        $lead = $this->authorizedLead($id);

        $data = $request->validate([
            'idempotency_key' => 'required|uuid',
            'source' => 'required|in:template,quote',
            'template_id' => 'required_if:source,template|nullable|string|max:64',
            'quote_id' => 'required_if:source,quote|nullable|integer',
            'name' => 'required|string|max:255',
            'recipient_name' => 'required|string|max:200',
            'recipient_email' => 'required|email|max:255',
            'expiration_hours' => 'nullable|integer|min:1|max:8760',
        ]);

        if ($data['source'] === 'quote') {
            $quote = $lead->quotes()->where('quotes.id', $data['quote_id'])->first();

            if ($quote) {
                $this->preventUnauthorizedAccess($quote->user_id);
            }
        }

        try {
            $result = $this->signatureRequestSender->send($lead, $data, auth()->guard('user')->id());
        } catch (FirmaApiException $exception) {
            /**
             * Nothing was sent.
             */
            return new JsonResponse(['message' => $exception->getMessage()], 422);
        }

        return new JsonResponse([
            'outcome' => $result['outcome'],
            'message' => $result['message'],
            'data' => $result['request']->lead_id === $lead->id ? $this->present($result['request']) : null,
        ]);
    }

    /**
     * Read the request's status from Firma; a completed request without its signed document
     * starts the download again.
     */
    public function refresh(int $id, int $requestId): JsonResponse
    {
        $lead = $this->authorizedLead($id);

        $request = SignatureRequest::query()->where('lead_id', $lead->id)->findOrFail($requestId);

        try {
            $request = $this->signatureRequestSender->refresh($request);
        } catch (FirmaApiException $exception) {
            return new JsonResponse(['message' => $exception->getMessage()], 422);
        }

        return new JsonResponse([
            'message' => trans('admin::app.firma.lead.refreshed'),
            'data' => $this->present($request),
        ]);
    }

    /**
     * The lead, held to the same data scope as the lead's own page.
     */
    protected function authorizedLead(int $id)
    {
        $lead = $this->leadRepository->findOrFail($id);

        $this->preventUnauthorizedAccess($lead->user_id);

        return $lead;
    }

    /**
     * A request as the lead page shows it.
     */
    protected function present(SignatureRequest $request): array
    {
        return [
            'id' => $request->id,
            'firma_id' => $request->firma_id,
            'name' => $request->name,
            'source' => $request->source,
            'template_name' => $request->template_name,
            'quote_id' => $request->quote_id,
            'recipient_name' => $request->recipient_name,
            'recipient_email' => $request->recipient_email,
            'status' => $request->status,
            'status_label' => trans('admin::app.firma.statuses.'.$request->status),
            'no_invitation_email' => $request->no_invitation_email,
            'has_signed_document' => ! is_null($request->signed_activity_id),
            'fetch_failed' => $request->fetch_failed,
            'sent_at' => $request->sent_at?->toIso8601String(),
            'completed_at' => $request->completed_at?->toIso8601String(),
        ];
    }
}
