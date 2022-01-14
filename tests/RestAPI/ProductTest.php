<?php

it('has all products', function () {
    $admin = getSanctumAuthenticatedAdmin();

    $response = test()->getJson(url('api/v1/products'));

    $response
        ->assertOK()
        ->assertJson([
            'data' => [],
        ]);
});
