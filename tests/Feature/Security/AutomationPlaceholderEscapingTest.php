<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Webkul\Automation\Helpers\Entity\Activity as ActivityHelper;
use Webkul\Automation\Helpers\Entity\Lead as LeadHelper;
use Webkul\Core\Contracts\Validations\Decimal;
use Webkul\WebForm\Rules\PhoneNumber;

/**
 * Workflow placeholders (`{%leads.title%}` and friends) are substituted into three different
 * targets, and the value being substituted is entity data — on the public web-form path, data an
 * anonymous visitor supplied. Splicing it in raw meant:
 *
 * - email bodies are rendered with `{!! $body !!}`, so a stored title became markup in the
 *   delivered mail (forged content, phishing links, tracking pixels);
 * - the webhook endpoint is a URL template, so an unencoded value could introduce `?`, `&`, `#` or
 *   `/../` and restructure the path and query the server requests on the admin's trusted host;
 * - the webhook query params, payload and headers are JSON encoded *before* substitution, so an
 *   unescaped quote broke out of its JSON string.
 *
 * The invite attachment had the same shape of bug in a different format: values were concatenated
 * into iCalendar properties joined with CRLF, so a newline terminated a property early.
 */
uses(DatabaseTransactions::class);

/**
 * `escapePlaceholderValue` is the single point every substitution passes through, so the contexts
 * are asserted there rather than through a full workflow run.
 */
function escapeFor(string $value, string $context): string
{
    $helper = app(LeadHelper::class);

    $method = new ReflectionMethod($helper, 'escapePlaceholderValue');
    $method->setAccessible(true);

    return $method->invoke($helper, $value, $context);
}

it('escapes html so a stored value cannot become markup in an email', function () {
    $payload = '</td></tr><h1>Payroll portal moved</h1><a href="https://evil.invalid">Sign in</a>';

    $escaped = escapeFor($payload, 'html');

    expect($escaped)->not->toContain('<h1>');
    expect($escaped)->not->toContain('<a href');
    expect($escaped)->toContain('&lt;h1&gt;');
});

it('url-encodes a value so it cannot restructure the webhook endpoint', function () {
    $escaped = escapeFor('../../admin/export?token=', 'url');

    expect($escaped)->not->toContain('/');
    expect($escaped)->not->toContain('?');
    expect($escaped)->toBe(rawurlencode('../../admin/export?token='));
});

it('url-encodes the separators that would inject extra query parameters', function () {
    $escaped = escapeFor('x&admin=1#frag', 'url');

    expect($escaped)->not->toContain('&');
    expect($escaped)->not->toContain('#');
});

it('escapes a value so it cannot break out of the webhook json', function () {
    $escaped = escapeFor('a" , "injected": "yes', 'json');

    /**
     * Every quote must be backslash-escaped — an unescaped one would close the JSON string and let
     * the rest of the value be read as further JSON. The round-trip is the assertion that matters:
     * the value lands inside its string intact and introduces no sibling key.
     */
    expect($escaped)->not->toMatch('/(?<!\\\\)"/');

    $decoded = json_decode('{"v":"'.$escaped.'"}', true);

    expect($decoded)->toBe(['v' => 'a" , "injected": "yes']);
    expect($decoded)->not->toHaveKey('injected');
});

it('leaves an ordinary value readable in every context', function () {
    expect(escapeFor('Quarterly review', 'html'))->toBe('Quarterly review');
    expect(escapeFor('Quarterly review', 'json'))->toBe('Quarterly review');
    expect(escapeFor('Quarterly-review', 'url'))->toBe('Quarterly-review');
});

/**
 * The webhook path must ask for the URL and JSON contexts, not the default.
 */
it('substitutes the webhook endpoint in url context and its json fields in json context', function () {
    $source = file_get_contents(
        base_path('packages/Webkul/Automation/src/Helpers/Entity/AbstractEntity.php')
    );

    expect($source)->toContain("\$webhook->end_point, 'url'");
    expect($source)->toContain("json_encode(\$webhook->query_params), 'json'");
    expect($source)->toContain("json_encode(\$webhook->payload), 'json'");
    expect($source)->toContain("json_encode(\$webhook->headers), 'json'");
});

it('escapes crlf in an ics value so it cannot inject calendar properties', function () {
    $helper = app(ActivityHelper::class);

    $method = new ReflectionMethod($helper, 'escapeICSValue');
    $method->setAccessible(true);

    $payload = "Standup\r\nORGANIZER;CN=IT Helpdesk:MAILTO:attacker@evil.invalid\r\nURL:https://evil.invalid";

    $escaped = $method->invoke($helper, $payload);

    expect($escaped)->not->toContain("\r");
    expect($escaped)->not->toContain("\n");

    // The text survives, as an escaped single line.
    expect($escaped)->toContain('Standup');
});

it('escapes the ics structural characters', function () {
    $helper = app(ActivityHelper::class);

    $method = new ReflectionMethod($helper, 'escapeICSValue');
    $method->setAccessible(true);

    expect($method->invoke($helper, 'a;b,c\\d'))->toBe('a\\;b\\,c\\\\d');
});

it('does not throw when a phone field arrives as an array', function () {
    $rule = new PhoneNumber;

    expect($rule->passes('persons.contact_numbers.0.value', ['1']))->toBeFalse();
});

it('still accepts a valid phone number', function () {
    $rule = new PhoneNumber;

    expect((bool) $rule->passes('persons.contact_numbers.0.value', '+15551234567'))->toBeTrue();
});

it('does not throw when a decimal field arrives as an array', function () {
    $failed = false;

    (new Decimal)->validate('leads.lead_value', ['1'], function () use (&$failed) {
        $failed = true;
    });

    expect($failed)->toBeTrue();
});

it('still accepts a valid decimal', function () {
    $failed = false;

    (new Decimal)->validate('leads.lead_value', '12.5000', function () use (&$failed) {
        $failed = true;
    });

    expect($failed)->toBeFalse();
});

/**
 * The CLI installer prompted for the administrator password with `text()`, which echoes what is
 * typed, and pre-filled a published default that an operator could accept by pressing Enter. It
 * then printed the chosen password to the console, where it lands in shell history and CI logs.
 */
it('prompts for the admin password without echoing it or offering a default', function () {
    $source = file_get_contents(
        base_path('packages/Webkul/Installer/src/Console/Commands/Installer.php')
    );

    // The prompt is the hidden one.
    expect($source)->toContain('$adminPassword = password(');

    // No published default, and no cleartext echo of what was chosen.
    expect($source)->not->toContain("default: 'admin123'");
    expect($source)->not->toContain("'Password: '.\$adminPassword");
});
