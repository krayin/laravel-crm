<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Fuzz\FuzzEnvironment;
use Tests\Fuzz\FuzzInput;
use Tests\Fuzz\FuzzRoutes;

uses(DatabaseTransactions::class)->group('fuzz');

beforeEach(fn () => FuzzEnvironment::isolate());

it('never answers a fuzzed route parameter with a server error', function (string $method, string $uri) {
    $admin = getDefaultAdmin();

    $failures = [];

    foreach (FuzzInput::strings() as $value) {
        $response = $this->actingAs($admin, 'user')->get(FuzzRoutes::fill($uri, $value));

        if ($response->getStatusCode() >= 500) {
            $failures[] = FuzzInput::describe($value).' -> '.FuzzEnvironment::failure($response);
        }
    }

    expect($failures)->toBeEmpty(FuzzEnvironment::report("{$method} {$uri}", $failures));
})->with(FuzzRoutes::admin(['GET'], withParameters: true));
