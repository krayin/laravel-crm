<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use Webkul\Firma\Models\SignatureRequest;
use Webkul\Lead\Models\Lead;
use Webkul\Quote\Models\Quote;

require_once __DIR__.'/FirmaHelpers.php';

uses(DatabaseTransactions::class);

function firmaSendPayload(array $overrides = []): array
{
    return array_merge([
        'idempotency_key' => (string) Str::uuid(),
        'source' => 'template',
        'template_id' => 'tpl_1',
        'name' => 'Master services agreement',
        'recipient_name' => 'Ada Lovelace',
        'recipient_email' => 'ada@firma.dev',
    ], $overrides);
}

function firmaSend(Lead $lead, array $payload, $user = null): TestResponse
{
    return test()->actingAs($user ?? getDefaultAdmin(), 'user')
        ->postJson(route('admin.leads.firma.store', $lead->id), $payload);
}

function firmaFakeTemplate(array $recipients): array
{
    return [
        'https://api.firma.dev/*/templates/tpl_1' => Http::response(['id' => 'tpl_1', 'name' => 'MSA', 'recipients' => $recipients]),
    ];
}

const FIRMA_ONE_SIGNER = [['id' => 'tu_1', 'designation' => 'Signer', 'order' => 1], ['id' => 'cc_1', 'designation' => 'CC', 'order' => 2]];

function firmaCreateAndSendCount(): int
{
    return Http::recorded(fn (Request $request) => str_ends_with($request->url(), '/signing-requests/create-and-send'))->count();
}

it('sends a single role template to the lead contact and records it', function () {
    firmaConfigure();

    $lead = firmaLead();

    Http::fake(firmaFakeTemplate(FIRMA_ONE_SIGNER) + [
        'https://api.firma.dev/*/signing-requests/create-and-send' => Http::response([
            'id' => 'sr_new',
            'name' => 'Master services agreement',
            'settings' => ['send_signing_email' => true],
            'first_signer' => ['id' => 'su_1'],
        ], 201),
    ]);

    firmaSend($lead, firmaSendPayload(['expiration_hours' => 48]))
        ->assertOk()
        ->assertJson(['outcome' => 'sent']);

    Http::assertSent(fn (Request $request) => str_ends_with($request->url(), '/create-and-send')
        && $request->data() === [
            'name' => 'Master services agreement',
            'expiration_hours' => 48,
            'template_id' => 'tpl_1',
            'recipients' => [[
                'first_name' => 'Ada',
                'email' => 'ada@firma.dev',
                'last_name' => 'Lovelace',
                'template_user_id' => 'tu_1',
                'designation' => 'Signer',
                'order' => 1,
            ]],
        ]);

    $request = SignatureRequest::where('lead_id', $lead->id)->sole();

    expect($request->firma_id)->toBe('sr_new')
        ->and($request->status)->toBe('sent')
        ->and($request->template_name)->toBe('MSA')
        ->and($request->no_invitation_email)->toBeFalse()
        ->and($lead->activities()->where('title', 'like', '%ada@firma.dev%')->count())->toBe(1);

    $this->actingAs(getDefaultAdmin(), 'user')
        ->getJson(route('admin.leads.firma.index', $lead->id))
        ->assertOk()
        ->assertJsonPath('data.0.firma_id', 'sr_new');
});

it('notes when Firma did not e-mail the invitation', function () {
    firmaConfigure();

    $lead = firmaLead();

    Http::fake(firmaFakeTemplate(FIRMA_ONE_SIGNER) + [
        'https://api.firma.dev/*/signing-requests/create-and-send' => Http::response(['id' => 'sr_quiet', 'settings' => ['send_signing_email' => false]], 201),
    ]);

    firmaSend($lead, firmaSendPayload())->assertOk();

    expect(SignatureRequest::where('firma_id', 'sr_quiet')->sole()->no_invitation_email)->toBeTrue();
});

it('refuses templates one recipient cannot fill before sending anything', function (array $recipients) {
    firmaConfigure();

    $lead = firmaLead();

    Http::fake(firmaFakeTemplate($recipients));

    firmaSend($lead, firmaSendPayload())->assertStatus(422);

    expect(firmaCreateAndSendCount())->toBe(0)
        ->and(SignatureRequest::where('lead_id', $lead->id)->count())->toBe(0);
})->with([
    'two signers' => [[['id' => 'a', 'designation' => 'Signer', 'order' => 1], ['id' => 'b', 'designation' => 'Signer', 'order' => 2]]],
    'only a CC' => [[['id' => 'c', 'designation' => 'CC', 'order' => 1]]],
]);

it('never resends after an uncertain outcome', function () {
    firmaConfigure();

    $lead = firmaLead();

    Http::fake(firmaFakeTemplate(FIRMA_ONE_SIGNER) + [
        'https://api.firma.dev/*/signing-requests/create-and-send' => Http::response(['error' => 'Bad gateway', 'code' => 'PROXY_ERROR'], 502),
    ]);

    $payload = firmaSendPayload();

    firmaSend($lead, $payload)->assertOk()->assertJson(['outcome' => 'uncertain']);

    firmaSend($lead, $payload)->assertOk()->assertJson(['outcome' => 'duplicate']);

    expect(firmaCreateAndSendCount())->toBe(1)
        ->and(SignatureRequest::where('lead_id', $lead->id)->sole()->status)->toBe('uncertain')
        ->and($lead->activities()->where('title', 'like', '%may have been sent%')->count())->toBe(1);
});

it('sends at most once for one dialog, even after success', function () {
    firmaConfigure();

    $lead = firmaLead();

    Http::fake(firmaFakeTemplate(FIRMA_ONE_SIGNER) + [
        'https://api.firma.dev/*/signing-requests/create-and-send' => Http::response(['id' => 'sr_once'], 201),
    ]);

    $payload = firmaSendPayload();

    firmaSend($lead, $payload)->assertJson(['outcome' => 'sent']);

    firmaSend($lead, $payload)->assertJson(['outcome' => 'duplicate']);

    expect(firmaCreateAndSendCount())->toBe(1);
});

it('lets the user correct a refused send and try again with the same dialog', function () {
    firmaConfigure();

    $lead = firmaLead();

    Http::fake(firmaFakeTemplate(FIRMA_ONE_SIGNER) + [
        'https://api.firma.dev/*/signing-requests/create-and-send' => Http::sequence()
            ->push(['error' => 'Invalid email', 'code' => 'VALIDATION_ERROR'], 400)
            ->push(['id' => 'sr_second'], 201),
    ]);

    $payload = firmaSendPayload();

    firmaSend($lead, $payload)->assertStatus(422)->assertJsonPath('message', fn ($message) => str_contains($message, 'Invalid email'));

    expect(SignatureRequest::where('lead_id', $lead->id)->count())->toBe(0);

    firmaSend($lead, $payload)->assertOk()->assertJson(['outcome' => 'sent']);

    expect(firmaCreateAndSendCount())->toBe(2);
});

it('recovers the request Firma reports as already sent', function () {
    firmaConfigure();

    $lead = firmaLead();

    Http::fake(firmaFakeTemplate(FIRMA_ONE_SIGNER) + [
        'https://api.firma.dev/*/signing-requests/create-and-send' => Http::response(['error' => 'Already sent', 'code' => 'ALREADY_SENT', 'signing_request_id' => 'sr_old'], 409),
        'https://api.firma.dev/*/signing-requests/sr_old' => Http::response(['id' => 'sr_old', 'name' => 'Old MSA', 'status' => ['sent' => true, 'finished' => true]]),
    ]);

    firmaSend($lead, firmaSendPayload())->assertOk()->assertJson(['outcome' => 'already_sent']);

    $request = SignatureRequest::where('lead_id', $lead->id)->sole();

    expect($request->firma_id)->toBe('sr_old')
        ->and($request->status)->toBe('completed')
        ->and(firmaCreateAndSendCount())->toBe(1);
});

it('keeps the request Firma kept after a failed send', function () {
    firmaConfigure();

    $lead = firmaLead();

    Http::fake(firmaFakeTemplate(FIRMA_ONE_SIGNER) + [
        'https://api.firma.dev/*/signing-requests/create-and-send' => Http::response(['error' => 'Kept', 'code' => 'SEND_EFFECTS_EXIST', 'signing_request_id' => 'sr_kept'], 409),
    ]);

    firmaSend($lead, firmaSendPayload())->assertOk()->assertJson(['outcome' => 'kept']);

    expect(SignatureRequest::where('lead_id', $lead->id)->sole()->firma_id)->toBe('sr_kept');
});

it('sends a quote of the lead as a PDF with a signature box', function () {
    firmaConfigure();

    $lead = firmaLead();

    $quote = Quote::create([
        'subject' => 'Firma quote',
        'user_id' => getDefaultAdmin()->id,
        'person_id' => $lead->person_id,
        'expired_at' => now()->addWeek(),
        'sub_total' => 0,
        'discount_amount' => 0,
        'tax_amount' => 0,
        'adjustment_amount' => 0,
        'grand_total' => 0,
    ]);

    $lead->quotes()->attach($quote->id);

    Http::fake(['https://api.firma.dev/*/signing-requests/create-and-send' => Http::response(['id' => 'sr_quote'], 201)]);

    firmaSend($lead, firmaSendPayload(['source' => 'quote', 'template_id' => null, 'quote_id' => $quote->id]))
        ->assertOk()
        ->assertJson(['outcome' => 'sent']);

    Http::assertSent(function (Request $request) {
        $body = $request->data();

        return str_ends_with($request->url(), '/create-and-send')
            && str_starts_with(base64_decode($body['document']), '%PDF')
            && $body['fields'][0]['type'] === 'signature'
            && $body['fields'][0]['page_number'] >= 1
            && isset($body['fields'][0]['position']['x'])
            && $body['recipients'][0]['designation'] === 'Signer';
    });

    expect(SignatureRequest::where('lead_id', $lead->id)->sole()->quote_id)->toBe($quote->id);
});

it('refuses a quote that does not belong to the lead', function () {
    firmaConfigure();

    $lead = firmaLead();

    $quote = Quote::create([
        'subject' => 'Other quote',
        'user_id' => getDefaultAdmin()->id,
        'person_id' => firmaPerson()->id,
        'expired_at' => now()->addWeek(),
        'sub_total' => 0,
        'discount_amount' => 0,
        'tax_amount' => 0,
        'adjustment_amount' => 0,
        'grand_total' => 0,
    ]);

    Http::fake();

    firmaSend($lead, firmaSendPayload(['source' => 'quote', 'quote_id' => $quote->id]))->assertStatus(422);

    Http::assertNothingSent();
});

it('refuses to send without an API key', function () {
    firmaConfigure(apiKey: null);

    Http::fake();

    firmaSend(firmaLead(), firmaSendPayload())->assertStatus(422);

    Http::assertNothingSent();
});

it('requires the send for signature permission', function () {
    firmaConfigure();

    Http::fake();

    firmaSend(firmaLead(), firmaSendPayload(), firmaProbeUser(['leads', 'leads.view']))->assertStatus(401);

    Http::assertNothingSent();
});

it('keeps users to the leads they may see', function () {
    firmaConfigure();

    Http::fake();

    $user = firmaProbeUser(['leads', 'leads.view', 'leads.firma'], 'individual');

    firmaSend(firmaLead(), firmaSendPayload(), $user)->assertStatus(401);

    Http::assertNothingSent();
});

it('lists single role templates for the send dialog', function () {
    firmaConfigure();

    Http::fake(['https://api.firma.dev/*/templates*' => Http::response([
        'results' => [
            ['id' => 't1', 'name' => 'One', 'recipients' => [['id' => 'u1', 'designation' => 'Signer', 'order' => 1]]],
            ['id' => 't2', 'name' => 'Two', 'recipients' => [['id' => 'u1', 'designation' => 'Signer', 'order' => 1], ['id' => 'u2', 'designation' => 'Signer', 'order' => 2]]],
        ],
        'pagination' => ['total_pages' => 1],
    ])]);

    $this->actingAs(getDefaultAdmin(), 'user')
        ->getJson(route('admin.leads.firma.templates'))
        ->assertOk()
        ->assertJson([
            'complete' => true,
            'data' => [
                ['id' => 't1', 'name' => 'One', 'supported' => true],
                ['id' => 't2', 'name' => 'Two', 'supported' => false],
            ],
        ]);
});

it('refreshes a status from Firma without moving it back', function () {
    firmaConfigure();

    $lead = firmaLead();

    $request = firmaSignatureRequest($lead, ['status' => 'in_progress']);

    Http::fake(['https://api.firma.dev/*' => Http::response(['id' => $request->firma_id, 'status' => ['sent' => true]])]);

    $this->actingAs(getDefaultAdmin(), 'user')
        ->postJson(route('admin.leads.firma.refresh', [$lead->id, $request->id]))
        ->assertOk()
        ->assertJsonPath('data.status', 'in_progress');
});

it('shows the Signatures tab on the lead page only with the send permission', function () {
    $lead = firmaLead();

    $this->actingAs(getDefaultAdmin(), 'user')
        ->get(route('admin.leads.view', $lead->id))
        ->assertOk()
        ->assertSee('v-lead-firma', false)
        ->assertSee(route('admin.leads.firma.store', $lead->id), false);

    $this->actingAs(firmaProbeUser(['leads', 'leads.view']), 'user')
        ->get(route('admin.leads.view', $lead->id))
        ->assertOk()
        ->assertDontSee('v-lead-firma', false);
});

it('lists the Firma permissions in the role editor tree', function () {
    $keys = collect(config('acl'))->pluck('key');

    expect($keys)->toContain('leads.firma')
        ->and($keys)->toContain('settings.other_settings.firma');
});
