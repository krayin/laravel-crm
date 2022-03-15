<?php

it('has all leads', function () {
    $admin = actingAsSanctumAuthenticatedAdmin();

    $response = test()->getJson(test()->versionRoute('leads'));

    $response
        ->assertOK()
        ->assertJson([
            'data' => [],
        ]);
});
