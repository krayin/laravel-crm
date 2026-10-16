<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Webkul\Admin\DataGrids\Mail\EmailDataGrid;

/**
 * The mail list opens a mail by finding its opening action among the row's actions and handing it
 * to the handler. That lookup asked for the action named `edit`, which only a draft carries, so in
 * every other folder it found nothing and the handler was called with no row at all — reading
 * `index` of undefined before it could do anything.
 *
 * The folders that do not open in the compose modal name their action `view`, so the lookup has to
 * accept either, and every folder has to offer one of the two.
 */
uses(DatabaseTransactions::class);

/**
 * The actions the grid offers for a folder, as the page receives them.
 */
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

        /**
         * The same lookup the page makes.
         */
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

    /**
     * The title is what the page used to branch on, and it does not survive a change of locale.
     */
    expect($spanish['title'])->not->toBe($english['title']);

    expect($spanish['index'])->toBe($english['index']);
});
