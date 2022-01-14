<?php

it('has all persons', function () {
    $admin = getSanctumAuthenticatedAdmin();

    $response = test()->getJson(url('api/v1/contacts/persons'));

    $response
        ->assertOK()
        ->assertJson([
            'data' => [],
        ]);
});
