<?php

namespace Webkul\Firma\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Sleep;
use Webkul\Firma\Models\SignatureRequest;
use Webkul\Firma\Services\FirmaApiException;
use Webkul\Firma\Services\FirmaSettings;
use Webkul\Firma\Services\LeadTimeline;

/**
 * Downloads the signed PDF of a completed request and attaches it to the lead.
 *
 * Right after completion Firma may answer 409 while it finishes the document, so the job
 * waits and tries again: through the queue's backoff on a real queue, or with short pauses
 * when the application runs jobs synchronously (then it runs after the webhook answered).
 */
class FetchSignedDocument implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    /**
     * The number of times the job may be attempted on a queue.
     *
     * @var int
     */
    public $tries = 5;

    /**
     * Seconds to wait before each retry on a queue.
     *
     * @var array
     */
    public $backoff = [60, 300, 900, 3600];

    /**
     * Pauses between attempts when the job runs synchronously.
     */
    public static array $syncRetryDelays = [5, 15];

    /**
     * Create a new job instance.
     */
    public function __construct(public int $signatureRequestId) {}

    /**
     * Queues the job, or runs it after the response when the queue is synchronous, so the
     * webhook answers within Firma's delivery timeout either way.
     */
    public static function dispatchFor(int $signatureRequestId): void
    {
        if (config('queue.default') === 'sync') {
            static::dispatchAfterResponse($signatureRequestId);

            return;
        }

        static::dispatch($signatureRequestId);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $request = SignatureRequest::query()->find($this->signatureRequestId);

        if (
            ! $request
            || ! $request->firma_id
            || $request->status !== SignatureRequest::STATUS_COMPLETED
            || $request->signed_activity_id
        ) {
            return;
        }

        $onQueue = $this->job && $this->job->getConnectionName() !== 'sync';

        $delays = $onQueue ? [] : static::$syncRetryDelays;

        $attempt = 0;

        while (true) {
            try {
                $content = app(FirmaSettings::class)->client()->fetchSignedPdf($request->firma_id);

                break;
            } catch (FirmaApiException $exception) {
                if ($exception->status === 409 && $attempt < count($delays)) {
                    Sleep::for($delays[$attempt++])->seconds();

                    continue;
                }

                if ($onQueue && $this->attempts() < $this->tries) {
                    $this->release($this->backoff[$this->attempts() - 1] ?? 3600);

                    return;
                }

                $this->giveUp($request, $exception);

                return;
            }
        }

        app(LeadTimeline::class)->attachSignedDocument($request, $content);
    }

    /**
     * Stops trying and says so on the lead; the lead's Refresh action tries again.
     */
    protected function giveUp(SignatureRequest $request, FirmaApiException $exception): void
    {
        Log::warning('Firma signed document not downloaded', [
            'signature_request_id' => $request->id,
            'status' => $exception->status,
            'code' => $exception->errorCode,
        ]);

        if ($request->fetch_failed) {
            return;
        }

        $request->update(['fetch_failed' => true]);

        app(LeadTimeline::class)->note($request, trans('admin::app.firma.timeline.fetch-failed', ['name' => $request->name]));
    }
}
