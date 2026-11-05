<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Fuzz\FuzzEnvironment;
use Tests\Fuzz\FuzzRoutes;

uses(DatabaseTransactions::class)->group('fuzz');

const FUZZ_REFLECTED_PARAMETERS = [
    'query', 'q', 'search', 'term', 'route', 'redirect', 'url', 'name', 'title', 'email',
    'tab', 'type', 'view_type', 'pipeline_id', 'pipeline_stage_id', 'locale', 'id',
];

beforeEach(fn () => FuzzEnvironment::isolate());

/**
 * Vue only evaluates mustaches in text, so a marker inside a tag (an attribute value) is inert.
 */
function fuzzReflectedInText(string $html, string $needle): bool
{
    for ($offset = 0; ($position = strpos($html, $needle, $offset)) !== false; $offset = $position + 1) {
        $before = substr($html, 0, $position);

        $lastOpen = strrpos($before, '<');

        $lastClose = strrpos($before, '>');

        if ($lastOpen === false || ($lastClose !== false && $lastClose > $lastOpen)) {
            return true;
        }
    }

    return false;
}

it('never reflects fuzzed query input as markup or vue template code', function (string $method, string $uri) {
    $marker = 'fz'.bin2hex(random_bytes(4));

    $payloads = [
        'html' => "<script>{$marker}</script>",
        'attribute' => "\"><b data-{$marker}>",
        'vue template' => '{{'.$marker.'}}',
    ];

    $failures = [];

    foreach ($payloads as $label => $payload) {
        $response = $this->actingAs(getDefaultAdmin(), 'user')
            ->get('/'.$uri.'?'.http_build_query(array_fill_keys(FUZZ_REFLECTED_PARAMETERS, $payload)));

        if (
            $response->getStatusCode() >= 500
            || ! str_contains((string) $response->headers->get('Content-Type'), 'text/html')
        ) {
            continue;
        }

        $html = (string) $response->getContent();

        $reflected = $label === 'vue template'
            ? fuzzReflectedInText($html, $payload)
            : str_contains($html, $payload);

        if ($reflected) {
            $failures[] = "{$label} payload reflected verbatim";
        }
    }

    expect($failures)->toBeEmpty(FuzzEnvironment::report("{$method} {$uri}", $failures));
})->with(FuzzRoutes::admin(['GET'], withParameters: false));
