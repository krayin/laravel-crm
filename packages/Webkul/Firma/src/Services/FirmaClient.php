<?php

namespace Webkul\Firma\Services;

use GuzzleHttp\Exception\TransferException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

/**
 * Minimal client for the Firma REST API (contract 01.45.00).
 *
 * Authentication is the raw API key in the `Authorization` header (no "Bearer" prefix).
 */
class FirmaClient
{
    public const BASE_URL = 'https://api.firma.dev/functions/v1/signing-request-api';

    public const TIMEOUT = 30;

    public const TRANSFER_TIMEOUT = 120;

    /**
     * Inline base64 documents share Firma's 6,291,456 byte request cap with the rest of the body,
     * so only files up to 4 MiB are sent inline. Larger files use the presigned upload.
     */
    public const INLINE_DOCUMENT_LIMIT = 4 * 1024 * 1024;

    public const MAX_UPLOAD_SIZE = 52428800;

    /**
     * Firma lists serve at most 200 rows per page.
     */
    public const PAGE_SIZE = 200;

    public const MAX_PAGES = 100;

    public const WEBHOOK_EVENTS = [
        'signing_request.completed',
        'signing_request.recipient.signed',
        'signing_request.recipient.declined',
        'signing_request.cancelled',
        'signing_request.expired',
    ];

    protected string $apiKey;

    protected string $baseUrl;

    /**
     * Create a new client instance.
     */
    public function __construct(?string $apiKey, string $baseUrl = self::BASE_URL)
    {
        $apiKey = trim((string) $apiKey);

        if ($apiKey === '') {
            throw FirmaApiException::invalidInput(trans('admin::app.firma.errors.not-configured'));
        }

        $this->apiKey = $apiKey;

        $this->baseUrl = rtrim($baseUrl, '/');
    }

    /**
     * Calls the Firma API and returns the decoded JSON answer.
     *
     * @throws FirmaApiException for any non 2xx answer or when no answer arrived.
     */
    public function request(string $method, string $path, array $query = [], ?array $body = null): mixed
    {
        $url = $this->baseUrl.$path;

        $query = array_filter($query, fn ($value) => ! is_null($value));

        if ($query) {
            $url .= '?'.http_build_query($query);
        }

        try {
            $response = $this->http()->send($method, $url, is_null($body) ? [] : ['json' => $body]);
        } catch (ConnectionException|TransferException) {
            throw new FirmaApiException(0, 'NETWORK_ERROR', 'Could not reach the Firma API. Please try again.');
        }

        $parsed = $this->decode($response);

        if (! $response->successful()) {
            $data = is_array($parsed) ? $parsed : [];

            $code = is_string($data['code'] ?? null) ? $data['code'] : null;

            $error = is_string($data['error'] ?? null) ? $data['error'] : null;

            $signingRequestId = $data['signing_request_id'] ?? null;

            $status = $response->status();

            throw new FirmaApiException(
                $status,
                $code ?? 'HTTP_'.$status,
                sprintf('Firma API error %d (%s): %s', $status, $code ?? 'HTTP_'.$status, $error ?? ($response->reason() ?: 'Request failed')),
                hasFirmaBody: $code !== null && $error !== null,
                signingRequestId: is_string($signingRequestId) && $signingRequestId !== '' ? $signingRequestId : null,
            );
        }

        return $parsed;
    }

    /**
     * GET /templates?page_size=1. Throws when the key does not work.
     */
    public function testConnection(): array
    {
        return $this->expectArray($this->request('GET', '/templates', ['page_size' => 1]));
    }

    /**
     * Every template, page by page.
     *
     * `complete` is true only when the listing reached its end: the page `pagination.total_pages`
     * names, or (without pagination data) a page shorter than the page size.
     *
     * @return array{templates: array, complete: bool}
     */
    public function listTemplates(): array
    {
        $templates = [];

        foreach ($this->pages('/templates', ['sort_by' => 'name', 'sort_order' => 'asc'], $complete) as $row) {
            if (! empty($row['id'])) {
                $templates[] = FirmaPayload::parseTemplate($row);
            }
        }

        return ['templates' => $templates, 'complete' => $complete];
    }

    /**
     * GET /templates/{id}, or null when it does not exist.
     */
    public function getTemplate(string $templateId): ?array
    {
        try {
            $body = $this->request('GET', '/templates/'.rawurlencode($templateId));
        } catch (FirmaApiException $exception) {
            if ($exception->status === 404) {
                return null;
            }

            throw $exception;
        }

        return FirmaPayload::parseTemplate($this->expectArray($body));
    }

    /**
     * GET /signing-requests/{id}.
     */
    public function getSigningRequest(string $signingRequestId): array
    {
        return $this->expectArray($this->request('GET', '/signing-requests/'.rawurlencode($signingRequestId)));
    }

    /**
     * GET /signing-requests/{id}/download: JSON with a presigned `download_url`.
     */
    public function getDownload(string $signingRequestId): array
    {
        return $this->expectArray($this->request('GET', '/signing-requests/'.rawurlencode($signingRequestId).'/download'));
    }

    /**
     * The final signed PDF bytes.
     *
     * A 409 right after completion means the final document is not ready yet; the caller tries
     * again later.
     */
    public function fetchSignedPdf(string $signingRequestId): string
    {
        $info = $this->getDownload($signingRequestId);

        if (! empty($info['is_partial']) || ($info['status'] ?? null) !== 'finished') {
            throw new FirmaApiException(409, 'NOT_READY', 'The signed document is not ready yet.');
        }

        $url = $info['download_url'] ?? null;

        if (! is_string($url) || ! str_starts_with($url, 'https://')) {
            throw new FirmaApiException(200, 'INVALID_RESPONSE', 'The Firma API returned no download URL.');
        }

        try {
            /**
             * Presigned URL: the API key is not sent with this call.
             */
            $response = Http::timeout(self::TRANSFER_TIMEOUT)->get($url);
        } catch (ConnectionException|TransferException) {
            throw new FirmaApiException(0, 'NETWORK_ERROR', 'Could not download the signed document.');
        }

        if ($response->status() !== 200 || $response->body() === '') {
            throw new FirmaApiException($response->status(), 'DOWNLOAD_FAILED', 'Could not download the signed document.');
        }

        return $response->body();
    }

    /**
     * POST /documents, then PUT the bytes to the presigned `upload_url`. Returns the `document_id`.
     */
    public function uploadDocument(string $fileName, string $content): string
    {
        $size = strlen($content);

        if ($size > self::MAX_UPLOAD_SIZE) {
            throw FirmaApiException::invalidInput(trans('admin::app.firma.errors.document-too-large'));
        }

        $body = $this->expectArray($this->request('POST', '/documents', [], [
            'file_name' => mb_substr($fileName, 0, 255),
            'file_size' => $size,
            'content_type' => 'application/pdf',
        ]));

        $documentId = $body['document_id'] ?? null;

        $uploadUrl = $body['upload_url'] ?? null;

        if (! is_string($documentId) || $documentId === '' || ! is_string($uploadUrl)) {
            throw new FirmaApiException(200, 'INVALID_RESPONSE', 'The Firma API returned no upload URL.');
        }

        try {
            $response = Http::timeout(self::TRANSFER_TIMEOUT)
                ->withBody($content, 'application/pdf')
                ->put($uploadUrl);
        } catch (ConnectionException|TransferException) {
            throw new FirmaApiException(0, 'UPLOAD_FAILED', 'Could not upload the document to Firma.');
        }

        if (! $response->successful()) {
            /**
             * Nothing was sent yet, so this is a plain failure.
             */
            throw new FirmaApiException(400, 'UPLOAD_FAILED', sprintf('Could not upload the document to Firma (%d).', $response->status()));
        }

        return $documentId;
    }

    /**
     * POST /signing-requests/create-and-send.
     *
     * Never retried: classify a failure with SendSafety::classify().
     */
    public function createAndSend(array $body): array
    {
        $parsed = $this->expectArray($this->request('POST', '/signing-requests/create-and-send', [], $body));

        if (empty($parsed['id']) || ! is_string($parsed['id'])) {
            throw new FirmaApiException(200, 'INVALID_RESPONSE', 'The Firma API returned a response this integration does not understand.');
        }

        return $parsed;
    }

    /**
     * What we know about a request Firma reported as already sent.
     *
     * Best effort: the request exists either way, so a failed read leaves the name empty and the
     * status at "sent".
     *
     * @return array{id: string, name: string, status: string}
     */
    public function recoverSentRequest(string $signingRequestId): array
    {
        $result = ['id' => $signingRequestId, 'name' => '', 'status' => 'sent'];

        try {
            $detail = $this->getSigningRequest($signingRequestId);

            $result['name'] = is_string($detail['name'] ?? null) ? $detail['name'] : '';

            $result['status'] = SendSafety::statusFromFlags($detail['status'] ?? null) ?? 'sent';
        } catch (FirmaApiException) {
            // Keep the defaults.
        }

        return $result;
    }

    /**
     * Every company webhook registered for exactly this URL, read across all pages.
     *
     * @throws FirmaApiException when the listing could not be read to its end.
     */
    public function findWebhooks(string $url): array
    {
        $rows = $this->pages('/webhooks', ['url' => $url], $complete);

        if (! $complete) {
            throw new FirmaApiException(200, 'INCOMPLETE_LISTING', 'Firma did not return the complete webhook list. Please try again.');
        }

        return array_values(array_filter($rows, fn ($row) => ($row['url'] ?? null) === $url && ! empty($row['id'])));
    }

    /**
     * POST /webhooks. Returns the new webhook id.
     */
    public function createWebhook(string $url, array $events = self::WEBHOOK_EVENTS): ?string
    {
        $body = $this->expectArray($this->request('POST', '/webhooks', [], ['url' => $url, 'events' => $events]));

        return is_string($body['id'] ?? null) ? $body['id'] : null;
    }

    /**
     * PUT /webhooks/{id}. `enabled: true` also clears Firma's failure counter and auto disable.
     */
    public function updateWebhook(string $webhookId, array $values): array
    {
        return $this->expectArray($this->request('PUT', '/webhooks/'.rawurlencode($webhookId), [], $values));
    }

    /**
     * Reads a paginated list to its end (at most MAX_PAGES pages).
     *
     * @param  bool  $complete  Set to true only when the last page was reached.
     */
    protected function pages(string $path, array $query, ?bool &$complete = null): array
    {
        $rows = [];

        $complete = false;

        for ($page = 1; $page <= self::MAX_PAGES; $page++) {
            $body = $this->expectArray($this->request('GET', $path, array_merge(
                ['page' => $page, 'page_size' => self::PAGE_SIZE],
                $query
            )));

            $results = $body['results'] ?? null;

            if (! is_array($results)) {
                throw new FirmaApiException(200, 'INVALID_RESPONSE', 'The Firma API returned a response this integration does not understand.');
            }

            foreach ($results as $row) {
                if (is_array($row)) {
                    $rows[] = $row;
                }
            }

            $totalPages = $body['pagination']['total_pages'] ?? null;

            if (is_int($totalPages)) {
                if ($page >= $totalPages) {
                    $complete = true;

                    return $rows;
                }
            } elseif (count($results) < self::PAGE_SIZE) {
                $complete = true;

                return $rows;
            }

            if (! $results) {
                /**
                 * Firma promised more pages but returned none: not complete.
                 */
                return $rows;
            }
        }

        return $rows;
    }

    /**
     * A request with the API key and JSON headers.
     */
    protected function http(): PendingRequest
    {
        return Http::withHeaders([
            'Authorization' => $this->apiKey,
            'Accept' => 'application/json',
        ])->timeout(self::TIMEOUT)->connectTimeout(10);
    }

    /**
     * The decoded JSON body, or null when it is empty or not JSON.
     */
    protected function decode(Response $response): mixed
    {
        $text = $response->body();

        if ($text === '') {
            return null;
        }

        $decoded = json_decode($text, true);

        return json_last_error() === JSON_ERROR_NONE ? $decoded : null;
    }

    /**
     * Ensures a successful answer is a JSON object.
     */
    protected function expectArray(mixed $value): array
    {
        if (! is_array($value)) {
            throw new FirmaApiException(200, 'INVALID_RESPONSE', 'The Firma API returned a response this integration does not understand.');
        }

        return $value;
    }
}
