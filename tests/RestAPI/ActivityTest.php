<?php

it('has all activities', function () {
    $admin = getSanctumAuthenticatedAdmin();

    $response = test()->getJson(url('api/v1/activities'));

    $response
        ->assertOK()
        ->assertJson([
            'data' => [],
        ]);
});
