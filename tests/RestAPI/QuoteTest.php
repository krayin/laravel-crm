<?php

it('has all quotes', function () {
    $admin = getSanctumAuthenticatedAdmin();

    $response = test()->getJson(url('api/v1/quotes'));

    $response
        ->assertOK()
        ->assertJson([
            'data' => [],
        ]);
});
