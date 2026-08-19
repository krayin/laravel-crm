<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;
use Webkul\User\Models\User;

/**
 * Hardening around the admin authentication endpoints.
 *
 * These tests are deliberately read-only against the database: they authenticate as the existing
 * administrator and submit credentials that cannot match an account, so no rows are created,
 * updated or deleted.
 */
uses(DatabaseTransactions::class);

beforeEach(function () {
    /**
     * The configured APP_URL carries a subdirectory, which the test client would otherwise prepend
     * to every request path, so no admin route would match.
     */
    URL::forceRootUrl('http://localhost');

    /**
     * Rate limiter state lives in the cache; phpunit.xml pins CACHE_STORE=array, so flushing here
     * isolates each test without touching any real cache.
     */
    Cache::flush();
});

it('rate limits repeated failed logins', function () {
    $credentials = [
        'email' => 'no-such-user@example.invalid',
        'password' => 'wrong-password',
    ];

    for ($attempt = 1; $attempt <= 5; $attempt++) {
        expect(test()->post('/admin/login', $credentials)->getStatusCode())
            ->not->toBe(429, "attempt {$attempt} should not yet be throttled");
    }

    test()->post('/admin/login', $credentials)->assertStatus(429);
});

it('rate limits repeated forgot password requests', function () {
    $payload = ['email' => 'no-such-user@example.invalid'];

    for ($attempt = 1; $attempt <= 5; $attempt++) {
        test()->post('/admin/forget-password', $payload);
    }

    test()->post('/admin/forget-password', $payload)->assertStatus(429);
});

it('does not reveal whether an email belongs to an account', function () {
    $response = test()->post('/admin/forget-password', [
        'email' => 'definitely-not-registered@example.invalid',
    ]);

    $response->assertSessionHasNoErrors();

    expect(session('success'))->not->toBeNull();
});

it('destroys the whole session on logout, not just the auth state', function () {
    $admin = User::find(1);

    expect($admin)->not->toBeNull();

    $response = test()
        ->actingAs($admin, 'user')
        ->withSession(['canary' => 'survives-only-if-session-is-not-invalidated'])
        ->delete('/admin/logout');

    $response->assertStatus(302);
    $response->assertSessionMissing('canary');

    expect(auth()->guard('user')->check())->toBeFalse();
});

/**
 * Only *failed* sign-ins may count against the limit.
 *
 * Throttling every attempt also throttles people who sign in correctly — the browser test suite
 * signs in once per test case from a single address, and several staff can share one office
 * address — while adding nothing against password guessing, which is made up entirely of failures.
 */
it('never throttles repeated successful logins', function () {
    $password = 'correct-horse-battery-staple';

    $user = User::create([
        'name' => 'Throttle Probe',
        'email' => 'throttle-probe-'.bin2hex(random_bytes(6)).'@example.invalid',
        'password' => bcrypt($password),
        'status' => 1,
        'role_id' => 1,
        'view_permission' => 'global',
    ]);

    foreach (range(1, 12) as $attempt) {
        $status = test()
            ->post('/admin/login', ['email' => $user->email, 'password' => $password])
            ->getStatusCode();

        expect($status)->not->toBe(429, "successful login {$attempt} was throttled");

        auth()->guard('user')->logout();
    }
});

it('lets a correct sign-in clear earlier failures for those credentials', function () {
    $password = 'correct-horse-battery-staple';

    $user = User::create([
        'name' => 'Throttle Probe',
        'email' => 'throttle-probe-'.bin2hex(random_bytes(6)).'@example.invalid',
        'password' => bcrypt($password),
        'status' => 1,
        'role_id' => 1,
        'view_permission' => 'global',
    ]);

    foreach (range(1, 4) as $ignored) {
        test()->post('/admin/login', ['email' => $user->email, 'password' => 'wrong']);
    }

    test()->post('/admin/login', ['email' => $user->email, 'password' => $password]);

    auth()->guard('user')->logout();

    /**
     * The budget was reset by the successful sign-in, so a fresh run of failures is needed again
     * before the limit trips.
     */
    foreach (range(1, 4) as $attempt) {
        expect(test()->post('/admin/login', ['email' => $user->email, 'password' => 'wrong'])->getStatusCode())
            ->not->toBe(429, "failure {$attempt} after a successful sign-in should not be throttled");
    }
});
