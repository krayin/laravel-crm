<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Webkul\Admin\DataGrids\Mail\EmailDataGrid;

uses(DatabaseTransactions::class);

function mailGridActionsFor(string $route): array
{
    auth()->guard('user')->setUser(getDefaultAdmin());

    request()->merge(['route' => $route]);

    $payload = json_decode(app(EmailDataGrid::class)->process()->getContent(), true);

    return $payload['actions'] ?? [];
}

it('offers an action the list can find in every folder', function () {
    foreach (['draft', 'outbox', 'inbox', 'trash'] as $route) {
        $actions = mailGridActionsFor($route);

        $opener = collect($actions)
            ->first(fn ($action) => in_array($action['index'], ['edit', 'view'], true));

        expect($opener)->not->toBeNull("the {$route} folder should offer an action to open a mail");
    }
});

it('names the draft action apart from the rest', function () {
    $draft = collect(mailGridActionsFor('draft'))
        ->first(fn ($action) => in_array($action['index'], ['edit', 'view'], true));

    expect($draft['index'])->toBe('edit');

    foreach (['outbox', 'inbox', 'trash'] as $route) {
        $opener = collect(mailGridActionsFor($route))
            ->first(fn ($action) => in_array($action['index'], ['edit', 'view'], true));

        expect($opener['index'])->toBe('view', "{$route} should not offer the draft action");
    }
});

it('keeps the action name steady while its title follows the locale', function () {
    app()->setLocale('en');

    $english = collect(mailGridActionsFor('outbox'))
        ->first(fn ($action) => in_array($action['index'], ['edit', 'view'], true));

    app()->setLocale('es');

    $spanish = collect(mailGridActionsFor('outbox'))
        ->first(fn ($action) => in_array($action['index'], ['edit', 'view'], true));

    app()->setLocale('en');

    expect($spanish['title'])->not->toBe($english['title']);

    expect($spanish['index'])->toBe($english['index']);
});
