<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Webkul\Contact\Models\Person;
use Webkul\Product\Models\Product;
use Webkul\Quote\Models\Quote;
use Webkul\User\Models\Role;
use Webkul\User\Models\User;

uses(DatabaseTransactions::class);

/**
 * A quote can be started from Quick Add without its line items.
 *
 * The controller already relaxed its line-item validation for a quick add, but the repository
 * iterated `items` unconditionally, so saving one threw `Undefined array key "items"` and the
 * quote was never created.
 */
function quoteQuickAddActor(): User
{
    $role = Role::create([
        'name' => 'quote-quick-'.uniqid(),
        'description' => 'test',
        'permission_type' => 'all',
    ]);

    return User::create([
        'name' => 'Quote Quick Actor',
        'email' => uniqid().'@example.com',
        'password' => bcrypt('password'),
        'status' => 1,
        'view_permission' => 'global',
        'role_id' => $role->id,
    ]);
}

it('creates a quote that has no line items yet', function () {
    $user = quoteQuickAddActor();

    $person = Person::create([
        'name' => 'Quote Person '.uniqid(),
        'emails' => [['value' => uniqid().'@example.com', 'label' => 'work']],
        'user_id' => $user->id,
    ]);

    $before = Quote::count();

    /**
     * Exceptions are allowed to surface rather than being turned into a rendered error page, so a
     * failure inside the repository fails the test instead of passing as a handled response.
     */
    $this->withoutExceptionHandling();

    $response = $this->actingAs($user, 'user')->post(route('admin.quotes.store'), [
        'quick_add' => 'quote',
        'subject' => 'Quick add quote '.uniqid(),
        'description' => 'started without products',
        'user_id' => $user->id,
        'person_id' => $person->id,
        'grand_total' => 5000,
        'sub_total' => 5000,
        'expired_at' => now()->addMonth()->format('Y-m-d'),
        'billing_address' => [
            'address' => '1 Example Street',
            'country' => 'IN',
            'state' => 'UP',
            'city' => 'Example City',
            'postcode' => '201301',
        ],
    ]);

    expect(Quote::count())->toBe($before + 1);

    $quote = Quote::latest('id')->first();

    expect($quote->items)->toHaveCount(0);
});

/**
 * A quote created the ordinary way still carries its products.
 */
it('still attaches line items when they are supplied', function () {
    $user = quoteQuickAddActor();

    $person = Person::create([
        'name' => 'Quote Person '.uniqid(),
        'emails' => [['value' => uniqid().'@example.com', 'label' => 'work']],
        'user_id' => $user->id,
    ]);

    $product = Product::create([
        'sku' => 'sku-'.uniqid(),
        'name' => 'Quote Product',
        'price' => 100,
        'quantity' => 10,
    ]);

    $this->actingAs($user, 'user')->post(route('admin.quotes.store'), [
        'subject' => 'Full quote '.uniqid(),
        'user_id' => $user->id,
        'person_id' => $person->id,
        'grand_total' => 100,
        'sub_total' => 100,
        'expired_at' => now()->addMonth()->format('Y-m-d'),
        'billing_address' => ['address' => '1 St', 'country' => 'IN', 'state' => 'UP', 'city' => 'C', 'postcode' => '1'],
        'items' => [[
            'product_id' => $product->id,
            'name' => 'Quote Product',
            'quantity' => 1,
            'price' => 100,
            'total' => 100,
            'discount_amount' => 0,
            'tax_amount' => 0,
            'final_total' => 100,
        ]],
    ]);

    expect(Quote::latest('id')->first()->items)->toHaveCount(1);
});
