<?php

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Webkul\Firma\Services\FirmaApiException;
use Webkul\Firma\Services\FirmaClient;
use Webkul\Firma\Services\FirmaPayload;
use Webkul\Firma\Services\SendSafety;
use Webkul\Firma\Services\WebhookSignature;

function firmaApiUrl(string $path): string
{
    return 'https://api.firma.dev/functions/v1/signing-request-api'.$path;
}

function firmaTemplateRow(string $id, array $recipients): array
{
    return ['id' => $id, 'name' => 'Template '.$id, 'recipients' => $recipients];
}

it('sends the raw API key without a Bearer prefix', function () {
    Http::fake([firmaApiUrl('/templates*') => Http::response(['results' => [], 'pagination' => ['total_pages' => 1]])]);

    (new FirmaClient('  key_123  '))->testConnection();

    Http::assertSent(fn (Request $request) => $request->method() === 'GET'
        && $request->header('Authorization') === ['key_123']
        && str_contains($request->url(), '/templates?page_size=1'));
});

it('refuses to build a client without an API key', function () {
    new FirmaClient('');
})->throws(FirmaApiException::class);

it('reads every template page and keeps only signer and approver roles', function () {
    Http::fake([
        firmaApiUrl('/templates?page=1*') => Http::response([
            'results' => [firmaTemplateRow('t1', [
                ['id' => 'u2', 'designation' => 'Approver', 'order' => 2],
                ['id' => 'u1', 'designation' => 'Signer', 'order' => 1],
                ['id' => 'c1', 'designation' => 'CC', 'order' => 3],
            ])],
            'pagination' => ['current_page' => 1, 'total_pages' => 2],
        ]),
        firmaApiUrl('/templates?page=2*') => Http::response([
            'results' => [firmaTemplateRow('t2', [['id' => 'u9', 'designation' => 'Signer', 'order' => 1]])],
            'pagination' => ['current_page' => 2, 'total_pages' => 2],
        ]),
    ]);

    $listing = (new FirmaClient('key'))->listTemplates();

    expect($listing['complete'])->toBeTrue()
        ->and(array_column($listing['templates'], 'id'))->toBe(['t1', 't2'])
        ->and(array_column($listing['templates'][0]['users'], 'id'))->toBe(['u1', 'u2'])
        ->and($listing['templates'][0]['supported'])->toBeFalse()
        ->and($listing['templates'][1]['supported'])->toBeTrue();

    Http::assertSent(fn (Request $request) => str_contains($request->url(), 'page=1&page_size=200&sort_by=name&sort_order=asc'));
    Http::assertSentCount(2);
});

it('marks a template listing incomplete when a promised page comes back empty', function () {
    Http::fake([
        firmaApiUrl('/templates?page=1*') => Http::response([
            'results' => [firmaTemplateRow('t1', [])],
            'pagination' => ['total_pages' => 3],
        ]),
        firmaApiUrl('/templates?page=2*') => Http::response(['results' => [], 'pagination' => ['total_pages' => 3]]),
    ]);

    expect((new FirmaClient('key'))->listTemplates()['complete'])->toBeFalse();
});

it('answers null for a template that no longer exists', function () {
    Http::fake([firmaApiUrl('/templates/gone') => Http::response(['error' => 'Not found', 'code' => 'NOT_FOUND'], 404)]);

    expect((new FirmaClient('key'))->getTemplate('gone'))->toBeNull();
});

it('targets the single template role of a one role template', function () {
    $template = FirmaPayload::parseTemplate(firmaTemplateRow('t1', [
        ['id' => 'u1', 'designation' => 'Approver', 'order' => 2],
        ['id' => 'c1', 'designation' => 'CC', 'order' => 1],
    ]));

    $body = FirmaPayload::buildTemplateBody('t1', FirmaPayload::pickTemplateUser($template), ' Contract ', 'Ada', 'Lovelace', 'ada@firma.dev', 72);

    expect($body)->toBe([
        'name' => 'Contract',
        'expiration_hours' => 72,
        'template_id' => 't1',
        'recipients' => [[
            'first_name' => 'Ada',
            'email' => 'ada@firma.dev',
            'last_name' => 'Lovelace',
            'template_user_id' => 'u1',
            'designation' => 'Approver',
            'order' => 2,
        ]],
    ]);
});

it('refuses templates whose roles one recipient cannot fill', function (array $recipients) {
    FirmaPayload::pickTemplateUser(FirmaPayload::parseTemplate(firmaTemplateRow('t1', $recipients)));
})->with([
    'two roles' => [[['id' => 'u1', 'designation' => 'Signer', 'order' => 1], ['id' => 'u2', 'designation' => 'Signer', 'order' => 2]]],
    'no role' => [[['id' => 'c1', 'designation' => 'CC', 'order' => 1]]],
    'empty' => [[]],
])->throws(FirmaApiException::class);

it('builds a document body with one signature box on the last page', function () {
    $inline = FirmaPayload::buildDocumentBody('Quote 7', 'Ada', null, 'ada@firma.dev', 3, 'JVBERi0=', null, null);
    $uploaded = FirmaPayload::buildDocumentBody('Quote 7', 'Ada', null, 'ada@firma.dev', 1, null, 'doc_1', null);

    expect($inline['document'])->toBe('JVBERi0=')
        ->and($inline)->not->toHaveKey('document_id')
        ->and($inline['recipients'])->toBe([['first_name' => 'Ada', 'email' => 'ada@firma.dev', 'id' => 'temp_1', 'designation' => 'Signer', 'order' => 1]])
        ->and($inline['fields'][0])->toMatchArray(['type' => 'signature', 'recipient_id' => 'temp_1', 'page_number' => 3, 'required' => true])
        ->and($inline['fields'][0]['position'])->toBe(FirmaPayload::DEFAULT_SIGNATURE_BOX)
        ->and($uploaded['document_id'])->toBe('doc_1')
        ->and($uploaded)->not->toHaveKey('document');
});

it('refuses an invalid recipient email before any call', function () {
    FirmaPayload::buildDocumentBody('Quote', 'Ada', null, 'not-an-email', 1, 'JVBERi0=', null, null);
})->throws(FirmaApiException::class);

it('uploads large documents through a presigned URL without the API key', function () {
    Http::fake([
        firmaApiUrl('/documents') => Http::response(['document_id' => 'doc_9', 'upload_url' => 'https://uploads.firma.test/put?sig=1'], 201),
        'https://uploads.firma.test/*' => Http::response('', 200),
    ]);

    $documentId = (new FirmaClient('key'))->uploadDocument('quote.pdf', '%PDF-1.4 body');

    expect($documentId)->toBe('doc_9');

    Http::assertSent(fn (Request $request) => $request->url() === firmaApiUrl('/documents')
        && $request->data() === ['file_name' => 'quote.pdf', 'file_size' => 13, 'content_type' => 'application/pdf']);

    Http::assertSent(fn (Request $request) => $request->method() === 'PUT'
        && str_starts_with($request->url(), 'https://uploads.firma.test/')
        && ! $request->hasHeader('Authorization')
        && $request->body() === '%PDF-1.4 body');
});

it('classifies every create and send failure', function (int $status, array|string|null $body, string $expected, ?string $id) {
    Http::fake([firmaApiUrl('/signing-requests/create-and-send') => Http::response($body, $status)]);

    try {
        (new FirmaClient('key'))->createAndSend(['name' => 'x']);
        $error = null;
    } catch (Throwable $caught) {
        $error = $caught;
    }

    expect(SendSafety::classify($error))->toBe(['kind' => $expected, 'signing_request_id' => $id]);
})->with([
    '400 validation' => [400, ['error' => 'Bad', 'code' => 'VALIDATION_ERROR'], SendSafety::NOT_CREATED, null],
    '401' => [401, ['error' => 'No', 'code' => 'UNAUTHORIZED'], SendSafety::NOT_CREATED, null],
    '402 credits' => [402, ['error' => 'Credits', 'code' => 'INSUFFICIENT_CREDITS'], SendSafety::NOT_CREATED, null],
    '404' => [404, ['error' => 'Gone', 'code' => 'NOT_FOUND'], SendSafety::NOT_CREATED, null],
    '413' => [413, ['error' => 'Big', 'code' => 'PAYLOAD_TOO_LARGE'], SendSafety::NOT_CREATED, null],
    '422' => [422, ['error' => 'Bad', 'code' => 'UNPROCESSABLE'], SendSafety::NOT_CREATED, null],
    '429' => [429, ['error' => 'Slow', 'code' => 'RATE_LIMITED'], SendSafety::NOT_CREATED, null],
    '409 already sent' => [409, ['error' => 'Sent', 'code' => 'ALREADY_SENT', 'signing_request_id' => 'sr_1'], SendSafety::ALREADY_SENT, 'sr_1'],
    '409 already sent without id' => [409, ['error' => 'Sent', 'code' => 'ALREADY_SENT'], SendSafety::NOT_CREATED, null],
    '409 send effects' => [409, ['error' => 'Kept', 'code' => 'SEND_EFFECTS_EXIST', 'signing_request_id' => 'sr_2'], SendSafety::KEPT, 'sr_2'],
    '409 in progress' => [409, ['error' => 'Busy', 'code' => 'SEND_IN_PROGRESS', 'signing_request_id' => 'sr_3'], SendSafety::KEPT, 'sr_3'],
    '409 in progress without id' => [409, ['error' => 'Busy', 'code' => 'SEND_IN_PROGRESS'], SendSafety::KEPT, null],
    '409 other' => [409, ['error' => 'Conflict', 'code' => 'CONFLICT'], SendSafety::NOT_CREATED, null],
    '500 firma' => [500, ['error' => 'Oops', 'code' => 'INTERNAL'], SendSafety::UNKNOWN, null],
    '502 proxy' => [502, ['error' => 'Proxy', 'code' => 'PROXY_ERROR'], SendSafety::UNKNOWN, null],
    '503 anchor' => [503, ['error' => 'Off', 'code' => 'ANCHOR_DISABLED_EDGE'], SendSafety::NOT_CREATED, null],
    '503 seals' => [503, ['error' => 'Off', 'code' => 'SEALS_DISABLED'], SendSafety::NOT_CREATED, null],
    '503 without body' => [503, 'Service Unavailable', SendSafety::UNKNOWN, null],
    '503 other code' => [503, ['error' => 'Down', 'code' => 'UNAVAILABLE'], SendSafety::UNKNOWN, null],
    '504' => [504, null, SendSafety::UNKNOWN, null],
    '546' => [546, null, SendSafety::UNKNOWN, null],
    '201 unreadable' => [201, 'not json', SendSafety::UNKNOWN, null],
    '201 without id' => [201, ['name' => 'x'], SendSafety::UNKNOWN, null],
]);

it('treats a network failure during create and send as an unknown outcome', function () {
    Http::fake(fn () => throw new ConnectionException('reset'));

    try {
        (new FirmaClient('key'))->createAndSend(['name' => 'x']);
    } catch (FirmaApiException $error) {
        expect($error->status)->toBe(0)
            ->and(SendSafety::classify($error)['kind'])->toBe(SendSafety::UNKNOWN);

        return;
    }

    $this->fail('No exception was raised.');
});

it('maps Firma status flags to a request status', function (mixed $flags, ?string $expected) {
    expect(SendSafety::statusFromFlags($flags))->toBe($expected);
})->with([
    [['finished' => true, 'sent' => true], 'completed'],
    [['declined' => true, 'sent' => true], 'declined'],
    [['cancelled' => true], 'cancelled'],
    [['expired' => true], 'expired'],
    [['sent' => true], 'sent'],
    [['sent' => false], null],
    ['sent', null],
]);

it('verifies webhook signatures over the timestamp and raw body', function () {
    $raw = '{"id":"evt_1"}';
    $now = 1_800_000_000;
    $sign = fn (string $secret, int $t) => 't='.$t.',v1='.hash_hmac('sha256', $t.'.'.$raw, $secret);
    $headers = fn (array $values) => fn (string $name) => $values[$name] ?? null;

    expect(WebhookSignature::verify($headers(['X-Firma-Signature' => $sign('s3cret', $now)]), $raw, 's3cret', $now))->toBe('ok')
        ->and(WebhookSignature::verify($headers(['X-Firma-Signature' => $sign('new', $now), 'X-Firma-Signature-Old' => $sign('s3cret', $now)]), $raw, 's3cret', $now))->toBe('ok')
        ->and(WebhookSignature::verify($headers(['X-Firma-Signature' => $sign('s3cret', $now - 301)]), $raw, 's3cret', $now))->toBe('stale')
        ->and(WebhookSignature::verify($headers(['X-Firma-Signature' => $sign('s3cret', $now)]), $raw.' ', 's3cret', $now))->toBe('invalid')
        ->and(WebhookSignature::verify($headers(['X-Firma-Signature' => $sign('other', $now)]), $raw, 's3cret', $now))->toBe('invalid')
        ->and(WebhookSignature::verify($headers(['X-Firma-Signature' => $sign('s3cret', $now)]), $raw, null, $now))->toBe('invalid')
        ->and(WebhookSignature::verify($headers(['X-Firma-Signature' => $sign('s3cret', $now)]), $raw, '', $now))->toBe('invalid')
        ->and(WebhookSignature::verify($headers([]), $raw, 's3cret', $now))->toBe('invalid')
        ->and(WebhookSignature::verify($headers(['X-Firma-Signature' => 't=abc,v1=00']), $raw, 's3cret', $now))->toBe('invalid');
});
