<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
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
