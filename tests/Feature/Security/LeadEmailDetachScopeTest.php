<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Webkul\Contact\Repositories\PersonRepository;
use Webkul\Email\Models\Email;
use Webkul\Lead\Models\Lead;
use Webkul\User\Models\Role;
use Webkul\User\Models\User;

uses(DatabaseTransactions::class);

/**
 * Unlinking a mail from its lead is a write against that mail, so it is held to the same data
 * scope as reading it.
 *
 * The mail to unlink is named by the request rather than the route, and was written to without
 * being checked: a user who could not read a lead's correspondence could still cut it loose from
 * the lead, leaving the owner's timeline quietly incomplete.
 */
function detachScopeUser(string $viewPermission): User
{
    $role = Role::create([
        'name' => 'detach-'.uniqid(),
        'description' => 'test',
        'permission_type' => 'all',
    ]);

    return User::create([
        'name' => 'Detach User',
        'email' => uniqid().'@example.com',
        'password' => bcrypt('password'),
        'status' => 1,
        'view_permission' => $viewPermission,
        'role_id' => $role->id,
    ]);
}

/**
 * A lead owned by `$owner`, with one mail linked to it.
 *
 * @return array{0: Lead, 1: Email}
 */
function detachScopeLeadWithMail(User $owner): array
{
    $pipeline = securityTestPipeline();

    $person = app(PersonRepository::class)->create([
        'name' => 'Detach Person '.uniqid(),
        'entity_type' => 'persons',
        'emails' => [['value' => uniqid().'@example.invalid', 'label' => 'work']],
        'user_id' => $owner->id,
    ]);

    $lead = Lead::create([
        'title' => 'Detach Lead '.uniqid(),
        'user_id' => $owner->id,
        'person_id' => $person->id,
        'lead_pipeline_id' => $pipeline->id,
        'lead_pipeline_stage_id' => $pipeline->stages()->value('id'),
    ]);

    $email = Email::create([
        'subject' => 'Linked correspondence '.uniqid(),
        'reply' => 'The agreed figure is 48,000 for the year.',
        'source' => 'email',
        'user_type' => 'admin',
        'folders' => ['inbox'],
        'lead_id' => $lead->id,
        'from' => ['owner@example.invalid'],
        'unique_id' => uniqid().'@example.invalid',
    ]);

    return [$lead, $email];
}

it('refuses to unlink a mail from a lead outside the acting user\'s scope', function () {
    $owner = detachScopeUser('global');

    $outsider = detachScopeUser('individual');

    [$lead, $email] = detachScopeLeadWithMail($owner);

    $response = $this->actingAs($outsider, 'user')
        ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
        ->delete(route('admin.leads.emails.detach', $lead->id), ['email_id' => $email->id]);

    expect($response->status())->toBe(401);

    /**
     * The database is what settles it: a refusal that still performed the write would leave the
     * status looking right and the record changed.
     */
    expect($email->fresh()->lead_id)->toBe($lead->id);
});

it('lets the owner unlink a mail from their own lead', function () {
    $owner = detachScopeUser('individual');

    [$lead, $email] = detachScopeLeadWithMail($owner);

    $response = $this->actingAs($owner, 'user')
        ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
        ->delete(route('admin.leads.emails.detach', $lead->id), ['email_id' => $email->id]);

    expect($response->status())->toBe(200);

    expect($email->fresh()->lead_id)->toBeNull();
});

it('lets a reader whose scope covers everything unlink the mail', function () {
    $owner = detachScopeUser('individual');

    $administrator = detachScopeUser('global');

    [$lead, $email] = detachScopeLeadWithMail($owner);

    $response = $this->actingAs($administrator, 'user')
        ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
        ->delete(route('admin.leads.emails.detach', $lead->id), ['email_id' => $email->id]);

    expect($response->status())->toBe(200);

    expect($email->fresh()->lead_id)->toBeNull();
});
