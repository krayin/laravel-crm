<?php

use Webkul\Admin\Services\NotificationDriver;

/**
 * Polling is the guaranteed fallback.
 *
 * Naming a socket driver is not the same as having set one up. A driver whose credentials are
 * missing, or whose broadcast connection points somewhere else, would publish into the void and
 * leave the bell silently dead — so anything short of a complete setup resolves to polling.
 */
function configureNotificationReverb(bool $complete = true, string $broadcast = 'reverb'): void
{
    config([
        'notifications.driver' => 'reverb',
        'broadcasting.default' => $broadcast,
        'broadcasting.connections.reverb.app_id' => '1',
        'broadcasting.connections.reverb.key' => $complete ? 'key' : null,
        'broadcasting.connections.reverb.secret' => 'secret',
        'broadcasting.connections.reverb.options.host' => 'localhost',
    ]);
}

it('uses polling when polling is chosen', function () {
    config(['notifications.driver' => 'polling']);

    expect(NotificationDriver::resolve())->toBe('polling');
});

it('uses polling when no driver is set', function () {
    config(['notifications.driver' => '']);

    expect(NotificationDriver::resolve())->toBe('polling');
});

it('uses polling when the driver is not one it knows', function () {
    config(['notifications.driver' => 'carrier-pigeon']);

    expect(NotificationDriver::resolve())->toBe('polling');
});

it('uses polling when a socket driver is missing its credentials', function () {
    configureNotificationReverb(complete: false);

    expect(NotificationDriver::resolve())->toBe('polling');
});

/**
 * The application has to publish to the same service the browser is listening on.
 */
it('uses polling when broadcasting points somewhere else', function () {
    configureNotificationReverb(broadcast: 'log');

    expect(NotificationDriver::resolve())->toBe('polling');
});

it('uses reverb once it is completely set up', function () {
    configureNotificationReverb();

    expect(NotificationDriver::resolve())->toBe('reverb');
});

it('uses pusher once it is completely set up', function () {
    config([
        'notifications.driver' => 'pusher',
        'broadcasting.default' => 'pusher',
        'broadcasting.connections.pusher.app_id' => '1',
        'broadcasting.connections.pusher.key' => 'key',
        'broadcasting.connections.pusher.secret' => 'secret',
        'broadcasting.connections.pusher.options.cluster' => 'mt1',
    ]);

    expect(NotificationDriver::resolve())->toBe('pusher');
});

it('tells the administrator which variables are missing', function () {
    configureNotificationReverb(complete: false);

    expect(NotificationDriver::fallbackReason())->toContain('REVERB_APP_KEY');
});

it('tells the administrator when broadcasting points somewhere else', function () {
    configureNotificationReverb(broadcast: 'log');

    expect(NotificationDriver::fallbackReason())->toContain('BROADCAST_DRIVER');
});

it('says nothing when the chosen driver is the one in use', function () {
    configureNotificationReverb();

    expect(NotificationDriver::fallbackReason())->toBeNull();
});

/**
 * A very short interval would make a request a second per signed-in user.
 */
/**
 * The browser is handed its connection settings by the server, so what that payload contains is
 * the whole of what a visitor can read. The application secret authenticates the server when it
 * publishes and is never needed to subscribe, so it must not be in there.
 */
it('gives the browser nothing while polling', function () {
    config(['notifications.driver' => 'polling']);

    expect(NotificationDriver::clientConfig())->toBeNull();
});

it('gives the browser nothing when the driver is not usable', function () {
    configureNotificationReverb(complete: false);

    expect(NotificationDriver::clientConfig())->toBeNull();
});

it('never puts the application secret in front of the browser', function () {
    configureNotificationReverb();

    config(['broadcasting.connections.reverb.secret' => 'super-secret-value']);

    $payload = json_encode(NotificationDriver::clientConfig());

    expect($payload)->not->toContain('super-secret-value');
});

it('gives the browser only what it needs to subscribe', function () {
    configureNotificationReverb();

    expect(array_keys(NotificationDriver::clientConfig()))
        ->toBe(['connection', 'key', 'host', 'port', 'forceTLS', 'cluster']);
});

/**
 * Pusher is reached through a cluster rather than a host of its own, so requiring a host left a
 * correctly configured Pusher install with no settings to hand the browser, which then fell back
 * to polling with nothing to explain why.
 */
it('gives the browser pusher settings when only a cluster is set', function () {
    config([
        'notifications.driver' => 'pusher',
        'broadcasting.default' => 'pusher',
        'broadcasting.connections.pusher.app_id' => '1',
        'broadcasting.connections.pusher.key' => 'key',
        'broadcasting.connections.pusher.secret' => 'secret',
        'broadcasting.connections.pusher.options' => ['cluster' => 'mt1', 'useTLS' => true],
    ]);

    $config = NotificationDriver::clientConfig();

    expect($config)->not->toBeNull()
        ->and($config['connection'])->toBe('pusher')
        ->and($config['cluster'])->toBe('mt1')
        ->and($config['forceTLS'])->toBeTrue();
});

it('gives the browser nothing when there is no address to reach', function () {
    config([
        'notifications.driver' => 'pusher',
        'broadcasting.default' => 'pusher',
        'broadcasting.connections.pusher.key' => 'key',
        'broadcasting.connections.pusher.options' => [],
    ]);

    expect(NotificationDriver::clientConfig())->toBeNull();
});

/**
 * Reverb runs over plain http on a local setup, so its scheme has to be honoured rather than
 * assuming TLS the way Pusher can.
 */
it('honours reverb running without tls', function () {
    configureNotificationReverb();

    config(['broadcasting.connections.reverb.options.scheme' => 'http']);

    expect(NotificationDriver::clientConfig()['forceTLS'])->toBeFalse();
});

it('keeps the poll interval above a floor', function () {
    config(['notifications.poll_interval' => 1]);

    expect(NotificationDriver::pollInterval())->toBe(5);
});

it('honours a sensible poll interval', function () {
    config(['notifications.poll_interval' => 45]);

    expect(NotificationDriver::pollInterval())->toBe(45);
});
