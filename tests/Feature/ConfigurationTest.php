<?php

it('Test display the configuration page', function () {
    $admin = getDefaultAdmin();

    test()->actingAs($admin)
        ->get(route('admin.configuration.index'))
        ->assertOK();
});

it('Test display price configuration page', function () {
    $admin = getDefaultAdmin();

    test()->actingAs($admin)
        ->get(route('admin.configuration.index', ['slug' => 'price']))
        ->assertOK();
});

it('Test format currency', function () {
    // example of price value
    $price = 1000.00;

    // output: ₫1,000
    test()->assertEquals('₫1,000', core()->formatBasePrice($price));

    // same currency, but different value
    // Example: 1000000.00
    // Thai Baht: ฿1,000,000
    // Vietnamese Dong: ₫1,000,000
    // US Dollar: $1,000,000
});
