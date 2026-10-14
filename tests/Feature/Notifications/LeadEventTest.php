<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Event;
use Webkul\Lead\Models\Pipeline;
use Webkul\Lead\Models\Source;
use Webkul\Lead\Models\Type;
use Webkul\User\Models\User;

uses(DatabaseTransactions::class);

/**
 * Creating a lead must announce itself however the request arrived.
 *
 * The controller returned its json response before reaching the dispatch, so a lead added from the
 * kanban board or a quick-create form — both of which post over ajax — fired no event at all, and
 * everything listening for one, notifications included, never heard about it.
 */
it('announces a lead created over ajax', function () {
    $user = User::whereHas('role', fn ($query) => $query->where('permission_type', 'all'))
        ->where('status', 1)
        ->first();

    expect($user)->not->toBeNull();

    Event::fake(['lead.create.after']);

    $this->actingAs($user, 'user')
        ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
        ->post(route('admin.leads.store'), leadPayload());

    Event::assertDispatched('lead.create.after');
});

it('announces a lead created from the full form', function () {
    $user = User::whereHas('role', fn ($query) => $query->where('permission_type', 'all'))
        ->where('status', 1)
        ->first();

    Event::fake(['lead.create.after']);

    $this->actingAs($user, 'user')
        ->post(route('admin.leads.store'), leadPayload());

    Event::assertDispatched('lead.create.after');
});

function leadPayload(): array
{
    $pipeline = Pipeline::query()->first();

    return [
        'title' => 'Event probe '.uniqid(),
        'description' => 'probe',
        'lead_value' => 1000,
        'lead_source_id' => Source::query()->first()?->id,
        'lead_type_id' => Type::query()->first()?->id,
        'lead_pipeline_id' => $pipeline?->id,
        'expected_close_date' => now()->addMonth()->format('Y-m-d'),
        'person' => [
            'name' => 'Probe Person',
            'emails' => [['value' => 'probe'.uniqid().'@example.com', 'label' => 'work']],
        ],
    ];
}
