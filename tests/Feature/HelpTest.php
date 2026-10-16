<?php

it('shows the help page to an authenticated admin', function () {
    $admin = getDefaultAdmin();

    $response = test()->actingAs($admin)
        ->get(route('admin.help.index'))
        ->assertOk();

    foreach ([
        'admin::app.help.index.title',
        'admin::app.help.index.description',
        'admin::app.help.index.contact-us',
        'admin::app.help.index.services.title',
        'admin::app.help.index.resources.title',
        'admin::app.help.index.still-need-help-title',
    ] as $key) {
        $translation = trans($key);

        expect($translation)->not->toBe($key, "{$key} should be translated");

        $response->assertSee($translation, false);
    }

    $response->assertSee('krayincrm.com/cloud-hosting', false);
});
