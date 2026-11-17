<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Webkul\Contact\Models\Person;
use Webkul\Quote\Models\Quote;

uses(DatabaseTransactions::class);

/**
 * The postcode and city line was written as `$postcode ?? '' . ' ' . $city ?? ''`. Concatenation
 * binds tighter than `??`, so whenever a postcode was set the whole fallback, city included, was
 * discarded and the printed address had no city.
 */
function quoteWithAddress(array $address): Quote
{
    $admin = getDefaultAdmin();

    $person = Person::create([
        'name' => 'Address Person '.uniqid(),
        'emails' => [['value' => uniqid().'@example.com', 'label' => 'work']],
        'user_id' => $admin->id,
    ]);

    return Quote::create([
        'subject' => 'Address check',
        'user_id' => $admin->id,
        'person_id' => $person->id,
        'billing_address' => $address,
        'shipping_address' => $address,
        'sub_total' => 100,
        'grand_total' => 100,
    ]);
}

it('prints the postcode and the city on both quote PDF addresses', function () {
    $quote = quoteWithAddress([
        'address' => 'Office 12, Al Qusais',
        'postcode' => '12345',
        'city' => 'Dubai',
        'state' => 'DU',
        'country' => 'AE',
    ]);

    $html = view('admin::quotes.pdf', compact('quote'))->render();

    expect(substr_count($html, '12345 Dubai'))->toBe(2);
});

it('prints the city alone when the address has no postcode', function () {
    $quote = quoteWithAddress([
        'address' => 'Office 12, Al Qusais',
        'city' => 'Dubai',
        'state' => 'DU',
        'country' => 'AE',
    ]);

    $html = view('admin::quotes.pdf', compact('quote'))->render();

    expect(substr_count($html, '<div>Dubai</div>'))->toBe(2);
});
