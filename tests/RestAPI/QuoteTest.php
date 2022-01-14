<?php

it('has all quotes', function () {
    $admin = actingAsSanctumAuthenticatedAdmin();

    $response = test()->getJson(test()->versionRoute('quotes'));

    $response
        ->assertOK()
        ->assertJson([
            'data' => [],
        ]);
});
