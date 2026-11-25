<?php

namespace Webkul\Firma\Services;

use Illuminate\Support\Str;
use RuntimeException;

/**
 * An error answer from the Firma API, a failure to get one, or input refused before any call.
 */
class FirmaApiException extends RuntimeException
{
    /**
     * Messages are shown to users, so they are kept short.
     */
    public const MAX_MESSAGE_LENGTH = 400;

    /**
     * Create a new exception instance.
     *
     * @param  int  $status  The HTTP status, or 0 when no answer arrived (or none was asked for).
     * @param  string  $errorCode  Firma's `code`, or a local code such as NETWORK_ERROR or INVALID_INPUT.
     * @param  bool  $hasFirmaBody  Whether the answer carried Firma's own `{error, code}` body.
     */
    public function __construct(
        public readonly int $status,
        public readonly string $errorCode,
        string $message,
        public readonly bool $hasFirmaBody = false,
        public readonly ?string $signingRequestId = null,
    ) {
        parent::__construct(Str::limit($message, self::MAX_MESSAGE_LENGTH - 3, '...'));
    }

    /**
     * Input refused before any call to Firma.
     */
    public static function invalidInput(string $message): self
    {
        return new self(0, 'INVALID_INPUT', $message);
    }
}
