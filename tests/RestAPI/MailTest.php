<?php

it('has all mails', function () {
    $admin = getSanctumAuthenticatedAdmin();

    $response = test()->getJson(url('api/v1/mails'));

    $response
        ->assertOK()
        ->assertJson([
            'data' => [],
        ]);
});
