<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Event;
use Webkul\Lead\Models\Lead;
use Webkul\Lead\Models\Stage;

uses(DatabaseTransactions::class);

function makeLeadWithExpectedCloseDate(): Lead
{
    return Lead::create([
        'title' => 'Partial Update Lead '.bin2hex(random_bytes(4)),
        'user_id' => getDefaultAdmin()->id,
        'lead_value' => 500,
        'expected_close_date' => '2030-01-15',
        'lead_pipeline_id' => 1,
        'lead_pipeline_stage_id' => 1,
    ]);
}

function stageByCode(string $code): Stage
{
    return Stage::where('lead_pipeline_id', 1)->where('code', $code)->firstOrFail();
}

it('keeps the expected close date when the stage is changed', function () {
    $lead = makeLeadWithExpectedCloseDate();

    test()
        ->actingAs(getDefaultAdmin(), 'user')
        ->putJson('/admin/leads/stage/edit/'.$lead->id, [
            'lead_pipeline_stage_id' => stageByCode('follow-up')->id,
        ])
        ->assertOk();

    expect($lead->refresh()->expected_close_date->toDateString())->toBe('2030-01-15');
});

it('keeps the expected close date when another attribute is edited inline', function () {
    $lead = makeLeadWithExpectedCloseDate();

    test()
        ->actingAs(getDefaultAdmin(), 'user')
        ->putJson('/admin/leads/attributes/edit/'.$lead->id, [
            'entity_type' => 'leads',
            'lead_value' => 900,
        ])
        ->assertOk();

    $lead->refresh();

    expect((float) $lead->lead_value)->toBe(900.0)
        ->and($lead->expected_close_date->toDateString())->toBe('2030-01-15');
});

it('sets the closed date when leads are mass updated to a won stage', function () {
    $lead = makeLeadWithExpectedCloseDate();

    test()
        ->actingAs(getDefaultAdmin(), 'user')
        ->postJson('/admin/leads/mass-update', [
            'indices' => [$lead->id],
            'value' => stageByCode('won')->id,
        ])
        ->assertOk();

    $lead->refresh();

    expect($lead->lead_pipeline_stage_id)->toBe(stageByCode('won')->id)
        ->and($lead->closed_at)->not->toBeNull()
        ->and($lead->expected_close_date->toDateString())->toBe('2030-01-15');
});

it('dispatches the update after event when leads are mass updated', function () {
    $lead = makeLeadWithExpectedCloseDate();

    Event::fake(['lead.update.before', 'lead.update.after']);

    test()
        ->actingAs(getDefaultAdmin(), 'user')
        ->postJson('/admin/leads/mass-update', [
            'indices' => [$lead->id],
            'value' => stageByCode('follow-up')->id,
        ])
        ->assertOk();

    Event::assertDispatchedTimes('lead.update.before', 1);
    Event::assertDispatched('lead.update.after', fn ($event, $updatedLead) => $updatedLead->id === $lead->id);
});
