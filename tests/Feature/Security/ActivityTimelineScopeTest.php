<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Webkul\Activity\Models\Activity;
use Webkul\Contact\Repositories\PersonRepository;
use Webkul\Email\Models\Email;
use Webkul\Lead\Models\Lead;
use Webkul\Product\Models\Product;
use Webkul\User\Models\Role;
use Webkul\User\Models\User;

uses(DatabaseTransactions::class);

/**
 * A record's activity timeline is held to the same data scope as the record itself.
 *
 * The timeline was built from the id in the url alone, so a user whose scope covers only their own
 * records could still read another user's notes and — for leads and people — the subject, body and
 * recipients of their correspondence, even while the record's own page and the mailbox both refused
 * to show it.
 */
function timelineScopeUser(string $viewPermission): User
{
    $role = Role::create([
        'name' => 'timeline-'.uniqid(),
        'description' => 'test',
        'permission_type' => 'all',
    ]);

    return User::create([
        'name' => 'Timeline User',
        'email' => uniqid().'@example.com',
        'password' => bcrypt('password'),
        'status' => 1,
        'view_permission' => $viewPermission,
        'role_id' => $role->id,
    ]);
}

function timelineLeadOwnedBy(User $owner, string $note): Lead
{
    $pipeline = securityTestPipeline();

    $person = app(PersonRepository::class)->create([
        'name' => 'Timeline Person '.uniqid(),
        'entity_type' => 'persons',
        'emails' => [['value' => uniqid().'@mail.invalid', 'label' => 'work']],
        'user_id' => $owner->id,
    ]);

    $lead = Lead::create([
        'title' => 'Timeline Lead '.uniqid(),
        'user_id' => $owner->id,
        'person_id' => $person->id,
        'lead_pipeline_id' => $pipeline->id,
        'lead_pipeline_stage_id' => $pipeline->stages()->value('id'),
    ]);

    $activity = Activity::create([
        'title' => 'Private note',
        'type' => 'note',
        'comment' => $note,
        'user_id' => $owner->id,
        'is_done' => 1,
    ]);

    $lead->activities()->attach($activity->id);

    return $lead;
}

it('refuses a lead timeline to a user outside its data scope', function () {
    $owner = timelineScopeUser('global');

    $outsider = timelineScopeUser('individual');

    $note = 'TIMELINE-SECRET-'.bin2hex(random_bytes(4));

    $lead = timelineLeadOwnedBy($owner, $note);

    $response = $this->actingAs($outsider, 'user')
        ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
        ->get(route('admin.leads.activities.index', $lead->id).'?type=all');

    expect($response->status())->toBe(401);

    expect($response->getContent())->not->toContain($note);
});

/**
 * The record's own page already refuses this reader, so the timeline agreeing with it is the whole
 * point: the two must not disagree about what the reader may see.
 */
it('refuses the timeline for the same lead the record page refuses', function () {
    $owner = timelineScopeUser('global');

    $outsider = timelineScopeUser('individual');

    $lead = timelineLeadOwnedBy($owner, 'TIMELINE-SECRET-'.bin2hex(random_bytes(4)));

    $view = $this->actingAs($outsider, 'user')->get(route('admin.leads.view', $lead->id));

    expect($view->status())->toBeGreaterThanOrEqual(300);

    $timeline = $this->actingAs($outsider, 'user')
        ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
        ->get(route('admin.leads.activities.index', $lead->id));

    expect($timeline->status())->toBe(401);
});

it('serves the timeline to the user who owns the record', function () {
    $owner = timelineScopeUser('individual');

    $note = 'TIMELINE-OWN-'.bin2hex(random_bytes(4));

    $lead = timelineLeadOwnedBy($owner, $note);

    $response = $this->actingAs($owner, 'user')
        ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
        ->get(route('admin.leads.activities.index', $lead->id).'?type=all');

    expect($response->status())->toBe(200);

    expect($response->getContent())->toContain($note);
});

it('serves the timeline to a user whose scope covers everything', function () {
    $owner = timelineScopeUser('individual');

    $administrator = timelineScopeUser('global');

    $note = 'TIMELINE-GLOBAL-'.bin2hex(random_bytes(4));

    $lead = timelineLeadOwnedBy($owner, $note);

    $response = $this->actingAs($administrator, 'user')
        ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
        ->get(route('admin.leads.activities.index', $lead->id).'?type=all');

    expect($response->status())->toBe(200);

    expect($response->getContent())->toContain($note);
});

/**
 * The timeline carries correspondence as well as notes, and the email entries expose the most:
 * the subject, the body, and every address the message was sent from and to.
 */
it('does not leak a lead\'s correspondence to a user outside its scope', function () {
    $owner = timelineScopeUser('global');

    $outsider = timelineScopeUser('individual');

    $lead = timelineLeadOwnedBy($owner, 'TIMELINE-NOTE-'.bin2hex(random_bytes(4)));

    $subject = 'CONFIDENTIAL pricing '.bin2hex(random_bytes(4));

    $reply = 'The agreed figure is 48,000 for the year.';

    $correspondent = 'client-'.bin2hex(random_bytes(4)).'@example.invalid';

    Email::create([
        'subject' => $subject,
        'reply' => $reply,
        'source' => 'email',
        'user_type' => 'admin',
        'folders' => ['outbox'],
        'lead_id' => $lead->id,
        'from' => ['owner@example.invalid'],
        'reply_to' => [$correspondent],
        'unique_id' => uniqid().'@example.invalid',
    ]);

    $response = $this->actingAs($outsider, 'user')
        ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
        ->get(route('admin.leads.activities.index', $lead->id).'?type=all');

    expect($response->status())->toBe(401);

    /**
     * Each part is checked separately: a refusal that still carried the correspondent's address
     * would leak who the record deals with even without the message itself.
     */
    expect($response->getContent())->not->toContain($subject)
        ->and($response->getContent())->not->toContain($reply)
        ->and($response->getContent())->not->toContain($correspondent);
});

/**
 * Persons carry correspondence too, and are reached through a controller of their own.
 */
it('refuses a person timeline to a user outside its data scope', function () {
    $owner = timelineScopeUser('global');

    $outsider = timelineScopeUser('individual');

    $person = app(PersonRepository::class)->create([
        'name' => 'Scoped Person '.uniqid(),
        'entity_type' => 'persons',
        'emails' => [['value' => uniqid().'@mail.invalid', 'label' => 'work']],
        'user_id' => $owner->id,
    ]);

    $response = $this->actingAs($outsider, 'user')
        ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
        ->get(route('admin.contacts.persons.activities.index', $person->id));

    expect($response->status())->toBe(401);
});

/**
 * A product is shared across the installation and owned by nobody, so its timeline stays reachable
 * — the fix must not quietly lock records that were never scoped.
 */
it('still serves a product timeline, which nobody owns', function () {
    $user = timelineScopeUser('individual');

    $product = Product::create([
        'sku' => 'timeline-'.uniqid(),
        'name' => 'Timeline Product',
        'price' => 100,
        'quantity' => 1,
    ]);

    $response = $this->actingAs($user, 'user')
        ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
        ->get(route('admin.products.activities.index', $product->id));

    expect($response->status())->toBe(200);
});
