<?php

namespace Webkul\Firma\Http\Controllers;

use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Webkul\Firma\Jobs\FetchSignedDocument;
use Webkul\Firma\Repositories\WebhookEventRepository;
use Webkul\Firma\Services\FirmaSettings;
use Webkul\Firma\Services\WebhookProcessor;
use Webkul\Firma\Services\WebhookSignature;

class WebhookController extends Controller
{
    public const MAX_BODY_BYTES = 1024 * 1024;

    /**
     * Firma retries one event for about an hour, so de-duplication rows older than this are
     * removed now and then.
     */
    public const RETENTION_DAYS = 14;

    /**
     * Outcomes that acted on a tracked request; only these are kept for de-duplication. Events
     * for requests this CRM does not track are acknowledged and not stored, which bounds the
     * table. Firma marks an acknowledged delivery as delivered and never sends it again, so
     * such an event is not processed later: a send whose outcome stayed unknown (no Firma id)
     * is not followed here and is checked in Firma.
     */
    protected const STORED_OUTCOMES = ['updated', 'unchanged'];

    /**
     * Create a new controller instance.
     */
    public function __construct(
        protected FirmaSettings $firmaSettings,
        protected WebhookProcessor $webhookProcessor,
        protected WebhookEventRepository $webhookEventRepository,
    ) {}

    /**
     * Receives one Firma webhook delivery.
     */
    public function __invoke(Request $request): JsonResponse
    {
        $raw = $request->getContent();

        if (strlen($raw) > self::MAX_BODY_BYTES) {
            return new JsonResponse(['error' => 'payload too large'], 413);
        }

        /**
         * Fails closed: without a stored secret nothing verifies.
         */
        $verdict = WebhookSignature::verify(fn (string $name) => $request->header($name), $raw, $this->firmaSettings->webhookSecret());

        if ($verdict !== WebhookSignature::OK) {
            Log::info('Firma webhook refused', ['reason' => $verdict]);

            return new JsonResponse(['error' => $verdict.' signature'], 401);
        }

        $payload = json_decode($raw, true);

        if (! is_array($payload)) {
            return new JsonResponse(['error' => 'invalid JSON'], 400);
        }

        $eventId = $payload['id'] ?? null;

        if (! is_string($eventId) || $eventId === '' || strlen($eventId) > 255) {
            return new JsonResponse(['error' => 'missing event id'], 400);
        }

        if ($this->webhookEventRepository->getModel()->newQuery()->where('event_id', $eventId)->exists()) {
            return new JsonResponse(['status' => 'duplicate']);
        }

        try {
            $result = DB::transaction(function () use ($payload, $eventId) {
                $result = $this->webhookProcessor->process($payload);

                if (in_array($result['outcome'], self::STORED_OUTCOMES, true)) {
                    $this->webhookEventRepository->create([
                        'event_id' => $eventId,
                        'event_type' => mb_substr((string) ($payload['type'] ?? ''), 0, 128),
                        'firma_id' => mb_substr((string) data_get($payload, 'data.signing_request.id', ''), 0, 64),
                    ]);
                }

                return $result;
            });
        } catch (UniqueConstraintViolationException) {
            /**
             * A concurrent delivery of the same event won; its changes stand and these were
             * rolled back.
             */
            return new JsonResponse(['status' => 'duplicate']);
        }

        if ($result['fetch']) {
            FetchSignedDocument::dispatchFor($result['fetch']);
        }

        if (random_int(1, 100) === 1) {
            $this->webhookEventRepository->getModel()->newQuery()->where('created_at', '<', now()->subDays(self::RETENTION_DAYS))->delete();
        }

        return new JsonResponse(['status' => $result['outcome']]);
    }
}
