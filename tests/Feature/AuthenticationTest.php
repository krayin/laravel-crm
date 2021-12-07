<?php

test('check main admin login page', function () {
    $response = $this->get(route('admin.session.create'));

    $response->assertOK();
});

test('check dashboard page after login', function () {
    $admin = getDefaultAdmin();

    test()->actingAs($admin)
        ->get(route('admin.dashboard.index'))
        ->assertSee(__('admin::app.dashboard.title'))
        ->assertSee(getFirstName($admin->name));

    expect(auth()->guard('user')->user()->name)->toBe($admin->name);
});

test('check for admin logout', function () {
    $admin = getDefaultAdmin();

    test()->actingAs($admin)
        ->delete(route('admin.session.destroy'))
        ->assertStatus(302);

    expect(auth()->guard('user')->user())->toBeNull();
});
