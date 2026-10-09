<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Webkul\Contact\Models\Person;
use Webkul\Lead\Models\Lead;
use Webkul\Lead\Models\Product as LeadProduct;
use Webkul\Product\Models\Product;
use Webkul\Quote\Models\Quote;
use Webkul\User\Models\Role;
use Webkul\User\Models\User;

/**
 * The Quote module enforces its data scope on `edit`, `update`, `destroy` and `print`, but two read
 * endpoints were left out. `search` returned every quote — subject, description, addresses, totals
 * and the linked person's contact details — and `leadProducts` returned another user's deal pricing
 * for any lead id, to any user holding quote access.
 *
 * These tests pin the scope on both endpoints and keep a global-scope user unrestricted.
 */
uses(DatabaseTransactions::class);

function makeQuoteScopeUser(string $viewPermission): User
{
    $role = Role::create([
        'name' => 'Quote Scope Role '.bin2hex(random_bytes(6)),
        'description' => 'Created by the quote data scope tests.',
        'permission_type' => 'all',
    ]);

    return User::create([
        'name' => 'Quote Scope Probe',
        'email' => 'quote-scope-'.bin2hex(random_bytes(6)).'@example.invalid',
        'password' => bcrypt('correct-horse-battery-staple'),
        'status' => 1,
        'role_id' => $role->id,
        'view_permission' => $viewPermission,
    ]);
}

function makeOwnedQuote(User $owner): Quote
{
    $person = Person::create([
        'name' => 'Quote Owner Contact '.bin2hex(random_bytes(4)),
        'user_id' => $owner->id,
        'emails' => [['value' => 'quote-contact-'.bin2hex(random_bytes(4)).'@example.invalid', 'label' => 'work']],
    ]);

    return Quote::create([
        'subject' => 'Renewal pricing for the enterprise tier',
        'description' => 'Negotiated down to 48,000 for the year.',
        'user_id' => $owner->id,
        'person_id' => $person->id,
        'sub_total' => 48000,
        'grand_total' => 48000,
        'expired_at' => now()->addMonth()->toDateString(),
    ]);
}

/**
 * A lead owned by `$owner` carrying one priced product, i.e. the deal pricing a restricted agent
 * must not be able to read.
 */
function makeOwnedLeadWithProduct(User $owner): Lead
{
    $person = Person::create([
        'name' => 'Lead Owner Contact '.bin2hex(random_bytes(4)),
        'user_id' => $owner->id,
        'emails' => [['value' => 'lead-contact-'.bin2hex(random_bytes(4)).'@example.invalid', 'label' => 'work']],
    ]);

    $lead = Lead::create([
        'title' => 'Owned Lead '.bin2hex(random_bytes(4)),
        'user_id' => $owner->id,
        'person_id' => $person->id,
        'lead_pipeline_id' => 1,
        'lead_pipeline_stage_id' => 1,
    ]);

    $product = Product::create([
        'name' => 'Confidential Appliance '.bin2hex(random_bytes(4)),
        'sku' => 'SKU-'.bin2hex(random_bytes(4)),
        'price' => 1234.56,
        'quantity' => 10,
    ]);

    LeadProduct::create([
        'lead_id' => $lead->id,
        'product_id' => $product->id,
        'quantity' => 7,
        'price' => 1234.56,
        'amount' => 8641.92,
    ]);

    return $lead;
}

it('refuses lead products for a lead owned by another user', function () {
    $owner = makeQuoteScopeUser('global');
    $agent = makeQuoteScopeUser('individual');

    $lead = makeOwnedLeadWithProduct($owner);

    $response = test()
        ->actingAs($agent, 'user')
        ->get('/admin/quotes/lead-products/'.$lead->id);

    expect($response->getStatusCode())->toBe(401);
});

it('does not leak another user deal pricing through lead products', function () {
    $owner = makeQuoteScopeUser('global');
    $agent = makeQuoteScopeUser('individual');

    $lead = makeOwnedLeadWithProduct($owner);

    $response = test()
        ->actingAs($agent, 'user')
        ->get('/admin/quotes/lead-products/'.$lead->id);

    expect($response->getContent())
        ->not->toContain('1234.56')
        ->not->toContain('8641.92');
});

it('excludes another user quote from the quote search', function () {
    $owner = makeQuoteScopeUser('global');
    $agent = makeQuoteScopeUser('individual');

    $quote = makeOwnedQuote($owner);

    $response = test()
        ->actingAs($agent, 'user')
        ->get('/admin/quotes/search');

    expect($response->getStatusCode())->toBe(200);

    $ids = collect($response->json('data') ?? [])->pluck('id')->all();

    expect($ids)->not->toContain($quote->id);
});

it('does not leak quote subject or totals through the search response', function () {
    $owner = makeQuoteScopeUser('global');
    $agent = makeQuoteScopeUser('individual');

    makeOwnedQuote($owner);

    $response = test()
        ->actingAs($agent, 'user')
        ->get('/admin/quotes/search');

    expect($response->getContent())
        ->not->toContain('Renewal pricing for the enterprise tier')
        ->not->toContain('Negotiated down to 48,000 for the year.');
});

it('still returns the owner own quote in the search', function () {
    $owner = makeQuoteScopeUser('individual');

    $quote = makeOwnedQuote($owner);

    $response = test()
        ->actingAs($owner, 'user')
        ->get('/admin/quotes/search');

    expect($response->getStatusCode())->toBe(200);

    $ids = collect($response->json('data') ?? [])->pluck('id')->all();

    expect($ids)->toContain($quote->id);
});

it('still allows a global user to read any lead products', function () {
    $owner = makeQuoteScopeUser('global');
    $other = makeQuoteScopeUser('global');

    $lead = makeOwnedLeadWithProduct($owner);

    $response = test()
        ->actingAs($other, 'user')
        ->get('/admin/quotes/lead-products/'.$lead->id);

    expect($response->getStatusCode())->toBe(200);

    expect($response->json('data'))->toHaveCount(1);
});

it('still allows a global user to see every quote in the search', function () {
    $owner = makeQuoteScopeUser('global');
    $other = makeQuoteScopeUser('global');

    $quote = makeOwnedQuote($owner);

    $response = test()
        ->actingAs($other, 'user')
        ->get('/admin/quotes/search');

    $ids = collect($response->json('data') ?? [])->pluck('id')->all();

    expect($ids)->toContain($quote->id);
});
