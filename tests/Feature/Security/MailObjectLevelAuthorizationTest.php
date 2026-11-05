<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Event;
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

function makeOwnedPerson(User $owner): Person
{
    return Person::create([
        'name' => 'Owned Person '.bin2hex(random_bytes(4)),
        'user_id' => $owner->id,
        'emails' => [['value' => 'owned-'.bin2hex(random_bytes(4)).'@example.invalid', 'label' => 'work']],
    ]);
}

function makeOwnedLead(User $owner, Person $person): Lead
{
    $pipeline = securityTestPipeline();

    return Lead::create([
        'title' => 'Owned Lead '.bin2hex(random_bytes(4)),
        'user_id' => $owner->id,
        'person_id' => $person->id,
        'lead_pipeline_id' => $pipeline->id,
        'lead_pipeline_stage_id' => $pipeline->stages()->value('id'),
    ]);
}

/**
 * A mail linked to a lead owned by `$owner`, i.e. the records the restricted agent must not reach.
 */
function makeOwnedMail(User $owner): Email
{
    $person = makeOwnedPerson($owner);

    $lead = makeOwnedLead($owner, $person);

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

/**
 * Broken object level authorization through the mail links.
 *
 * `store()` and `update()` saved `lead_id`, `person_id` and `parent_id` from the request unchecked,
 * and the mail resource returns the linked lead, person and parent mail in full. A mail was
 * reachable when any one of its owners was in scope, so linking the caller's own person next to
 * someone else's lead and parent mail made the draft readable — and with it the other records.
 */
function draftPayload(array $links = []): array
{
    return [
        'is_draft' => 1,
        'subject' => 'Draft subject',
        'reply' => 'Draft body',
        'reply_to' => ['recipient@example.invalid'],
        ...$links,
    ];
}

function makeReply(Email $parent): Email
{
    return Email::create([
        'subject' => 'Re: '.$parent->subject,
        'reply' => 'A reply stored under the thread root.',
        'source' => 'email',
        'user_type' => 'person',
        'folders' => ['inbox'],
        'name' => 'Owner Contact',
        'unique_id' => bin2hex(random_bytes(8)),
        'message_id' => bin2hex(random_bytes(8)).'@example.invalid',
        'parent_id' => $parent->id,
    ]);
}

it('refuses a draft linking another user lead and parent mail next to the caller own person', function () {
    $owner = makeMailUser('global');
    $agent = makeMailUser('individual');

    $victimMail = makeOwnedMail($owner);
    $ownPerson = makeOwnedPerson($agent);

    $count = Email::query()->count();

    $response = test()
        ->actingAs($agent, 'user')
        ->post('/admin/mail/create', draftPayload([
            'parent_id' => $victimMail->id,
            'lead_id' => $victimMail->lead_id,
            'person_id' => $ownPerson->id,
        ]));

    expect($response->getStatusCode())->toBe(401)
        ->and(Email::query()->count())->toBe($count);
});

it('refuses to link a new mail to a record outside the caller scope', function (string $field) {
    $owner = makeMailUser('global');
    $agent = makeMailUser('individual');

    $victimMail = makeOwnedMail($owner);

    $links = [
        'lead_id' => $victimMail->lead_id,
        'person_id' => $victimMail->person_id,
        'parent_id' => $victimMail->id,
    ];

    $count = Email::query()->count();

    $response = test()
        ->actingAs($agent, 'user')
        ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
        ->post('/admin/mail/create', draftPayload([$field => $links[$field]]));

    expect($response->getStatusCode())->toBe(401)
        ->and(Email::query()->count())->toBe($count)
        ->and($response->getContent())->not->toContain('The agreed figure is 48,000 for the year.');
})->with(['lead_id', 'person_id', 'parent_id']);

it('lets a restricted user file a new mail under their own records', function () {
    $agent = makeMailUser('individual');

    $ownPerson = makeOwnedPerson($agent);
    $ownLead = makeOwnedLead($agent, $ownPerson);
    $sharedMail = makeUnlinkedMail();

    test()
        ->actingAs($agent, 'user')
        ->post('/admin/mail/create', draftPayload([
            'parent_id' => $sharedMail->id,
            'lead_id' => $ownLead->id,
            'person_id' => $ownPerson->id,
        ]))
        ->assertRedirect();

    $email = Email::query()->latest('id')->first();

    expect($email->lead_id)->toBe($ownLead->id)
        ->and($email->person_id)->toBe($ownPerson->id)
        ->and($email->parent_id)->toBe($sharedMail->id);
});

it('lets a global user link a new mail to any record', function () {
    $owner = makeMailUser('individual');
    $manager = makeMailUser('global');

    $ownedMail = makeOwnedMail($owner);

    test()
        ->actingAs($manager, 'user')
        ->post('/admin/mail/create', draftPayload([
            'parent_id' => $ownedMail->id,
            'lead_id' => $ownedMail->lead_id,
            'person_id' => $ownedMail->person_id,
        ]))
        ->assertRedirect();

    expect(Email::query()->latest('id')->first()->lead_id)->toBe($ownedMail->lead_id);
});

it('refuses to link an existing mail to another user lead', function () {
    $owner = makeMailUser('global');
    $agent = makeMailUser('individual');

    $victimMail = makeOwnedMail($owner);
    $sharedMail = makeUnlinkedMail();

    $response = test()
        ->actingAs($agent, 'user')
        ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
        ->put('/admin/mail/edit/'.$sharedMail->id, ['lead_id' => $victimMail->lead_id]);

    expect($response->getStatusCode())->toBe(401)
        ->and($sharedMail->fresh()->lead_id)->toBeNull()
        ->and($response->getContent())->not->toContain($victimMail->lead->title);
});

it('lets a restricted user link an existing mail to their own lead', function () {
    $agent = makeMailUser('individual');

    $ownLead = makeOwnedLead($agent, makeOwnedPerson($agent));
    $sharedMail = makeUnlinkedMail();

    test()
        ->actingAs($agent, 'user')
        ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
        ->put('/admin/mail/edit/'.$sharedMail->id, ['lead_id' => $ownLead->id])
        ->assertOk();

    expect($sharedMail->fresh()->lead_id)->toBe($ownLead->id);
});

/**
 * Mail linked before the write check existed may already mix the caller's records with another
 * user's; reaching it must require every one of them to be in scope.
 */
it('refuses a mail linked to the caller own person and another user lead', function () {
    $owner = makeMailUser('global');
    $agent = makeMailUser('individual');

    $victimMail = makeOwnedMail($owner);

    $mixedMail = makeUnlinkedMail();

    $mixedMail->update([
        'lead_id' => $victimMail->lead_id,
        'person_id' => makeOwnedPerson($agent)->id,
        'parent_id' => $victimMail->id,
    ]);

    $response = test()
        ->actingAs($agent, 'user')
        ->get('/admin/mail/draft/'.$mixedMail->id);

    expect($response->getStatusCode())->toBe(401)
        ->and($response->getContent())
        ->not->toContain($victimMail->lead->title)
        ->not->toContain('The agreed figure is 48,000 for the year.');

    test()
        ->actingAs($agent, 'user')
        ->post('/admin/mail/mass-update', [
            'indices' => [$mixedMail->id],
            'value' => 'trash',
            'folders' => ['trash'],
        ]);

    expect($mixedMail->fresh()->folders)->toBe(['inbox']);
});

/**
 * Replies are stored unlinked under the thread root, and unlinked mail is shared, so opening a
 * reply returned the root — subject, body and linked lead — as its `parent`.
 */
it('refuses a reply in a thread linked to another user record', function () {
    $owner = makeMailUser('global');
    $agent = makeMailUser('individual');

    $reply = makeReply(makeOwnedMail($owner));

    $response = test()
        ->actingAs($agent, 'user')
        ->get('/admin/mail/draft/'.$reply->id);

    expect($response->getStatusCode())->toBe(401)
        ->and($response->getContent())->not->toContain('The agreed figure is 48,000 for the year.');
});

it('still lets the owner open a reply in their own thread', function () {
    $owner = makeMailUser('individual');

    $reply = makeReply(makeOwnedMail($owner));

    test()
        ->actingAs($owner, 'user')
        ->get('/admin/mail/draft/'.$reply->id)
        ->assertOk()
        ->assertJsonPath('data.parent.reply', 'The agreed figure is 48,000 for the year.');
});

it('still lets a restricted user open a reply in a shared thread', function () {
    $agent = makeMailUser('individual');

    $reply = makeReply(makeUnlinkedMail());

    test()
        ->actingAs($agent, 'user')
        ->get('/admin/mail/draft/'.$reply->id)
        ->assertOk();
});

/**
 * Missing authorization in the mail listing.
 *
 * `EmailDataGrid` listed every mail in a folder — subject and full body — with no data-scope filter,
 * so the list (and its export) handed out what `view()` refused.
 */
function listedMailIds(User $user, string $token): array
{
    return collect(
        test()
            ->actingAs($user, 'user')
            ->getJson('/admin/mail/inbox?'.http_build_query(['filters' => ['all' => [$token]]]), [
                'X-Requested-With' => 'XMLHttpRequest',
            ])
            ->assertOk()
            ->json('records')
    )->pluck('id')->all();
}

/**
 * One mail of each kind, tagged with a token so the listing can be narrowed to them.
 */
function mailOfEveryScope(User $owner, User $agent, string $token): array
{
    $foreign = makeOwnedMail($owner);

    $mixed = makeUnlinkedMail();

    $mixed->update([
        'lead_id' => $foreign->lead_id,
        'person_id' => makeOwnedPerson($agent)->id,
    ]);

    $own = makeUnlinkedMail();

    $own->update(['person_id' => makeOwnedPerson($agent)->id]);

    $shared = makeUnlinkedMail();

    $mails = compact('foreign', 'mixed', 'own', 'shared');

    foreach ($mails as $mail) {
        $mail->update(['subject' => $token.' '.$mail->id]);
    }

    return array_map(fn ($mail) => $mail->id, $mails);
}

it('lists only the mail inside the caller scope', function () {
    $owner = makeMailUser('global');
    $agent = makeMailUser('individual');

    $token = 'scope-'.bin2hex(random_bytes(6));

    $mails = mailOfEveryScope($owner, $agent, $token);

    $listed = listedMailIds($agent, $token);

    expect($listed)->toContain($mails['own'], $mails['shared'])
        ->not->toContain($mails['foreign'])
        ->not->toContain($mails['mixed']);
});

it('lists only shared mail to a group user who belongs to no group', function () {
    $owner = makeMailUser('global');
    $agent = makeMailUser('group');

    $token = 'scope-'.bin2hex(random_bytes(6));

    $mails = mailOfEveryScope($owner, makeMailUser('individual'), $token);

    expect(listedMailIds($agent, $token))->toBe([$mails['shared']]);
});

it('still lists every mail to a global user', function () {
    $owner = makeMailUser('individual');
    $manager = makeMailUser('global');

    $token = 'scope-'.bin2hex(random_bytes(6));

    $mails = mailOfEveryScope($owner, makeMailUser('individual'), $token);

    expect(listedMailIds($manager, $token))->toEqualCanonicalizing(array_values($mails));
});

/**
 * Broken object level authorization in the lead and person create listeners.
 *
 * Creating a lead or person with an `email_id` in the request filed that mail under the new record,
 * whoever owned the mail — from the admin panel, and from the public web form without signing in.
 */
function createWithEmailId(string $event, $record, Email $email): void
{
    request()->merge(['email_id' => $email->id]);

    Event::dispatch($event, $record);
}

it('does not file another user mail under a person the caller creates', function () {
    $owner = makeMailUser('global');
    $agent = makeMailUser('individual');

    $victimMail = makeUnlinkedMail();

    $victimMail->update(['lead_id' => makeOwnedLead($owner, makeOwnedPerson($owner))->id]);

    test()->actingAs($agent, 'user');

    createWithEmailId('contacts.person.create.after', makeOwnedPerson($agent), $victimMail);

    expect($victimMail->fresh()->person_id)->toBeNull();
});

it('does not file another user mail under a lead the caller creates', function () {
    $owner = makeMailUser('global');
    $agent = makeMailUser('individual');

    $victimMail = makeUnlinkedMail();

    $victimMail->update(['person_id' => makeOwnedPerson($owner)->id]);

    test()->actingAs($agent, 'user');

    createWithEmailId('lead.create.after', makeOwnedLead($agent, makeOwnedPerson($agent)), $victimMail);

    expect($victimMail->fresh()->lead_id)->toBeNull();
});

it('does not file mail under a new record when nobody is signed in', function (string $event, string $column) {
    $email = makeUnlinkedMail();

    $owner = makeMailUser('global');

    $person = makeOwnedPerson($owner);

    $record = $event === 'lead.create.after' ? makeOwnedLead($owner, $person) : $person;

    createWithEmailId($event, $record, $email);

    expect($email->fresh()->{$column})->toBeNull();
})->with([
    'lead' => ['lead.create.after', 'lead_id'],
    'person' => ['contacts.person.create.after', 'person_id'],
]);

it('still files a reachable mail under a record the caller creates', function (string $event, string $column) {
    $agent = makeMailUser('individual');

    $email = makeUnlinkedMail();

    $person = makeOwnedPerson($agent);

    $record = $event === 'lead.create.after' ? makeOwnedLead($agent, $person) : $person;

    test()->actingAs($agent, 'user');

    createWithEmailId($event, $record, $email);

    expect($email->fresh()->{$column})->toBe($record->id);
})->with([
    'lead' => ['lead.create.after', 'lead_id'],
    'person' => ['contacts.person.create.after', 'person_id'],
]);

/**
 * A lead or person with no owner is refused on its own page, so a mail linked to one must not hand
 * it to a restricted user either.
 */
it('refuses a mail linked to a person with no owner to a restricted user', function () {
    $agent = makeMailUser('individual');
    $manager = makeMailUser('global');

    $token = 'scope-'.bin2hex(random_bytes(6));

    $unowned = Person::create([
        'name' => 'Unowned Person',
        'emails' => [['value' => 'unowned-'.bin2hex(random_bytes(4)).'@example.invalid', 'label' => 'work']],
    ]);

    $mail = makeUnlinkedMail();

    $mail->update(['person_id' => $unowned->id, 'subject' => $token]);

    expect(test()->actingAs($agent, 'user')->get('/admin/mail/draft/'.$mail->id)->getStatusCode())->toBe(401)
        ->and(listedMailIds($agent, $token))->toBe([])
        ->and(listedMailIds($manager, $token))->toBe([$mail->id]);
});

it('lists a group user who belongs to no group their own mail', function () {
    $agent = makeMailUser('group');

    $token = 'scope-'.bin2hex(random_bytes(6));

    $own = makeUnlinkedMail();

    $own->update(['person_id' => makeOwnedPerson($agent)->id, 'subject' => $token]);

    expect(listedMailIds($agent, $token))->toBe([$own->id]);
});
