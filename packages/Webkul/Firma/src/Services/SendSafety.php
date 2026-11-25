<?php

namespace Webkul\Firma\Services;

use Throwable;

/**
 * What a failed create-and-send left behind. A send is never retried automatically.
 */
class SendSafety
{
    /**
     * Firma answered and nothing was created, or the input was refused before any call.
     */
    public const NOT_CREATED = 'not_created';

    /**
     * 409 ALREADY_SENT with the id of the request that was sent.
     */
    public const ALREADY_SENT = 'already_sent';

    /**
     * 409 SEND_EFFECTS_EXIST with an id, or SEND_IN_PROGRESS: the request exists and may be out.
     */
    public const KEPT = 'kept';

    /**
     * No usable answer, any other 5xx (Firma's own 500 included, its rollback can fail), or a
     * 409 ALREADY_SENT without an id: the request may have been created and sent.
     */
    public const UNKNOWN = 'unknown';

    /**
     * 503 codes that Firma answers before creating anything.
     */
    protected const DEFINITE_503_CODES = ['ANCHOR_DISABLED_EDGE', 'SEALS_DISABLED'];

    /**
     * Classifies a create-and-send failure.
     *
     * @return array{kind: string, signing_request_id: ?string}
     */
    public static function classify(?Throwable $error): array
    {
        $result = fn (string $kind, ?string $id = null) => ['kind' => $kind, 'signing_request_id' => $id];

        if (! $error instanceof FirmaApiException) {
            return $result(self::UNKNOWN);
        }

        if (in_array($error->errorCode, ['INVALID_INPUT', 'UPLOAD_FAILED'], true)) {
            return $result(self::NOT_CREATED);
        }

        if ($error->status === 0 || $error->errorCode === 'INVALID_RESPONSE') {
            return $result(self::UNKNOWN);
        }

        if ($error->status === 409) {
            /**
             * Firma answers ALREADY_SENT from create-and-send only with the id of the request it
             * kept. Without the id the answer is not one Firma gives, and the code itself says a
             * request was sent, so the outcome is unknown.
             */
            if ($error->errorCode === 'ALREADY_SENT') {
                return $error->signingRequestId
                    ? $result(self::ALREADY_SENT, $error->signingRequestId)
                    : $result(self::UNKNOWN);
            }

            /**
             * SEND_EFFECTS_EXIST names the request Firma could not roll back. Without the id it is
             * the send step's refusal passed on after the rollback deleted the request, so nothing
             * is left.
             */
            if ($error->errorCode === 'SEND_EFFECTS_EXIST' && $error->signingRequestId) {
                return $result(self::KEPT, $error->signingRequestId);
            }

            /**
             * Another attempt holds the send; Firma did not roll the request back.
             */
            if ($error->errorCode === 'SEND_IN_PROGRESS') {
                return $result(self::KEPT, $error->signingRequestId);
            }

            return $result(self::NOT_CREATED);
        }

        if ($error->status < 500) {
            return $result(self::NOT_CREATED);
        }

        if (
            $error->status === 503
            && $error->hasFirmaBody
            && in_array($error->errorCode, self::DEFINITE_503_CODES, true)
        ) {
            return $result(self::NOT_CREATED);
        }

        return $result(self::UNKNOWN);
    }

    /**
     * Maps the `status` flags of GET /signing-requests/{id} to a request status.
     */
    public static function statusFromFlags(mixed $flags): ?string
    {
        if (! is_array($flags)) {
            return null;
        }

        foreach ([
            'finished' => 'completed',
            'declined' => 'declined',
            'cancelled' => 'cancelled',
            'expired' => 'expired',
            'sent' => 'sent',
        ] as $flag => $status) {
            if (! empty($flags[$flag])) {
                return $status;
            }
        }

        return null;
    }
}
