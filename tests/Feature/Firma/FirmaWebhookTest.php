<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Sleep;
use Illuminate\Testing\TestResponse;
use Webkul\Firma\Jobs\FetchSignedDocument;
use Webkul\Firma\Models\WebhookEvent;

require_once __DIR__.'/FirmaHelpers.php';

uses(DatabaseTransactions::class);

function firmaWebhookPost(array|string $payload, ?string $secret = 'whsec_test', ?int $timestamp = null, string $header = 'X-Firma-Signature'): TestResponse
{
    $raw = is_string($payload) ? $payload : json_encode($payload);

    $timestamp ??= time();

    $headers = ['CONTENT_TYPE' => 'application/json'];

    if ($secret !== null) {
        $headers['HTTP_'.strtoupper(str_replace('-', '_', $header))] = 't='.$timestamp.',v1='.hash_hmac('sha256', $timestamp.'.'.$raw, $secret);
    }

    return test()->call('POST', route('firma.webhook'), [], [], [], $headers, $raw);
}

function firmaEvent(string $type, string $firmaId, array $data = []): array
{
    return [
        'id' => 'evt_'.uniqid(),
        'type' => $type,
        'data' => array_merge(['signing_request' => ['id' => $firmaId]], $data),
    ];
}

it('serves the webhook outside the web middleware group, so no session or CSRF token applies', function () {
    $route = Route::getRoutes()->getByName('firma.webhook');

    expect($route->methods())->toBe(['POST'])
        ->and($route->gatherMiddleware())->not->toContain('web');
});

it('refuses a delivery when no webhook secret is configured', function () {
    firmaConfigure(secret: null);

    firmaWebhookPost(firmaEvent('signing_request.completed', 'sr_x'))->assertStatus(401);
});

it('refuses unsigned, wrongly signed and stale deliveries', function () {
    firmaConfigure();

    $lead = firmaLead();

    $request = firmaSignatureRequest($lead);

    firmaWebhookPost(firmaEvent('signing_request.completed', $request->firma_id), null)->assertStatus(401);

    firmaWebhookPost(firmaEvent('signing_request.completed', $request->firma_id), 'whsec_other')->assertStatus(401);

    firmaWebhookPost(firmaEvent('signing_request.completed', $request->firma_id), 'whsec_test', time() - 600)
        ->assertStatus(401)
        ->assertJson(['error' => 'stale signature']);

    expect($request->fresh()->status)->toBe('sent');
});

it('accepts the previous secret during a rotation', function () {
    firmaConfigure();

    $request = firmaSignatureRequest(firmaLead());

    firmaWebhookPost(firmaEvent('signing_request.recipient.signed', $request->firma_id), 'whsec_test', null, 'X-Firma-Signature-Old')
        ->assertOk()
        ->assertJson(['status' => 'updated']);
});

it('records a signature on the lead timeline and moves the status forward', function () {
    firmaConfigure();

    $lead = firmaLead();

    $request = firmaSignatureRequest($lead);

    firmaWebhookPost(firmaEvent('signing_request.recipient.signed', $request->firma_id, [
        'recipient' => ['first_name' => 'Ada', 'last_name' => 'Lovelace', 'email' => 'ada@firma.dev'],
    ]))->assertOk()->assertJson(['status' => 'updated']);

    expect($request->fresh()->status)->toBe('in_progress')
        ->and($lead->activities()->where('title', 'like', '%Ada Lovelace%')->count())->toBe(1);
});

it('marks the request completed and queues the signed document download', function () {
    Bus::fake([FetchSignedDocument::class]);

    firmaConfigure();

    $lead = firmaLead();

    $request = firmaSignatureRequest($lead);

    firmaWebhookPost(firmaEvent('signing_request.completed', $request->firma_id))->assertOk();

    expect($request->fresh()->status)->toBe('completed')
        ->and($request->fresh()->completed_at)->not->toBeNull();

    Bus::assertDispatched(FetchSignedDocument::class, fn ($job) => $job->signatureRequestId === $request->id);
});

it('acknowledges a repeated delivery without processing it again', function () {
    firmaConfigure();

    $lead = firmaLead();

    $request = firmaSignatureRequest($lead);

    $event = firmaEvent('signing_request.recipient.declined', $request->firma_id, ['recipient' => ['email' => 'ada@firma.dev']]);

    firmaWebhookPost($event)->assertOk()->assertJson(['status' => 'updated']);

    firmaWebhookPost($event)->assertOk()->assertJson(['status' => 'duplicate']);

    expect(WebhookEvent::where('event_id', $event['id'])->count())->toBe(1)
        ->and($request->fresh()->status)->toBe('declined')
        ->and($lead->activities()->where('title', 'like', '%declined%')->count())->toBe(1);
});

it('never moves a finished request back', function () {
    firmaConfigure();

    $request = firmaSignatureRequest(firmaLead(), ['status' => 'completed', 'completed_at' => now()]);

    firmaWebhookPost(firmaEvent('signing_request.recipient.signed', $request->firma_id))
        ->assertOk()
        ->assertJson(['status' => 'unchanged']);

    firmaWebhookPost(firmaEvent('signing_request.cancelled', $request->firma_id))->assertOk();

    expect($request->fresh()->status)->toBe('completed');
});

it('acknowledges events for requests this CRM does not track and does not store them', function () {
    firmaConfigure();

    $event = firmaEvent('signing_request.completed', 'sr_unknown');

    firmaWebhookPost($event)->assertOk()->assertJson(['status' => 'unknown_request']);

    expect(WebhookEvent::where('event_id', $event['id'])->exists())->toBeFalse();
});

it('rejects a signed body that is not a Firma event', function () {
    firmaConfigure();

    firmaWebhookPost('not json')->assertStatus(400);

    firmaWebhookPost(['type' => 'signing_request.completed'])->assertStatus(400);
});

it('answers only POST', function () {
    $this->get(route('firma.webhook'))->assertStatus(405);
});

it('attaches the signed PDF to the lead once', function () {
    Storage::fake();

    firmaConfigure();

    $lead = firmaLead();

    $request = firmaSignatureRequest($lead, ['status' => 'completed', 'completed_at' => now(), 'name' => 'Master services agreement']);

    Http::fake([
        'https://api.firma.dev/*/download' => Http::response(['status' => 'finished', 'is_partial' => false, 'download_url' => 'https://files.firma.test/signed.pdf?sig=1']),
        'https://files.firma.test/*' => Http::response('%PDF-1.4 signed', 200),
    ]);

    (new FetchSignedDocument($request->id))->handle();
    (new FetchSignedDocument($request->id))->handle();

    $request->refresh();

    $activity = $lead->activities()->where('type', 'file')->sole();

    expect($request->signed_activity_id)->toBe($activity->id)
        ->and($activity->files)->toHaveCount(1)
        ->and(Storage::get($activity->files->first()->path))->toBe('%PDF-1.4 signed')
        ->and($activity->files->first()->name)->toBe('Master services agreement (signed).pdf');

    Http::assertSent(fn ($sent) => str_starts_with($sent->url(), 'https://files.firma.test/') && ! $sent->hasHeader('Authorization'));
});

it('stops after repeated not ready answers and says so on the timeline', function () {
    Sleep::fake();

    firmaConfigure();

    $lead = firmaLead();

    $request = firmaSignatureRequest($lead, ['status' => 'completed', 'completed_at' => now()]);

    Http::fake(['https://api.firma.dev/*' => Http::response(['error' => 'Not ready', 'code' => 'DOCUMENT_NOT_READY'], 409)]);

    (new FetchSignedDocument($request->id))->handle();

    $request->refresh();

    expect($request->signed_activity_id)->toBeNull()
        ->and($request->fetch_failed)->toBeTrue()
        ->and($lead->activities()->where('type', 'system')->where('title', 'like', '%signed document%')->count())->toBe(1);
});
