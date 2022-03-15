<?php

test('check main admin login and grab token', function () {
    $admin = getDefaultAdmin();

    $response = test()->postJson(test()->versionRoute('login'), [
        'email'       => 'admin@example.com',
        'password'    => 'admin123',
        'device_name' => 'Samsung A70s',
    ]);

    $response
        ->assertOK()
        ->assertJson([
            'data'    => [
                'name'  => $admin->name,
                'email' => $admin->email,
            ],
            'message' => __('rest-api::app.common-response.success.login'),
        ]);
});

test('check current logged in user details', function () {
    $admin = actingAsSanctumAuthenticatedAdmin();

    $response = test()->getJson(test()->versionRoute('get'));

    $response
        ->assertOK()
        ->assertJson([
            'data' => [
                'name'  => $admin->name,
                'email' => $admin->email,
            ],
        ]);
});

test('check for admin logout and destroy token', function () {
    $admin = actingAsSanctumAuthenticatedAdmin();

    $response = test()->deleteJson(test()->versionRoute('logout'));

    $response
        ->assertOK()
        ->assertJson([
            'message' => __('rest-api::app.common-response.success.logout'),
        ]);
});
