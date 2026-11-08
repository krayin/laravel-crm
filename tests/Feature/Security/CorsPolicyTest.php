<?php

use Illuminate\Testing\TestResponse;

/**
 * Permissive CORS policy.
 *
 * The policy answered every origin with `Access-Control-Allow-Origin: *`, and targeted the old
 * `admin/web-forms/forms/*` path from 1.x rather than the public web form routes. Only origins listed
 * in CORS_ALLOWED_ORIGINS may now read web form responses cross-origin, credentials are never
 * allowed, and nothing outside the public web form endpoints is CORS-enabled.
 */
function sendCorsPreflight(string $uri, string $origin): TestResponse
{
    return test()->call('OPTIONS', $uri, [], [], [], [
        'HTTP_ORIGIN' => $origin,
        'HTTP_ACCESS_CONTROL_REQUEST_METHOD' => 'POST',
    ]);
}

it('ships a policy without wildcards, credentials or admin paths', function (string $file) {
    $policy = require $file;

    expect($policy['paths'])->toBe(['web-forms/forms/*'])
        ->and($policy['allowed_origins'])->not->toContain('*')
        ->and($policy['allowed_methods'])->not->toContain('*')
        ->and($policy['allowed_headers'])->not->toContain('*')
        ->and($policy['supports_credentials'])->toBeFalse();
})->with([
    'published config' => [dirname(__DIR__, 3).'/config/cors.php'],
    'package template' => [dirname(__DIR__, 3).'/packages/Webkul/Core/src/Config/cors.php'],
]);

it('ignores a bare wildcard in CORS_ALLOWED_ORIGINS', function () {
    $previous = $_SERVER['CORS_ALLOWED_ORIGINS'] ?? null;

    $_SERVER['CORS_ALLOWED_ORIGINS'] = '*, https://www.example.com';

    try {
        $policy = require base_path('config/cors.php');
    } finally {
        if ($previous === null) {
            unset($_SERVER['CORS_ALLOWED_ORIGINS']);
        } else {
            $_SERVER['CORS_ALLOWED_ORIGINS'] = $previous;
        }
    }

    expect($policy['allowed_origins'])->toBe(['https://www.example.com']);
});

it('answers no origin when none is trusted', function () {
    config(['cors.allowed_origins' => []]);

    sendCorsPreflight('/web-forms/forms/1', 'https://attacker.example')
        ->assertHeaderMissing('Access-Control-Allow-Origin')
        ->assertHeaderMissing('Access-Control-Allow-Credentials');
});

it('allows a configured origin on the web form endpoints', function () {
    config(['cors.allowed_origins' => ['https://www.example.com', 'https://shop.example.com']]);

    sendCorsPreflight('/web-forms/forms/1', 'https://shop.example.com')
        ->assertHeader('Access-Control-Allow-Origin', 'https://shop.example.com')
        ->assertHeaderMissing('Access-Control-Allow-Credentials');
});

it('never grants an untrusted origin or a wildcard', function (array $allowedOrigins) {
    config(['cors.allowed_origins' => $allowedOrigins]);

    $allowOrigin = sendCorsPreflight('/web-forms/forms/1', 'https://attacker.example')
        ->headers
        ->get('Access-Control-Allow-Origin');

    expect($allowOrigin)
        ->not->toBe('https://attacker.example')
        ->not->toBe('*');
})->with([
    'one trusted origin' => [['https://www.example.com']],
    'several trusted origins' => [['https://www.example.com', 'https://shop.example.com']],
]);

it('does not enable CORS outside the public web form endpoints', function (string $uri) {
    config(['cors.allowed_origins' => ['https://www.example.com']]);

    sendCorsPreflight($uri, 'https://www.example.com')
        ->assertHeaderMissing('Access-Control-Allow-Origin');
})->with([
    'the 1.x admin web form path' => ['/admin/web-forms/forms/1'],
    'the authenticated web form view' => ['/web-forms/form/1/form.html'],
    'the admin panel' => ['/admin/leads'],
]);
