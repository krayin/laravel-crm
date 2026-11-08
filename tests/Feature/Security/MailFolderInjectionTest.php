<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Webkul\Email\Models\Email;

/**
 * Code injection through the mail folder.
 */
uses(DatabaseTransactions::class);

const MAIL_FOLDER_PAYLOAD = "{{constructor.constructor('alert(document.domain)')()}}";

function mailFolderUrl(string $route, array $parameters, array $query = []): string
{
    return route($route, $parameters).($query ? '?'.http_build_query($query) : '');
}

function folderInjectionEmail(): Email
{
    return Email::create([
        'subject' => 'Folder injection probe',
        'reply' => 'Body',
        'source' => 'email',
        'user_type' => 'person',
        'folders' => ['inbox'],
        'name' => 'Sender',
        'unique_id' => bin2hex(random_bytes(8)),
        'message_id' => bin2hex(random_bytes(8)).'@example.invalid',
    ]);
}

it('ignores a folder smuggled through the query string on the mail list', function () {
    $this->actingAs(getDefaultAdmin(), 'user')
        ->get(mailFolderUrl('admin.mail.index', ['route' => 'inbox'], ['route' => MAIL_FOLDER_PAYLOAD]))
        ->assertOk()
        ->assertDontSee('constructor.constructor', false);
});

it('ignores a folder smuggled through the query string on a mail view', function () {
    $email = folderInjectionEmail();

    $this->actingAs(getDefaultAdmin(), 'user')
        ->get(mailFolderUrl('admin.mail.view', ['route' => 'inbox', 'id' => $email->id], ['route' => MAIL_FOLDER_PAYLOAD]))
        ->assertOk()
        ->assertDontSee('constructor.constructor', false);
});

it('rejects a folder outside the whitelist in the URL segment', function (bool $ajax) {
    $headers = $ajax ? ['X-Requested-With' => 'XMLHttpRequest'] : [];

    $this->actingAs(getDefaultAdmin(), 'user')
        ->get('/admin/mail/'.rawurlencode(MAIL_FOLDER_PAYLOAD), $headers)
        ->assertUnauthorized();
})->with([
    'page request' => [false],
    'ajax request' => [true],
]);

it('still serves every whitelisted folder', function (string $folder) {
    $this->actingAs(getDefaultAdmin(), 'user')
        ->get(route('admin.mail.index', ['route' => $folder]))
        ->assertOk();
})->with(['inbox', 'important', 'starred', 'draft', 'outbox', 'sent', 'spam', 'trash']);
