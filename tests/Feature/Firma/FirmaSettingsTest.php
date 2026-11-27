<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;
use Webkul\Core\Models\CoreConfig;
use Webkul\Firma\Services\FirmaClient;
use Webkul\Firma\Services\FirmaSettings;

require_once __DIR__.'/FirmaHelpers.php';

uses(DatabaseTransactions::class);

function firmaWebhookUrl(): string
{
    return 'https://crm.example.test/firma/webhook';
}

function firmaHttpsRoot(): void
{
    URL::forceRootUrl('https://crm.example.test');

    URL::forceScheme('https');
}

function firmaWebhookRow(array $overrides = []): array
{
    return array_merge([
        'id' => 'wh_1',
        'url' => firmaWebhookUrl(),
        'events' => FirmaClient::WEBHOOK_EVENTS,
        'enabled' => true,
        'auto_disabled_at' => null,
    ], $overrides);
}

function firmaWebhookWrites(): array
{
    return Http::recorded(fn (Request $request) => in_array($request->method(), ['POST', 'PUT']))
        ->map(fn ($pair) => [$pair[0]->method(), $pair[0]->url(), $pair[0]->data()])
        ->values()
        ->all();
}

it('stores the API key and the webhook secret encrypted', function () {
    $this->actingAs(getDefaultAdmin(), 'user')
        ->post(route('admin.settings.firma.update'), ['api_key' => 'firma_live_abcd1234', 'webhook_secret' => 'whsec_9876'])
        ->assertRedirect(route('admin.settings.firma.index'));

    $stored = CoreConfig::where('code', FirmaSettings::API_KEY)->value('value');

    expect($stored)->not->toContain('firma_live_abcd1234')
        ->and(Crypt::decryptString($stored))->toBe('firma_live_abcd1234')
        ->and(CoreConfig::where('code', FirmaSettings::WEBHOOK_SECRET)->value('value'))->not->toContain('whsec_9876')
        ->and(app(FirmaSettings::class)->webhookSecret())->toBe('whsec_9876');
});

it('never renders the stored secrets back into the settings page', function () {
    firmaConfigure('firma_live_abcd1234', 'whsec_9876');

    $this->actingAs(getDefaultAdmin(), 'user')
        ->get(route('admin.settings.firma.index'))
        ->assertOk()
        ->assertDontSee('firma_live_abcd1234')
        ->assertDontSee('whsec_9876')
        ->assertSee('1234')
        ->assertSee('Enable Webhooks');
});

it('keeps a stored value when its field is left blank', function () {
    firmaConfigure('firma_live_abcd1234', 'whsec_9876');

    $this->actingAs(getDefaultAdmin(), 'user')
        ->post(route('admin.settings.firma.update'), ['api_key' => '', 'webhook_secret' => 'whsec_new']);

    expect(app(FirmaSettings::class)->apiKey())->toBe('firma_live_abcd1234')
        ->and(app(FirmaSettings::class)->webhookSecret())->toBe('whsec_new');
});

it('treats an unreadable stored value as not configured', function () {
    CoreConfig::create(['code' => FirmaSettings::API_KEY, 'value' => 'plain text']);

    expect(app(FirmaSettings::class)->apiKey())->toBeNull();
});

it('tests the connection with the stored key', function () {
    firmaConfigure();

    Http::fake(['https://api.firma.dev/*' => Http::sequence()
        ->push(['results' => [], 'pagination' => ['total_pages' => 1]])
        ->push(['error' => 'Invalid API key', 'code' => 'UNAUTHORIZED'], 401)]);

    $this->actingAs(getDefaultAdmin(), 'user')
        ->post(route('admin.settings.firma.test'))
        ->assertSessionHas('success');

    $this->actingAs(getDefaultAdmin(), 'user')
        ->post(route('admin.settings.firma.test'))
        ->assertSessionHas('error', fn ($message) => str_contains($message, 'refused'));
});

it('registers the webhook only over https', function () {
    firmaConfigure();

    Http::fake();

    $this->actingAs(getDefaultAdmin(), 'user')
        ->post(route('admin.settings.firma.register_webhook'))
        ->assertSessionHas('error');

    Http::assertNothingSent();
});

it('refuses to register the webhook before the signing secret is stored', function () {
    firmaConfigure(secret: null);

    firmaHttpsRoot();

    Http::fake();

    $this->actingAs(getDefaultAdmin(), 'user')
        ->post(route('admin.settings.firma.register_webhook'))
        ->assertSessionHas('error', fn ($message) => str_contains($message, 'Webhook Signing Secret'));

    Http::assertNothingSent();

    expect(app(FirmaSettings::class)->webhookId())->toBeNull();

    $this->actingAs(getDefaultAdmin(), 'user')
        ->get(route('admin.settings.firma.index'))
        ->assertOk()
        ->assertSee(trans('admin::app.firma.settings.secret-required'), false);
});

it('creates the webhook when none exists and states the Enable Webhooks step', function () {
    firmaConfigure();

    firmaHttpsRoot();

    Http::fake([
        'https://api.firma.dev/*/webhooks?*' => Http::response(['results' => [], 'pagination' => ['total_pages' => 1]]),
        'https://api.firma.dev/*/webhooks' => Http::response(['id' => 'wh_new'], 201),
    ]);

    $this->actingAs(getDefaultAdmin(), 'user')
        ->post(route('admin.settings.firma.register_webhook'))
        ->assertSessionHas('success', fn ($message) => str_contains($message, 'Enable Webhooks'));

    expect(firmaWebhookWrites())->toBe([
        ['POST', 'https://api.firma.dev/functions/v1/signing-request-api/webhooks', ['url' => firmaWebhookUrl(), 'events' => FirmaClient::WEBHOOK_EVENTS]],
    ])->and(app(FirmaSettings::class)->webhookId())->toBe('wh_new');
});

it('reuses an enabled webhook that already has every event', function () {
    firmaConfigure();

    firmaHttpsRoot();

    Http::fake(['https://api.firma.dev/*/webhooks?*' => Http::response(['results' => [firmaWebhookRow()], 'pagination' => ['total_pages' => 1]])]);

    $this->actingAs(getDefaultAdmin(), 'user')
        ->post(route('admin.settings.firma.register_webhook'))
        ->assertSessionHas('success', fn ($message) => str_contains($message, 'already exists'));

    expect(firmaWebhookWrites())->toBe([]);
});

it('re-enables a disabled or auto disabled webhook and adds missing events', function (array $row, array $expectedEvents) {
    firmaConfigure();

    firmaHttpsRoot();

    Http::fake([
        'https://api.firma.dev/*/webhooks?*' => Http::response(['results' => [firmaWebhookRow($row)], 'pagination' => ['total_pages' => 1]]),
        'https://api.firma.dev/*/webhooks/wh_1' => Http::response(['id' => 'wh_1']),
    ]);

    $this->actingAs(getDefaultAdmin(), 'user')
        ->post(route('admin.settings.firma.register_webhook'))
        ->assertSessionHas('warning', fn ($message) => str_contains($message, 'Enable Webhooks'));

    expect(firmaWebhookWrites())->toBe([
        ['PUT', 'https://api.firma.dev/functions/v1/signing-request-api/webhooks/wh_1', ['enabled' => true, 'events' => $expectedEvents]],
    ]);
})->with([
    'disabled' => [['enabled' => false], FirmaClient::WEBHOOK_EVENTS],
    'auto disabled' => [['auto_disabled_at' => '2026-11-20T10:00:00Z'], FirmaClient::WEBHOOK_EVENTS],
    'missing events' => [['events' => ['signing_request.completed', 'signing_request.viewed']], array_merge(
        ['signing_request.completed', 'signing_request.viewed'],
        array_values(array_diff(FirmaClient::WEBHOOK_EVENTS, ['signing_request.completed']))
    )],
]);

it('reads every webhook page before deciding to create one', function () {
    firmaConfigure();

    firmaHttpsRoot();

    Http::fake([
        'https://api.firma.dev/*/webhooks?page=1*' => Http::response(['results' => [firmaWebhookRow(['id' => 'wh_other', 'url' => 'https://elsewhere.test/hook'])], 'pagination' => ['total_pages' => 2]]),
        'https://api.firma.dev/*/webhooks?page=2*' => Http::response(['results' => [firmaWebhookRow()], 'pagination' => ['total_pages' => 2]]),
    ]);

    $this->actingAs(getDefaultAdmin(), 'user')
        ->post(route('admin.settings.firma.register_webhook'))
        ->assertSessionHas('success');

    expect(firmaWebhookWrites())->toBe([]);
});

it('limits the Firma settings to users with that permission', function () {
    $this->actingAs(firmaProbeUser(['leads', 'leads.view']), 'user')
        ->post(route('admin.settings.firma.update'), ['api_key' => 'x'])
        ->assertStatus(401);

    expect(app(FirmaSettings::class)->apiKey())->toBeNull();
});
