<?php

use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Webklex\PHPIMAP\Address;
use Webklex\PHPIMAP\Attribute;
use Webklex\PHPIMAP\Client;
use Webklex\PHPIMAP\ClientManager;
use Webkul\Email\InboundEmailProcessor\WebklexImapEmailProcessor;
use Webkul\Email\Models\Email;

uses(DatabaseTransactions::class);

/**
 * Build the processor through its real constructor with a client that never touches a server.
 */
function makeImapProcessor(): WebklexImapEmailProcessor
{
    $fakeClient = Mockery::mock(Client::class);
    $fakeClient->shouldReceive('connect')->andReturnSelf();
    $fakeClient->shouldReceive('isConnected')->andReturn(true);
    $fakeClient->shouldReceive('disconnect')->andReturnSelf();

    $fakeManager = new class($fakeClient) extends ClientManager
    {
        public function __construct(private Client $fakeClient)
        {
            // skip parent constructor
        }

        public function make(array $config): Client
        {
            return $this->fakeClient;
        }
    };

    app()->instance(ClientManager::class, $fakeManager);

    return app(WebklexImapEmailProcessor::class);
}

/**
 * A message shaped like the parts of Webklex' Message the processor reads.
 */
function makeImapMessage(array $attributes): object
{
    $date = Carbon::parse('2026-10-30 09:15:00');

    return new class($attributes, $date)
    {
        public array $bodies = ['text' => 'Hello'];

        public Attribute $date;

        public function __construct(private array $attributes, Carbon $date)
        {
            $this->date = new Attribute('date', $date);
        }

        public function getAttributes(): array
        {
            return $this->attributes;
        }

        public function getFolder(): object
        {
            return (object) ['name' => 'INBOX'];
        }

        public function flags(): object
        {
            return new class
            {
                public function has(string $flag): bool
                {
                    return false;
                }
            };
        }

        public function hasAttachments(): bool
        {
            return false;
        }
    };
}

function imapHeaders(array $extra = []): array
{
    $tag = bin2hex(random_bytes(4));

    return array_merge([
        'from' => new Attribute('from', new Address((object) ['mail' => "sender-$tag@example.invalid", 'personal' => 'Sender'])),
        'subject' => new Attribute('subject', "No id $tag"),
        'date' => new Attribute('date', Carbon::parse('2026-10-30 09:15:00')),
    ], $extra);
}

it('imports a message without message_id under a stable fallback id', function () {
    $headers = imapHeaders();
    $processor = makeImapProcessor();

    $processor->processMessage(makeImapMessage($headers));
    $processor->processMessage(makeImapMessage($headers));

    $email = Email::query()->where('subject', $headers['subject']->first())->get();

    expect($email)->toHaveCount(1)
        ->and($email->first()->message_id)->toMatch('/^[0-9a-f]{40}@krayin\.invalid$/')
        ->and($email->first()->unique_id)->toBe($email->first()->message_id);
});

it('treats a blank message_id like a missing one', function () {
    $headers = imapHeaders(['message_id' => new Attribute('message_id', '')]);
    $processor = makeImapProcessor();

    $processor->processMessage(makeImapMessage($headers));

    $email = Email::query()->where('subject', $headers['subject']->first())->first();

    expect($email)->not->toBeNull()
        ->and($email->message_id)->toMatch('/^[0-9a-f]{40}@krayin\.invalid$/');
});

it('keeps the message_id a message already has', function () {
    $headers = imapHeaders(['message_id' => new Attribute('message_id', 'real-id@example.invalid')]);

    makeImapProcessor()->processMessage(makeImapMessage($headers));

    expect(Email::query()->where('message_id', 'real-id@example.invalid')->exists())->toBeTrue();
});

it('gives different header-less messages different fallback ids', function () {
    $headers1 = imapHeaders();
    $headers2 = imapHeaders();
    $headers3 = imapHeaders(['message_id' => new Attribute('message_id', 'batch-test-id@example.invalid')]);

    $processor = makeImapProcessor();

    $processor->processMessage(makeImapMessage($headers1));
    $processor->processMessage(makeImapMessage($headers2));
    $processor->processMessage(makeImapMessage($headers3));

    $email1 = Email::query()->where('subject', $headers1['subject']->first())->first();
    $email2 = Email::query()->where('subject', $headers2['subject']->first())->first();
    $email3 = Email::query()->where('message_id', 'batch-test-id@example.invalid')->first();

    expect($email1)->not->toBeNull()
        ->and($email2)->not->toBeNull()
        ->and($email3)->not->toBeNull()
        ->and($email1->message_id)->toMatch('/^[0-9a-f]{40}@krayin\.invalid$/')
        ->and($email2->message_id)->toMatch('/^[0-9a-f]{40}@krayin\.invalid$/')
        ->and($email1->message_id)->not->toBe($email2->message_id)
        ->and($email3->message_id)->toBe('batch-test-id@example.invalid');
});
