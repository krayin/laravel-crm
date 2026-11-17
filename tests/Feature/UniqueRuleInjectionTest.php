<?php

use Illuminate\Support\Facades\DB;

afterEach(function () {
    DB::table('datagrid_saved_filters')->where('name', 'like', 'unique-rule-test-%')->delete();
    DB::table('lead_pipelines')->where('name', 'like', 'unique-rule-test-%')->delete();
});

function createSavedFilter(string $name, string $src): void
{
    DB::table('datagrid_saved_filters')->insert([
        'user_id' => getDefaultAdmin()->id,
        'name' => $name,
        'src' => $src,
        'applied' => json_encode([]),
        'created_at' => now(),
        'updated_at' => now(),
    ]);
}

function createPipeline(string $name): int
{
    return DB::table('lead_pipelines')->insertGetId([
        'name' => $name,
        'rotten_days' => 30,
        'is_default' => 0,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
}

it('rejects a duplicate saved filter name', function () {
    createSavedFilter('unique-rule-test-dup', 'leads');

    test()->actingAs(getDefaultAdmin())
        ->postJson(route('admin.datagrid.saved_filters.store'), [
            'name' => 'unique-rule-test-dup',
            'src' => 'leads',
            'applied' => [],
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('name');
});

it('does not let a comma in src bypass the saved filter duplicate check', function () {
    $src = 'leads,src,other';

    createSavedFilter('unique-rule-test-inject', $src);

    test()->actingAs(getDefaultAdmin())
        ->postJson(route('admin.datagrid.saved_filters.store'), [
            'name' => 'unique-rule-test-inject',
            'src' => $src,
            'applied' => [],
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('name');

    expect(DB::table('datagrid_saved_filters')->where('name', 'unique-rule-test-inject')->count())->toBe(1);
});

it('does not let a comma in src bypass the saved filter duplicate check on update', function () {
    $src = 'leads,src,other';

    createSavedFilter('unique-rule-test-upd-a', $src);
    createSavedFilter('unique-rule-test-upd-b', $src);

    $id = DB::table('datagrid_saved_filters')->where('name', 'unique-rule-test-upd-b')->value('id');

    test()->actingAs(getDefaultAdmin())
        ->putJson(route('admin.datagrid.saved_filters.update', $id), [
            'name' => 'unique-rule-test-upd-a',
            'src' => $src,
            'applied' => [],
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('name');
});

it('rejects a duplicate pipeline name', function () {
    createPipeline('unique-rule-test-pipeline');

    test()->actingAs(getDefaultAdmin())
        ->postJson(route('admin.settings.pipelines.store'), [
            'name' => 'unique-rule-test-pipeline',
            'rotten_days' => 30,
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('name');
});

it('does not let a comma in id bypass the pipeline duplicate check', function () {
    createPipeline('unique-rule-test-pipeline-inject');

    test()->actingAs(getDefaultAdmin())
        ->postJson(route('admin.settings.pipelines.store'), [
            'name' => 'unique-rule-test-pipeline-inject',
            'id' => 'NULL,NULL,name,zzz',
            'rotten_days' => 30,
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('name');

    expect(DB::table('lead_pipelines')->where('name', 'unique-rule-test-pipeline-inject')->count())->toBe(1);
});

it('still allows a pipeline to keep its own name on update', function () {
    $id = createPipeline('unique-rule-test-pipeline-own');

    test()->actingAs(getDefaultAdmin())
        ->postJson(route('admin.settings.pipelines.update', $id), [
            'id' => $id,
            'name' => 'unique-rule-test-pipeline-own',
            'rotten_days' => 30,
        ])
        ->assertJsonMissingValidationErrors('name');
});
