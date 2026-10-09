<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Webkul\Contact\Models\Person;
use Webkul\Email\Models\Email;
use Webkul\Lead\Models\Lead;
use Webkul\User\Models\Role;
use Webkul\User\Models\User;

/**
 * The mail read path (`view`, `download`) scopes a linked mail to whoever owns the lead or person
 * it is attached to. The write path did not: `update`, `destroy`, `massUpdate` and `massDestroy`
 * acted on any id supplied by the caller, so a user restricted to their own records could edit,
 * trash or permanently delete another user's mail — and `update` returned the full mail resource,
 * disclosing the subject and body that `view` would have refused.
 *
 * These tests pin the object-level check on every write action, and keep the shared-mailbox
 * behaviour that unlinked mail stays writable for everyone with mail access.
 */
uses(DatabaseTransactions::class);

function makeMailUser(string $viewPermission): User
{
    $role = Role::create([
        'name' => 'Mail Scope Role '.bin2hex(random_bytes(6)),
        'description' => 'Created by the mail object level authorization tests.',
        'permission_type' => 'all',
    ]);

    return User::create([
        'name' => 'Mail Scope Probe',
        'email' => 'mail-scope-'.bin2hex(random_bytes(6)).'@example.invalid',
        'password' => bcrypt('correct-horse-battery-staple'),
        'status' => 1,
        'role_id' => $role->id,
        'view_permission' => $viewPermission,
    ]);
}

/**
 * A mail linked to a lead owned by `$owner`, i.e. the records the restricted agent must not reach.
 */
function makeOwnedMail(User $owner): Email
{
    $person = Person::create([
        'name' => 'Owned Person '.bin2hex(random_bytes(4)),
        'user_id' => $owner->id,
        'emails' => [['value' => 'owned-'.bin2hex(random_bytes(4)).'@example.invalid', 'label' => 'work']],
    ]);

    $lead = Lead::create([
        'title' => 'Owned Lead '.bin2hex(random_bytes(4)),
        'user_id' => $owner->id,
        'person_id' => $person->id,
        'lead_pipeline_id' => 1,
        'lead_pipeline_stage_id' => 1,
    ]);

    return Email::create([
        'subject' => 'Confidential pricing for the renewal',
        'reply' => 'The agreed figure is 48,000 for the year.',
        'source' => 'email',
        'user_type' => 'person',
        'folders' => ['inbox'],
        'name' => 'Owner Contact',
        'unique_id' => bin2hex(random_bytes(8)),
        'message_id' => bin2hex(random_bytes(8)).'@example.invalid',
        'lead_id' => $lead->id,
        'person_id' => $person->id,
    ]);
}

/**
 * A mail attached to no lead or person. The mailbox is shared, so this stays writable.
 */
function makeUnlinkedMail(): Email
{
    return Email::create([
        'subject' => 'Shared mailbox enquiry',
        'reply' => 'Sent to the general address.',
        'source' => 'email',
        'user_type' => 'person',
        'folders' => ['inbox'],
        'name' => 'Anonymous Sender',
        'unique_id' => bin2hex(random_bytes(8)),
        'message_id' => bin2hex(random_bytes(8)).'@example.invalid',
    ]);
}

it('refuses to update a mail linked to another user record', function () {
    $owner = makeMailUser('global');
    $agent = makeMailUser('individual');

    $email = makeOwnedMail($owner);

    $response = test()
        ->actingAs($agent, 'user')
        ->put('/admin/mail/edit/'.$email->id, ['is_read' => 1]);

    expect($response->getStatusCode())->toBe(401);

    expect($email->fresh()->is_read)->not->toBe(1);
});

it('does not disclose the mail body through the update response', function () {
    $owner = makeMailUser('global');
    $agent = makeMailUser('individual');

    $email = makeOwnedMail($owner);

    /**
     * `update()` only returns the mail resource on an ajax request, so the disclosure the report
     * describes is only reachable that way — a plain request is answered with a redirect and would
     * contain no body either way.
     */
    $response = test()
        ->actingAs($agent, 'user')
        ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
        ->put('/admin/mail/edit/'.$email->id, ['is_read' => 1]);

    expect($response->getStatusCode())->toBe(401);

    expect($response->getContent())
        ->not->toContain('Confidential pricing for the renewal')
        ->not->toContain('The agreed figure is 48,000 for the year.');
});

it('refuses to trash a mail linked to another user record', function () {
    $owner = makeMailUser('global');
    $agent = makeMailUser('individual');

    $email = makeOwnedMail($owner);

    $response = test()
        ->actingAs($agent, 'user')
        ->delete('/admin/mail/'.$email->id, ['type' => 'trash']);

    expect($response->getStatusCode())->toBe(401);

    expect($email->fresh()->folders)->toBe(['inbox']);
});

it('refuses to permanently delete a mail linked to another user record', function () {
    $owner = makeMailUser('global');
    $agent = makeMailUser('individual');

    $email = makeOwnedMail($owner);

    test()
        ->actingAs($agent, 'user')
        ->delete('/admin/mail/'.$email->id, ['type' => 'delete']);

    expect(Email::find($email->id))->not->toBeNull();
});

it('skips out-of-scope mail in a mass destroy instead of trashing it', function () {
    $owner = makeMailUser('global');
    $agent = makeMailUser('individual');

    $email = makeOwnedMail($owner);

    test()
        ->actingAs($agent, 'user')
        ->post('/admin/mail/mass-destroy', [
            'type' => 'trash',
            'indices' => [$email->id],
        ]);

    expect($email->fresh()->folders)->toBe(['inbox']);
});

it('skips out-of-scope mail in a mass update instead of moving it', function () {
    $owner = makeMailUser('global');
    $agent = makeMailUser('individual');

    $email = makeOwnedMail($owner);

    /**
     * `value` is required by MassUpdateRequest; without it the request fails validation and never
     * reaches the controller, so the test would pass without proving anything.
     */
    $response = test()
        ->actingAs($agent, 'user')
        ->post('/admin/mail/mass-update', [
            'indices' => [$email->id],
            'value' => 'trash',
            'folders' => ['trash'],
        ]);

    expect($response->getStatusCode())->not->toBe(422);

    expect($email->fresh()->folders)->toBe(['inbox']);
});

/**
 * The body parameter used to win over the path parameter, so a request authorized against one mail
 * could be redirected onto another.
 */
it('ignores an id in the request body and uses the path id', function () {
    $owner = makeMailUser('global');
    $agent = makeMailUser('individual');

    $ownedMail = makeOwnedMail($owner);
    $reachableMail = makeUnlinkedMail();

    test()
        ->actingAs($agent, 'user')
        ->put('/admin/mail/edit/'.$reachableMail->id, [
            'id' => $ownedMail->id,
            'is_read' => 1,
        ]);

    expect($ownedMail->fresh()->is_read)->not->toBe(1);
});

it('still allows a restricted user to act on unlinked shared mail', function () {
    $agent = makeMailUser('individual');

    $email = makeUnlinkedMail();

    $response = test()
        ->actingAs($agent, 'user')
        ->delete('/admin/mail/'.$email->id, ['type' => 'trash']);

    expect($response->getStatusCode())->not->toBe(401);

    expect($email->fresh()->folders)->toBe(['trash']);
});

it('still allows a global user to act on any mail', function () {
    $owner = makeMailUser('global');
    $other = makeMailUser('global');

    $email = makeOwnedMail($owner);

    $response = test()
        ->actingAs($other, 'user')
        ->delete('/admin/mail/'.$email->id, ['type' => 'trash']);

    expect($response->getStatusCode())->not->toBe(401);

    expect($email->fresh()->folders)->toBe(['trash']);
});
