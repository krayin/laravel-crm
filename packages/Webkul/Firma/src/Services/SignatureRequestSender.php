<?php

namespace Webkul\Firma\Services;

use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\Log;
use Throwable;
use Webkul\Firma\Jobs\FetchSignedDocument;
use Webkul\Firma\Models\SignatureRequest;
use Webkul\Firma\Repositories\SignatureRequestRepository;
use Webkul\Lead\Models\Lead;

/**
 * Sends a lead's document for signature through Firma's create-and-send, at most once.
 *
 * Every send carries an idempotency key (one per send dialog). The key is written in its own
 * statement before the request to Firma, so a repeated submission of the dialog finds it and
 * never posts again. A create-and-send is never retried: an outcome Firma did not confirm is
 * kept as "uncertain" and the user is told to check Firma before sending again.
 */
class SignatureRequestSender
{
    public const OUTCOME_SENT = 'sent';

    public const OUTCOME_ALREADY_SENT = 'already_sent';

    public const OUTCOME_KEPT = 'kept';

    public const OUTCOME_UNCERTAIN = 'uncertain';

    public const OUTCOME_DUPLICATE = 'duplicate';

    /**
     * Create a new sender instance.
     */
    public function __construct(
        protected SignatureRequestRepository $signatureRequestRepository,
        protected FirmaSettings $firmaSettings,
        protected LeadTimeline $leadTimeline,
        protected QuotePdfRenderer $quotePdfRenderer,
    ) {}

    /**
     * Sends a template or a quote of the lead to one recipient.
     *
     * @param  array{idempotency_key: string, source: string, template_id?: ?string, quote_id?: ?int, name: string, recipient_name: string, recipient_email: string, expiration_hours?: ?int}  $data
     * @return array{outcome: string, message: string, request: SignatureRequest}
     *
     * @throws FirmaApiException when nothing was sent (the input was refused, locally or by Firma).
     */
    public function send(Lead $lead, array $data, ?int $userId): array
    {
        if ($existing = $this->signatureRequestRepository->findOneByField('idempotency_key', $data['idempotency_key'])) {
            return $this->duplicate($existing);
        }

        $client = $this->firmaSettings->client();

        $quote = null;

        if ($data['source'] === 'quote') {
            $quote = $lead->quotes()->where('quotes.id', $data['quote_id'] ?? 0)->first();

            if (! $quote) {
                throw FirmaApiException::invalidInput(trans('admin::app.firma.errors.quote-not-on-lead'));
            }
        }

        try {
            /** @var SignatureRequest $marker */
            $marker = $this->signatureRequestRepository->create([
                'idempotency_key' => $data['idempotency_key'],
                'lead_id' => $lead->id,
                'quote_id' => $quote?->getKey(),
                'user_id' => $userId,
                'name' => mb_substr(trim($data['name']), 0, FirmaPayload::MAX_NAME_LENGTH),
                'source' => $data['source'],
                'template_id' => $data['source'] === 'template' ? $data['template_id'] : null,
                'recipient_name' => mb_substr(trim($data['recipient_name']), 0, 255),
                'recipient_email' => mb_substr(trim($data['recipient_email']), 0, 255),
                'status' => SignatureRequest::STATUS_SENDING,
            ]);
        } catch (UniqueConstraintViolationException) {
            return $this->duplicate($this->signatureRequestRepository->getModel()->newQuery()->where('idempotency_key', $data['idempotency_key'])->firstOrFail());
        }

        try {
            $body = $this->buildBody($client, $marker, $data, $quote);
        } catch (Throwable $exception) {
            /**
             * Nothing was sent yet: free the key so the corrected dialog can send.
             */
            $marker->delete();

            if ($exception instanceof FirmaApiException) {
                throw $exception;
            }

            /**
             * A failure inside the CRM (the quote PDF renderer, for example) is still a refusal
             * before any request to Firma, so the user is told nothing was sent.
             */
            Log::warning('Firma send not prepared', [
                'lead_id' => $lead->id,
                'source' => $data['source'],
                'error' => class_basename($exception),
            ]);

            throw FirmaApiException::invalidInput(trans('admin::app.firma.errors.prepare-failed'));
        }

        try {
            $result = $client->createAndSend($body);
        } catch (Throwable $exception) {
            return $this->handleFailure($client, $marker, $exception);
        }

        $marker->fill([
            'firma_id' => $result['id'],
            'name' => is_string($result['name'] ?? null) && $result['name'] !== '' ? mb_substr($result['name'], 0, 255) : $marker->name,
            'status' => SignatureRequest::STATUS_SENT,
            'sent_at' => now(),
            'no_invitation_email' => ($result['settings']['send_signing_email'] ?? null) === false,
        ])->save();

        $message = trans('admin::app.firma.send.sent', [
            'recipient' => $marker->recipient_name.' <'.$marker->recipient_email.'>',
        ]);

        if ($marker->no_invitation_email) {
            $message .= ' '.trans('admin::app.firma.send.no-invitation');
        }

        $this->leadTimeline->note($marker, $message, $userId);

        return ['outcome' => self::OUTCOME_SENT, 'message' => $message, 'request' => $marker];
    }

    /**
     * Reads the status from Firma and applies it when it moves the request forward.
     */
    public function refresh(SignatureRequest $request): SignatureRequest
    {
        if (! $request->firma_id) {
            return $request;
        }

        $detail = $this->firmaSettings->client()->getSigningRequest($request->firma_id);

        $status = SendSafety::statusFromFlags($detail['status'] ?? null);

        if ($status && $request->advanceTo($status)) {
            $this->leadTimeline->note($request, trans('admin::app.firma.timeline.status', [
                'name' => $request->name,
                'status' => trans('admin::app.firma.statuses.'.$status),
            ]));
        }

        if ($request->status === SignatureRequest::STATUS_COMPLETED && ! $request->signed_activity_id) {
            $request->update(['fetch_failed' => false]);

            FetchSignedDocument::dispatchFor($request->id);
        }

        return $request;
    }

    /**
     * The create-and-send body, read fresh from Firma for templates.
     */
    protected function buildBody(FirmaClient $client, SignatureRequest $marker, array $data, $quote): array
    {
        [$firstName, $lastName] = FirmaPayload::splitName($data['recipient_name']);

        $expiration = isset($data['expiration_hours']) ? (int) $data['expiration_hours'] : null;

        if ($data['source'] === 'template') {
            /**
             * Read the template again: its roles decide the body and may have changed since the
             * dialog listed it.
             */
            $template = $client->getTemplate((string) ($data['template_id'] ?? ''));

            if (! $template) {
                throw FirmaApiException::invalidInput(trans('admin::app.firma.errors.template-missing'));
            }

            $body = FirmaPayload::buildTemplateBody(
                $template['id'],
                FirmaPayload::pickTemplateUser($template),
                $data['name'],
                $firstName,
                $lastName,
                $data['recipient_email'],
                $expiration
            );

            $marker->update(['template_name' => mb_substr($template['name'], 0, 255)]);

            return $body;
        }

        $content = $this->quotePdfRenderer->render($quote);

        $pageCount = $this->quotePdfRenderer->pageCount($content);

        if ($pageCount < 1) {
            throw FirmaApiException::invalidInput(trans('admin::app.firma.errors.unreadable-pdf'));
        }

        if (strlen($content) <= FirmaClient::INLINE_DOCUMENT_LIMIT) {
            return FirmaPayload::buildDocumentBody(
                $data['name'], $firstName, $lastName, $data['recipient_email'], $pageCount, base64_encode($content), null, $expiration
            );
        }

        $documentId = $client->uploadDocument('Quote_'.$quote->getKey().'.pdf', $content);

        return FirmaPayload::buildDocumentBody(
            $data['name'], $firstName, $lastName, $data['recipient_email'], $pageCount, null, $documentId, $expiration
        );
    }

    /**
     * Records what a failed create-and-send left behind. Never sends again.
     */
    protected function handleFailure(FirmaClient $client, SignatureRequest $marker, Throwable $exception): array
    {
        $classified = SendSafety::classify($exception);

        if ($classified['kind'] === SendSafety::NOT_CREATED) {
            $marker->delete();

            throw $exception;
        }

        $firmaId = $classified['signing_request_id'];

        if ($firmaId && in_array($classified['kind'], [SendSafety::ALREADY_SENT, SendSafety::KEPT], true)) {
            $status = SignatureRequest::STATUS_SENT;

            if ($classified['kind'] === SendSafety::ALREADY_SENT) {
                $status = $client->recoverSentRequest($firmaId)['status'];
            }

            $request = $this->signatureRequestRepository->findOneByField('firma_id', $firmaId);

            if ($request) {
                /**
                 * This CRM already tracks the request Firma names: keep that one.
                 */
                $marker->delete();
            } else {
                $request = $marker;

                $request->fill(['firma_id' => $firmaId, 'sent_at' => now()])->save();
            }

            $request->advanceTo(SignatureRequest::STATUS_SENT);

            $request->advanceTo($status);

            $message = $classified['kind'] === SendSafety::ALREADY_SENT
                ? trans('admin::app.firma.send.already-sent', ['id' => $firmaId])
                : trans('admin::app.firma.send.kept', ['id' => $firmaId]);

            $this->leadTimeline->note($request, $message, $request->user_id);

            return [
                'outcome' => $classified['kind'] === SendSafety::ALREADY_SENT ? self::OUTCOME_ALREADY_SENT : self::OUTCOME_KEPT,
                'message' => $message,
                'request' => $request,
            ];
        }

        /**
         * Unknown outcome: the request may have been created and sent.
         */
        Log::warning('Firma create-and-send outcome unknown', [
            'signature_request_id' => $marker->id,
            'lead_id' => $marker->lead_id,
            'status' => $exception instanceof FirmaApiException ? $exception->status : null,
            'code' => $exception instanceof FirmaApiException ? $exception->errorCode : class_basename($exception),
        ]);

        $marker->update(['status' => SignatureRequest::STATUS_UNCERTAIN]);

        $message = trans('admin::app.firma.send.uncertain');

        $this->leadTimeline->note($marker, $message, $marker->user_id);

        return ['outcome' => self::OUTCOME_UNCERTAIN, 'message' => $message, 'request' => $marker];
    }

    /**
     * The answer for a dialog that was already submitted: nothing is sent again.
     */
    protected function duplicate(SignatureRequest $request): array
    {
        return [
            'outcome' => self::OUTCOME_DUPLICATE,
            'message' => trans('admin::app.firma.send.duplicate'),
            'request' => $request,
        ];
    }
}
