<?php

it('has all organizations', function () {
    $admin = actingAsSanctumAuthenticatedAdmin();

    $response = test()->getJson(test()->versionRoute('contacts/organizations'));

    $response
        ->assertOK()
        ->assertJson([
            'data' => [],
        ]);
});
