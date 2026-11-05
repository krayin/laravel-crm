<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Fuzz\FuzzEnvironment;
use Tests\Fuzz\FuzzInput;
use Tests\Fuzz\FuzzRoutes;

uses(DatabaseTransactions::class)->group('fuzz');

beforeEach(fn () => FuzzEnvironment::isolate());

it('never serves an admin route to a signed-out visitor', function (string $method, string $uri) {
    $failures = [];

    foreach (['1', "' OR '1'='1", '../../../../etc/passwd'] as $value) {
        $response = $this->json($method, FuzzRoutes::fill($uri, $value), ['indices' => [1], 'name' => 'x']);

        $status = $response->getStatusCode();

        $redirectsToLogin = $response->isRedirect() && str_ends_with((string) $response->headers->get('Location'), '/admin/login');

        if (! $redirectsToLogin && ! in_array($status, [401, 403, 404, 405], true)) {
            $failures[] = FuzzInput::describe($value)." -> {$status} ".$response->headers->get('Location');
        }
    }

    expect($failures)->toBeEmpty(FuzzEnvironment::report("{$method} {$uri}", $failures));
})->with(FuzzRoutes::admin(['GET', 'POST', 'PUT', 'PATCH', 'DELETE']));
