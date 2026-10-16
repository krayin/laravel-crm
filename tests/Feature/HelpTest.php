<?php

/**
 * The help page is asserted through its translation keys rather than the English words they
 * resolve to: the locale is taken from the installation's own settings, so a database configured
 * for anything but English rendered the page in that language and the hardcoded words matched
 * nothing.
 */
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

        /**
         * A key that resolves to itself names nothing on the page, which is a failure of the
         * assertion rather than of the page.
         */
        expect($translation)->not->toBe($key, "{$key} should be translated");

        $response->assertSee($translation, false);
    }

    /**
     * The links are not translated, so they are asserted as they are written.
     */
    $response->assertSee('krayincrm.com/cloud-hosting', false);
});
