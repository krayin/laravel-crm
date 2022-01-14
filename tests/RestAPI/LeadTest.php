<?php

it('has all leads', function () {
    $admin = getSanctumAuthenticatedAdmin();

    $response = test()->getJson(url('api/v1/leads'));

    $response
        ->assertOK()
        ->assertJson([
            'data' => [],
        ]);
});
