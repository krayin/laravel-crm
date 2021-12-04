<?php

test('check main admin login page', function () {
    $response = $this->get(route('admin.session.create'));

    $response->assertStatus(200);
});

test('check dashboard page after login', function () {
    $admin = loggedInAsAdmin();

    test()->actingAs($admin)
        ->get(route('admin.dashboard.index'))
        ->assertSee(__('admin::app.dashboard.title'))
        ->assertSee(getFirstName($admin->name));
});
