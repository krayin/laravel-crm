<?php

namespace Webkul\Firma\Services;

use Illuminate\Support\Carbon;
use Throwable;
use Webkul\Firma\Models\SignatureRequest;
use Webkul\Firma\Repositories\SignatureRequestRepository;

/**
 * Applies one verified Firma webhook payload. Only records the change: slow work (the signed
 * document download) is left to a queued job, because Firma waits 5 seconds for an answer.
 */
class WebhookProcessor
{
    public const EVENT_STATUS = [
        'signing_request.recipient.signed' => SignatureRequest::STATUS_IN_PROGRESS,
        'signing_request.completed' => SignatureRequest::STATUS_COMPLETED,
        'signing_request.recipient.declined' => SignatureRequest::STATUS_DECLINED,
        'signing_request.cancelled' => SignatureRequest::STATUS_CANCELLED,
        'signing_request.expired' => SignatureRequest::STATUS_EXPIRED,
    ];

    /**
     * Create a new processor instance.
     */
    public function __construct(
        protected SignatureRequestRepository $signatureRequestRepository,
        protected LeadTimeline $leadTimeline,
    ) {}

    /**
     * Returns the outcome and, for a request that just completed, its id to download.
     *
     * @return array{outcome: string, fetch: ?int}
     */
    public function process(array $payload): array
    {
        $type = $payload['type'] ?? null;

        $data = is_array($payload['data'] ?? null) ? $payload['data'] : [];

        $firmaId = data_get($data, 'signing_request.id');

        if (! is_string($firmaId) || $firmaId === '' || ! is_string($type)) {
            return ['outcome' => 'ignored', 'fetch' => null];
        }

        /** @var SignatureRequest|null $request */
        $request = $this->signatureRequestRepository->findOneByField('firma_id', $firmaId);

        if (! $request) {
            return ['outcome' => 'unknown_request', 'fetch' => null];
        }

        if ($type === 'signing_request.recipient.signed') {
            $this->leadTimeline->note($request, trans('admin::app.firma.timeline.signed', [
                'name' => $request->name,
                'recipients' => $this->recipientNames($data, 'signed_at') ?: trans('admin::app.firma.timeline.a-recipient'),
            ]));
        } elseif ($type === 'signing_request.recipient.declined') {
            $this->leadTimeline->note($request, trans('admin::app.firma.timeline.declined', [
                'name' => $request->name,
                'recipients' => $this->recipientNames($data, 'declined_at') ?: trans('admin::app.firma.timeline.a-recipient'),
            ]));
        }

        $status = self::EVENT_STATUS[$type] ?? null;

        if (! $status) {
            return ['outcome' => 'ignored', 'fetch' => null];
        }

        $changed = $request->advanceTo($status, $status === SignatureRequest::STATUS_COMPLETED
            ? $this->timestamp(data_get($data, 'signing_request.completed_at'))
            : null);

        if ($changed && in_array($status, [
            SignatureRequest::STATUS_COMPLETED,
            SignatureRequest::STATUS_CANCELLED,
            SignatureRequest::STATUS_EXPIRED,
        ], true)) {
            $this->leadTimeline->note($request, trans('admin::app.firma.timeline.status', [
                'name' => $request->name,
                'status' => trans('admin::app.firma.statuses.'.$status),
            ]));
        }

        return [
            'outcome' => $changed ? 'updated' : 'unchanged',
            'fetch' => $changed && $status === SignatureRequest::STATUS_COMPLETED ? $request->id : null,
        ];
    }

    /**
     * Names from the single `recipient` Firma sends with recipient events, else from the
     * `recipients` entries carrying `$doneKey`.
     */
    protected function recipientNames(array $data, string $doneKey): string
    {
        $candidates = is_array($data['recipient'] ?? null)
            ? [$data['recipient']]
            : array_filter($data['recipients'] ?? [], fn ($row) => is_array($row) && ! empty($row[$doneKey]));

        $names = [];

        foreach ($candidates as $candidate) {
            $full = trim(implode(' ', array_filter([
                is_string($candidate['first_name'] ?? null) ? $candidate['first_name'] : '',
                is_string($candidate['last_name'] ?? null) ? $candidate['last_name'] : '',
            ])));

            $label = trim((is_string($candidate['name'] ?? null) ? $candidate['name'] : '') ?: $full ?: (is_string($candidate['email'] ?? null) ? $candidate['email'] : ''));

            if ($label !== '') {
                $names[] = mb_substr($label, 0, 100);
            }
        }

        return implode(', ', $names);
    }

    /**
     * Parses an ISO timestamp, or null.
     */
    protected function timestamp(mixed $value): ?Carbon
    {
        if (! is_string($value) || $value === '') {
            return null;
        }

        try {
            return Carbon::parse($value);
        } catch (Throwable) {
            return null;
        }
    }
}
