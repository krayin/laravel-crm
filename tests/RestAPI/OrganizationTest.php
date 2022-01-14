<?php

it('has all organizations', function () {
    $admin = getSanctumAuthenticatedAdmin();

    $response = test()->getJson(url('api/v1/contacts/organizations'));

    $response
        ->assertOK()
        ->assertJson([
            'data' => [],
        ]);
});
