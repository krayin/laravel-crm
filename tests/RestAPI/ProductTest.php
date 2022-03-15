<?php

it('has all products', function () {
    $admin = actingAsSanctumAuthenticatedAdmin();

    $response = test()->getJson(test()->versionRoute('products'));

    $response
        ->assertOK()
        ->assertJson([
            'data' => [],
        ]);
});
