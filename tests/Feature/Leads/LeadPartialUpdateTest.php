<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Webkul\Lead\Models\Lead;
use Webkul\Lead\Models\Stage;

uses(DatabaseTransactions::class);

function makeLeadWithExpectedCloseDate(): Lead
{
    /**
     * The pipeline is resolved rather than assumed to be id 1: the parallel test databases are
     * migrated but not seeded, so that row is not always there and the insert failed on its
     * foreign key.
     */
    $pipeline = securityTestPipeline();

    return Lead::create([
        'title' => 'Partial Update Lead '.bin2hex(random_bytes(4)),
        'user_id' => getDefaultAdmin()->id,
        'lead_value' => 500,
        'expected_close_date' => '2030-01-15',
        'lead_pipeline_id' => $pipeline->id,
        'lead_pipeline_stage_id' => $pipeline->stages()->value('id'),
    ]);
}

/**
 * A stage of the test's own pipeline, created when it is absent.
 *
 * The pipeline was assumed to be id 1 and to carry every stage the seeders install; the parallel
 * test databases are migrated but not seeded, so neither held.
 */
function stageByCode(string $code): Stage
{
    $pipeline = securityTestPipeline();

    return Stage::query()->firstOrCreate(
        ['lead_pipeline_id' => $pipeline->id, 'code' => $code],
        [
            'name' => ucfirst(str_replace('-', ' ', $code)),
            'probability' => 50,
            'sort_order' => 2,
        ]
    );
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
