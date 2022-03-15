<?php

it('has all mails', function () {
    $admin = actingAsSanctumAuthenticatedAdmin();

    $response = test()->getJson(test()->versionRoute('mails'));

    $response
        ->assertOK()
        ->assertJson([
            'data' => [],
        ]);
});
