<?php

use Illuminate\Support\Facades\URL;
use Webkul\Email\InboundEmailProcessor\Contracts\InboundEmailProcessor;

/**
 * The inbound parse endpoint is reachable without a session: the route drops the `user`
 * middleware and bootstrap/app.php exempts it from CSRF. Whatever the endpoint checks for
 * itself is therefore the only thing between the public internet and the CRM inbox.
 *
 * These tests assert the boundary rather than the parsing, so they do not need the mailparse
 * extension: a request that is not authorised must be turned away before the processor runs.
 */
beforeEach(function () {
    URL::forceRootUrl('http://localhost');

    /**
     * A stand-in for the real processor which records whether it was reached. Binding it also
     * keeps the raw message away from mailparse, which is an optional extension.
     */
    $this->processorReached = false;

    app()->bind(InboundEmailProcessor::class, function () {
        return new class($this) implements InboundEmailProcessor
        {
            public function __construct(private $test) {}

            public function processMessagesFromAllFolders() {}

            public function processMessage($content = null): void
            {
                $this->test->processorReached = true;
            }
        };
    });
});

function forgedEmail(): string
{
    return implode("\r\n", [
        'From: "Chief Executive" <ceo@victim.test>',
        'To: sales@victim.test',
        'Subject: Please wire the payment',
        'Message-ID: <'.bin2hex(random_bytes(8)).'@attacker.test>',
        '',
        'Account details attached.',
    ]);
}

it('rejects an inbound parse request that carries no token', function () {
    config(['mail-receiver.inbound_token' => 'the-configured-token']);

    $response = test()->post('/admin/mail/inbound-parse', ['email' => forgedEmail()]);

    expect($response->getStatusCode())->toBe(403);
    expect($this->processorReached)->toBeFalse();
});

it('rejects an inbound parse request that carries the wrong token', function () {
    config(['mail-receiver.inbound_token' => 'the-configured-token']);

    $response = test()->withHeader('X-Inbound-Token', 'not-the-token')
        ->post('/admin/mail/inbound-parse', ['email' => forgedEmail()]);

    expect($response->getStatusCode())->toBe(403);
    expect($this->processorReached)->toBeFalse();
});

it('refuses to accept anything while no token is configured', function () {
    config(['mail-receiver.inbound_token' => null]);

    $response = test()->post('/admin/mail/inbound-parse', ['email' => forgedEmail()]);

    expect($response->getStatusCode())->toBe(403);
    expect($this->processorReached)->toBeFalse();
});

it('accepts a request carrying the configured token', function () {
    config(['mail-receiver.inbound_token' => 'the-configured-token']);

    $response = test()->withHeader('X-Inbound-Token', 'the-configured-token')
        ->post('/admin/mail/inbound-parse', ['email' => forgedEmail()]);

    expect($response->getStatusCode())->toBe(200);
    expect($this->processorReached)->toBeTrue();
});
