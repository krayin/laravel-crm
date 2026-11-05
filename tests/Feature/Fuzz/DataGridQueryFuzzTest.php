<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Fuzz\FuzzEnvironment;
use Tests\Fuzz\FuzzInput;
use Tests\Fuzz\FuzzRoutes;

uses(DatabaseTransactions::class)->group('fuzz');

beforeEach(fn () => FuzzEnvironment::isolate());

it('never answers fuzzed datagrid filters, sorting or paging with a server error', function (string $method, string $uri) {
    $admin = getDefaultAdmin();

    $ajax = ['X-Requested-With' => 'XMLHttpRequest', 'Accept' => 'application/json'];

    $probe = $this->actingAs($admin, 'user')->get('/'.$uri, $ajax);

    if (! is_array($probe->json('records'))) {
        $this->markTestSkipped('not a datagrid endpoint');
    }

    $failures = [];

    foreach (FuzzInput::strings() as $value) {
        $queries = [
            'filters' => ['filters' => ['all' => [$value], $value => [$value]]],
            'filters as scalar' => ['filters' => $value],
            'sort' => ['sort' => ['column' => $value, 'order' => $value]],
            'pagination' => ['pagination' => ['page' => $value, 'per_page' => $value]],
            'export' => ['export' => $value, 'format' => $value],
        ];

        foreach ($queries as $label => $query) {
            $response = $this->actingAs($admin, 'user')->get('/'.$uri.'?'.http_build_query($query), $ajax);

            if ($response->getStatusCode() >= 500) {
                $failures[] = "{$label} ".FuzzInput::describe($value).' -> '.FuzzEnvironment::failure($response);
            }
        }
    }

    expect($failures)->toBeEmpty(FuzzEnvironment::report("{$method} {$uri}", $failures));
})->with(FuzzRoutes::admin(['GET'], withParameters: false));
