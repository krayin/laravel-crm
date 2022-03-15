<?php

it('has all activities', function () {
    $admin = actingAsSanctumAuthenticatedAdmin();

    $response = test()->getJson(test()->versionRoute('activities'));

    $response
        ->assertOK()
        ->assertJson([
            'data' => [],
        ]);
});
