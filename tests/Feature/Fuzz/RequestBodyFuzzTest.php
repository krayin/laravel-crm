<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Fuzz\FuzzEnvironment;
use Tests\Fuzz\FuzzInput;
use Tests\Fuzz\FuzzRoutes;

uses(DatabaseTransactions::class)->group('fuzz');

const FUZZ_BODY_FIELDS = [
    'id', 'ids', 'indices', 'value', 'name', 'title', 'email', 'emails', 'contact_numbers',
    'type', 'entity_type', 'status', 'description', 'comment', 'password', 'confirm_password',
    'code', 'user_id', 'person_id', 'organization_id', 'lead_id', 'lead_value', 'lead_pipeline_id',
    'lead_pipeline_stage_id', 'products', 'participants', 'tags', 'attributes', 'options',
    'subject', 'reply', 'reply_to', 'cc', 'bcc', 'schedule_from', 'schedule_to', 'query', 'route',
];

beforeEach(fn () => FuzzEnvironment::isolate());

it('never answers a fuzzed request body with a server error', function (string $method, string $uri) {
    $admin = getDefaultAdmin();

    $url = FuzzRoutes::fill($uri, '1');

    $bodies = ['empty body' => []];

    foreach (FuzzInput::typeConfusion() as $value) {
        $bodies['every field '.FuzzInput::describe($value)] = array_fill_keys(FUZZ_BODY_FIELDS, $value);
    }

    $failures = [];

    foreach ($bodies as $label => $body) {
        $response = $this->actingAs($admin, 'user')->json($method, $url, $body);

        if ($response->getStatusCode() >= 500) {
            $failures[] = "{$label} -> ".FuzzEnvironment::failure($response);
        }
    }

    expect($failures)->toBeEmpty(FuzzEnvironment::report("{$method} {$uri}", $failures));
})->with(FuzzRoutes::admin(['POST', 'PUT', 'PATCH', 'DELETE']));
