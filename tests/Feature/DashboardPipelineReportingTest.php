<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Webkul\Admin\Helpers\Reporting\Lead;
use Webkul\Lead\Repositories\LeadRepository;

it('only reports open stages from the selected pipeline and date range', function () {
    config(['database.connections.reporting_test' => [
        'driver' => 'sqlite',
        'database' => ':memory:',
        'prefix' => '',
    ]]);

    $connection = DB::connection('reporting_test');

    try {
        $connection->getSchemaBuilder()->create('lead_pipeline_stages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
        });

        $connection->getSchemaBuilder()->create('leads', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('lead_pipeline_id');
            $table->integer('lead_pipeline_stage_id');
            $table->decimal('lead_value');
            $table->dateTime('created_at');
        });

        $connection->table('lead_pipeline_stages')->insert([
            ['id' => 29, 'name' => 'checkout'],
            ['id' => 31, 'name' => 'won'],
            ['id' => 32, 'name' => 'lost'],
            ['id' => 80, 'name' => 'Checkout'],
            ['id' => 81, 'name' => 'won'],
        ]);

        foreach ([[7, 29], [7, 29], [7, 29], [7, 31], [7, 31], [7, 32], [8, 80], [8, 81]] as [$pipelineId, $stageId]) {
            $connection->table('leads')->insert([
                'lead_pipeline_id' => $pipelineId,
                'lead_pipeline_stage_id' => $stageId,
                'lead_value' => 710,
                'created_at' => '2026-11-23 10:00:00',
            ]);
        }

        $connection->table('leads')->insert([
            'lead_pipeline_id' => 7,
            'lead_pipeline_stage_id' => 29,
            'lead_value' => 710,
            'created_at' => '2026-10-01 10:00:00',
        ]);

        $repository = Mockery::mock(LeadRepository::class);
        $repository->shouldReceive('resetModel')->once()->andReturn($connection->table('leads'));

        $reflection = new ReflectionClass(Lead::class);
        $reporting = $reflection->newInstanceWithoutConstructor();

        foreach ([
            'leadRepository' => $repository,
            'pipeline' => (object) ['id' => 7],
            'wonStageIds' => [31],
            'lostStageIds' => [32],
            'startDate' => Carbon::parse('2026-10-24')->startOfDay(),
            'endDate' => Carbon::parse('2026-11-23')->endOfDay(),
        ] as $property => $value) {
            $reflection->getProperty($property)->setValue($reporting, $value);
        }

        $results = $reporting->getOpenLeadsByStates();

        expect($results)->toHaveCount(1)
            ->and($results->first()->name)->toBe('checkout')
            ->and((int) $results->first()->total)->toBe(3);
    } finally {
        DB::purge('reporting_test');
    }
});
