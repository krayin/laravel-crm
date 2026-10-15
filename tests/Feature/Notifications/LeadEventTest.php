<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Event;
use Webkul\Lead\Models\Pipeline;
use Webkul\Lead\Models\Source;
use Webkul\Lead\Models\Type;
use Webkul\User\Models\Role;
use Webkul\User\Models\User;

uses(DatabaseTransactions::class);

/**
 * Creating a lead must announce itself however the request arrived.
 *
 * The controller returned its json response before reaching the dispatch, so a lead added from the
 * kanban board or a quick-create form — both of which post over ajax — fired no event at all, and
 * everything listening for one, notifications included, never heard about it.
 */

/**
 * Built here rather than looked up: the suite runs in parallel, and a user this test found in the
 * database could be one another test is in the middle of rolling back.
 */
function notificationLeadActor(): User
{
    $role = Role::create([
        'name' => 'lead-event-'.uniqid(),
        'description' => 'test',
        'permission_type' => 'all',
    ]);

    return User::create([
        'name' => 'Lead Event Actor',
        'email' => uniqid().'@example.com',
        'password' => bcrypt('password'),
        'status' => 1,
        'view_permission' => 'global',
        'role_id' => $role->id,
    ]);
}

/**
 * The suite runs in parallel, so the lead's source, type and pipeline are created here rather than
 * read from the database: a row found by a lookup may belong to another process that is about to
 * roll it back, leaving the request pointing at an id that no longer exists.
 */
function notificationLeadPayload(): array
{
    $source = Source::create(['name' => 'lead-event-source-'.uniqid()]);

    $type = Type::create(['name' => 'lead-event-type-'.uniqid()]);

    $pipeline = Pipeline::query()->firstOrCreate(
        ['name' => 'Lead Event Pipeline'],
        ['is_default' => 0, 'rotten_days' => 30]
    );

    if (! $pipeline->stages()->count()) {
        $pipeline->stages()->create([
            'name' => 'New',
            'code' => 'new',
            'probability' => 100,
            'sort_order' => 1,
        ]);
    }

    return [
        'title' => 'Event probe '.uniqid(),
        'description' => 'probe',
        'lead_value' => 1000,
        'lead_source_id' => $source->id,
        'lead_type_id' => $type->id,
        'lead_pipeline_id' => $pipeline->id,
        'expected_close_date' => now()->addMonth()->format('Y-m-d'),
        'person' => [
            'name' => 'Probe Person',
            'emails' => [['value' => 'probe'.uniqid().'@example.com', 'label' => 'work']],
        ],
    ];
}

it('announces a lead created over ajax', function () {
    Event::fake(['lead.create.after']);

    $this->actingAs(notificationLeadActor(), 'user')
        ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
        ->post(route('admin.leads.store'), notificationLeadPayload());

    Event::assertDispatched('lead.create.after');
});

it('announces a lead created from the full form', function () {
    Event::fake(['lead.create.after']);

    $this->actingAs(notificationLeadActor(), 'user')
        ->post(route('admin.leads.store'), notificationLeadPayload());

    Event::assertDispatched('lead.create.after');
});
