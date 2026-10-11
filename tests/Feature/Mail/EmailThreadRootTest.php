<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Webkul\Email\Models\Email;
use Webkul\Email\Repositories\EmailRepository;

/**
 * The mail list only shows root emails and the thread view only renders the root's direct
 * children, so an inbound reply to a reply must be attached to the root of the thread. The
 * inbound processors resolve the matched email to its root through the repository before
 * attaching the new message.
 */
uses(DatabaseTransactions::class);

function makeThreadEmail(?Email $parent = null): Email
{
    $messageId = bin2hex(random_bytes(8)).'@example.invalid';

    return Email::create([
        'subject' => 'Thread root lookup',
        'source' => 'email',
        'user_type' => 'person',
        'name' => 'Thread Probe',
        'reply' => 'Body',
        'from' => 'thread-probe@example.invalid',
        'folders' => ['inbox'],
        'unique_id' => $messageId,
        'message_id' => $messageId,
        'reference_ids' => [$messageId],
        'parent_id' => $parent?->id,
    ]);
}

it('returns the email itself when it is the root of the thread', function () {
    $root = makeThreadEmail();

    expect(app(EmailRepository::class)->findThreadRoot($root)->id)->toBe($root->id);
});

it('returns the root for a direct reply', function () {
    $root = makeThreadEmail();

    $reply = makeThreadEmail($root);

    expect(app(EmailRepository::class)->findThreadRoot($reply)->id)->toBe($root->id);
});

it('returns the root for a reply nested several levels deep', function () {
    $root = makeThreadEmail();

    $reply = makeThreadEmail(makeThreadEmail(makeThreadEmail($root)));

    expect(app(EmailRepository::class)->findThreadRoot($reply)->id)->toBe($root->id);
});
