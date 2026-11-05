<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Crypt;
use Webkul\Admin\DataGrids\Lead\LeadDataGrid;
use Webkul\Attribute\Models\AttributeValue;
use Webkul\Contact\Models\Person;
use Webkul\Email\Models\Email;
use Webkul\Lead\Models\Lead;
use Webkul\Lead\Models\Product as LeadProduct;
use Webkul\Product\Models\Product;
use Webkul\Quote\Models\Quote;
use Webkul\Tag\Models\Tag;
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

    $pipeline = securityTestPipeline();

    $lead = Lead::create([
        'title' => 'Owned Lead '.bin2hex(random_bytes(4)),
        'user_id' => $owner->id,
        'person_id' => $person->id,
        'lead_pipeline_id' => $pipeline->id,
        'lead_pipeline_stage_id' => $pipeline->stages()->value('id'),
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

/**
 * Broken object level authorization through the quote's `lead_id`.
 *
 * `leadProducts()` was bound to the lead's owner, but `create()` filled the form from any lead named
 * in the query string — contact, owner, billing address and product pricing — and `store()` and
 * `update()` attached the quote to any lead named in the body.
 */
function quotePayload(User $user, array $extra = []): array
{
    $person = Person::create([
        'name' => 'Quote Payload Contact '.bin2hex(random_bytes(4)),
        'user_id' => $user->id,
        'emails' => [['value' => 'payload-'.bin2hex(random_bytes(4)).'@example.invalid', 'label' => 'work']],
    ]);

    $product = Product::create([
        'name' => 'Payload Product '.bin2hex(random_bytes(4)),
        'sku' => 'SKU-'.bin2hex(random_bytes(4)),
        'price' => 100,
        'quantity' => 10,
    ]);

    return [
        'subject' => 'Quote under test '.bin2hex(random_bytes(4)),
        'user_id' => $user->id,
        'person_id' => $person->id,
        'billing_address' => [
            'address' => '1 Main Street',
            'city' => 'Springfield',
            'state' => 'ST',
            'country' => 'US',
            'postcode' => '12345',
        ],
        'sub_total' => 100,
        'grand_total' => 100,
        'expired_at' => now()->addMonth()->toDateString(),
        /**
         * Keyed as the form keys new rows: on update, a numeric key names an existing item.
         */
        'items' => ['item_0' => [
            'product_id' => $product->id,
            'quantity' => 1,
            'price' => 100,
            'total' => 100,
            'discount_amount' => 0,
            'tax_amount' => 0,
            'final_total' => 100,
        ]],
        ...$extra,
    ];
}

it('refuses to fill a new quote from a lead owned by another user', function () {
    $owner = makeQuoteScopeUser('global');
    $agent = makeQuoteScopeUser('individual');

    $lead = makeOwnedLeadWithProduct($owner);

    $response = test()
        ->actingAs($agent, 'user')
        ->get(route('admin.quotes.create', ['lead_id' => $lead->id]));

    expect($response->getStatusCode())->toBe(401)
        ->and($response->getContent())
        ->not->toContain('Confidential Appliance')
        ->not->toContain('1234.56');
});

it('still fills a new quote from the caller own lead', function () {
    $agent = makeQuoteScopeUser('individual');

    $lead = makeOwnedLeadWithProduct($agent);

    test()
        ->actingAs($agent, 'user')
        ->get(route('admin.quotes.create', ['lead_id' => $lead->id]))
        ->assertOk()
        ->assertSee('Confidential Appliance');
});

it('refuses to attach a new quote to a lead owned by another user', function () {
    $owner = makeQuoteScopeUser('global');
    $agent = makeQuoteScopeUser('individual');

    $lead = makeOwnedLeadWithProduct($owner);

    $payload = quotePayload($agent, ['lead_id' => $lead->id]);

    $response = test()
        ->actingAs($agent, 'user')
        ->post(route('admin.quotes.store'), $payload);

    expect($response->getStatusCode())->toBe(401)
        ->and(Quote::where('subject', $payload['subject'])->exists())->toBeFalse()
        ->and($lead->quotes()->count())->toBe(0);
});

it('still attaches a new quote to the caller own lead', function () {
    $agent = makeQuoteScopeUser('individual');

    $lead = makeOwnedLeadWithProduct($agent);

    $payload = quotePayload($agent, ['lead_id' => $lead->id]);

    test()
        ->actingAs($agent, 'user')
        ->post(route('admin.quotes.store'), $payload)
        ->assertSessionHasNoErrors()
        ->assertRedirect();

    expect($lead->quotes()->pluck('subject')->all())->toBe([$payload['subject']]);
});

it('refuses to move a quote onto a lead owned by another user', function () {
    $owner = makeQuoteScopeUser('global');
    $agent = makeQuoteScopeUser('individual');

    $quote = makeOwnedQuote($agent);

    $lead = makeOwnedLeadWithProduct($owner);

    $response = test()
        ->actingAs($agent, 'user')
        ->put(route('admin.quotes.update', $quote->id), quotePayload($agent, ['lead_id' => $lead->id]));

    expect($response->getStatusCode())->toBe(401)
        ->and($lead->quotes()->count())->toBe(0);
});

it('keeps a quote linked to a lead that has left the caller scope', function () {
    $owner = makeQuoteScopeUser('global');
    $agent = makeQuoteScopeUser('individual');

    $quote = makeOwnedQuote($agent);

    $lead = makeOwnedLeadWithProduct($owner);

    $lead->quotes()->attach($quote->id);

    test()
        ->actingAs($agent, 'user')
        ->put(route('admin.quotes.update', $quote->id), quotePayload($agent, ['lead_id' => $lead->id]))
        ->assertSessionHasNoErrors()
        ->assertRedirect();

    expect($quote->fresh()->leads->pluck('id')->all())->toBe([$lead->id]);
});

/**
 * `old('lead_id')` is flashed from the caller's own failed submission, so it may name any lead.
 */
it('does not fill the quote edit form from another user lead flashed as old input', function () {
    $owner = makeQuoteScopeUser('global');
    $agent = makeQuoteScopeUser('individual');

    $quote = makeOwnedQuote($agent);

    $lead = makeOwnedLeadWithProduct($owner);

    $response = test()
        ->actingAs($agent, 'user')
        ->withSession(['_old_input' => ['lead_id' => $lead->id]])
        ->get(route('admin.quotes.edit', $quote->id))
        ->assertOk();

    expect($response->getContent())
        ->not->toContain('Confidential Appliance')
        ->not->toContain($lead->title);
});

/**
 * The look-up endpoints behind filter dropdowns and lookup fields returned matching names from the
 * whole installation.
 */
function scopedPersonPair(User $owner, User $agent, string $token): array
{
    return [
        'foreign' => Person::create([
            'name' => $token.' Foreign',
            'user_id' => $owner->id,
            'emails' => [['value' => 'foreign-'.bin2hex(random_bytes(4)).'@example.invalid', 'label' => 'work']],
        ]),
        'own' => Person::create([
            'name' => $token.' Own',
            'user_id' => $agent->id,
            'emails' => [['value' => 'own-'.bin2hex(random_bytes(4)).'@example.invalid', 'label' => 'work']],
        ]),
    ];
}

it('lists only in-scope records in a datagrid filter look-up', function () {
    $owner = makeQuoteScopeUser('global');
    $agent = makeQuoteScopeUser('individual');

    $token = 'Zlookup'.bin2hex(random_bytes(4));

    scopedPersonPair($owner, $agent, $token);

    $labels = test()
        ->actingAs($agent, 'user')
        ->getJson(route('admin.datagrid.look_up', [
            'datagrid_id' => Crypt::encryptString(LeadDataGrid::class),
            'column' => 'person_name',
            'search' => $token,
        ]))
        ->assertOk()
        ->json('*.label');

    expect($labels)->toBe([$token.' Own']);
});

it('lists only in-scope records in a kanban filter look-up', function () {
    $owner = makeQuoteScopeUser('global');
    $agent = makeQuoteScopeUser('individual');

    $token = 'Zlookup'.bin2hex(random_bytes(4));

    scopedPersonPair($owner, $agent, $token);

    $labels = test()
        ->actingAs($agent, 'user')
        ->getJson(route('admin.leads.kanban.look_up', ['column' => 'person.id', 'search' => $token]))
        ->assertOk()
        ->json('*.label');

    expect($labels)->toBe([$token.' Own']);
});

it('lists and names only in-scope records in an attribute lookup', function () {
    $owner = makeQuoteScopeUser('global');
    $agent = makeQuoteScopeUser('individual');

    $token = 'Zlookup'.bin2hex(random_bytes(4));

    $persons = scopedPersonPair($owner, $agent, $token);

    test()->actingAs($agent, 'user');

    /**
     * Compared by id: a person created directly, rather than through the repository, has no
     * attribute values, so its serialized `name` is empty.
     */
    expect(test()->getJson(route('admin.settings.attributes.lookup', 'persons').'?query='.$token)->json('*.id'))
        ->toBe([$persons['own']->id]);

    expect(test()->getJson(route('admin.settings.attributes.lookup_entity', 'persons').'?query='.$persons['foreign']->id)->json('id'))
        ->toBeNull();

    expect(test()->getJson(route('admin.settings.attributes.lookup_entity', 'persons').'?query='.$persons['own']->id)->json())
        ->toBe(['id' => $persons['own']->id, 'name' => $persons['own']->name]);
});

it('lists only themself to a group user who belongs to no group', function () {
    $agent = makeQuoteScopeUser('group');

    expect(test()->actingAs($agent, 'user')->getJson(route('admin.settings.attributes.lookup', 'users'))->json('*.id'))
        ->toBe([$agent->id]);
});

it('still lists every matching record to a global user', function () {
    $owner = makeQuoteScopeUser('individual');
    $manager = makeQuoteScopeUser('global');

    $token = 'Zlookup'.bin2hex(random_bytes(4));

    $persons = scopedPersonPair($owner, makeQuoteScopeUser('individual'), $token);

    expect(test()->actingAs($manager, 'user')->getJson(route('admin.settings.attributes.lookup', 'persons').'?query='.$token)->json('*.id'))
        ->toEqualCanonicalizing([$persons['foreign']->id, $persons['own']->id]);
});

/**
 * Tag attach and detach wrote to any lead, person or mail id.
 */
it('refuses to tag or untag a record outside the caller scope', function (string $kind) {
    $owner = makeQuoteScopeUser('global');
    $agent = makeQuoteScopeUser('individual');

    $tag = Tag::create(['name' => 'Scope Tag '.bin2hex(random_bytes(4)), 'color' => '#000000', 'user_id' => $agent->id]);

    $lead = makeOwnedLeadWithProduct($owner);

    [$record, $route] = match ($kind) {
        'lead' => [$lead, 'admin.leads.tags'],
        'person' => [$lead->person, 'admin.contacts.persons.tags'],
        'mail' => [Email::create([
            'subject' => 'Owned thread',
            'reply' => 'Owned body.',
            'source' => 'email',
            'user_type' => 'person',
            'folders' => ['inbox'],
            'name' => 'Owner Contact',
            'unique_id' => bin2hex(random_bytes(8)),
            'message_id' => bin2hex(random_bytes(8)).'@example.invalid',
            'lead_id' => $lead->id,
        ]), 'admin.mail.tags'],
    };

    test()->actingAs($agent, 'user');

    expect(test()->postJson(route($route.'.attach', $record->id), ['tag_id' => $tag->id])->getStatusCode())->toBe(401)
        ->and($record->tags()->count())->toBe(0);

    $record->tags()->attach($tag->id);

    expect(test()->deleteJson(route($route.'.detach', $record->id), ['tag_id' => $tag->id])->getStatusCode())->toBe(401)
        ->and($record->tags()->count())->toBe(1);
})->with(['lead', 'person', 'mail']);

it('still lets the caller tag their own lead', function () {
    $agent = makeQuoteScopeUser('individual');

    $tag = Tag::create(['name' => 'Scope Tag '.bin2hex(random_bytes(4)), 'color' => '#000000', 'user_id' => $agent->id]);

    $lead = makeOwnedLeadWithProduct($agent);

    test()
        ->actingAs($agent, 'user')
        ->postJson(route('admin.leads.tags.attach', $lead->id), ['tag_id' => $tag->id])
        ->assertOk();

    expect($lead->tags()->pluck('tags.id')->all())->toBe([$tag->id]);
});

/**
 * Quotes filed under another user's contact, and detached from another user's lead.
 */
it('refuses to file a quote under another user contact', function () {
    $owner = makeQuoteScopeUser('global');
    $agent = makeQuoteScopeUser('individual');

    $foreignPerson = makeOwnedLeadWithProduct($owner)->person;

    $payload = quotePayload($agent, ['person_id' => $foreignPerson->id]);

    expect(test()->actingAs($agent, 'user')->post(route('admin.quotes.store'), $payload)->getStatusCode())->toBe(401)
        ->and(Quote::where('subject', $payload['subject'])->exists())->toBeFalse();
});

it('still files a quote under the contact of the caller own lead', function () {
    $owner = makeQuoteScopeUser('global');
    $agent = makeQuoteScopeUser('individual');

    $lead = makeOwnedLeadWithProduct($agent);

    $lead->person->update(['user_id' => $owner->id]);

    $payload = quotePayload($agent, ['lead_id' => $lead->id, 'person_id' => $lead->person_id]);

    test()->actingAs($agent, 'user')
        ->post(route('admin.quotes.store'), $payload)
        ->assertSessionHasNoErrors()
        ->assertRedirect();

    expect(Quote::where('subject', $payload['subject'])->value('person_id'))->toBe($lead->person_id);
});

it('refuses to detach another user quote by owning one of its leads', function () {
    $owner = makeQuoteScopeUser('global');
    $agent = makeQuoteScopeUser('individual');

    $quote = makeOwnedQuote($owner);

    $victimLead = makeOwnedLeadWithProduct($owner);
    $agentLead = makeOwnedLeadWithProduct($agent);

    $victimLead->quotes()->attach($quote->id);
    $agentLead->quotes()->attach($quote->id);

    expect(test()->actingAs($agent, 'user')->deleteJson(route('admin.leads.quotes.delete', $quote->id))->getStatusCode())->toBe(401)
        ->and($victimLead->quotes()->count())->toBe(1);
});

/**
 * `lookupEntity` returned the whole record — a lead's value, description and owner — for any id.
 */
it('names a lead by id without its value or description', function () {
    $agent = makeQuoteScopeUser('individual');

    $lead = makeOwnedLeadWithProduct($agent);

    $lead->update(['lead_value' => 987654.32, 'description' => 'Secret margin note']);

    test()
        ->actingAs($agent, 'user')
        ->getJson(route('admin.settings.attributes.lookup_entity', 'leads').'?query='.$lead->id)
        ->assertExactJson(['id' => $lead->id, 'name' => $lead->title]);
});

it('does not embed a kept out-of-scope lead in the quote edit page', function () {
    $owner = makeQuoteScopeUser('global');
    $agent = makeQuoteScopeUser('individual');

    $quote = makeOwnedQuote($agent);

    $lead = makeOwnedLeadWithProduct($owner);

    /**
     * Stored as an attribute value, as the lead form stores it: that is where the record the
     * lookup returns reads its custom fields from.
     */
    AttributeValue::create([
        'entity_type' => 'leads',
        'entity_id' => $lead->id,
        'attribute_id' => webFormAttributeFor('leads', 'description')->id,
        'text_value' => 'Secret margin note',
    ]);

    $lead->quotes()->attach($quote->id);

    $content = test()
        ->actingAs($agent, 'user')
        ->get(route('admin.quotes.edit', $quote->id))
        ->assertOk()
        ->getContent();

    expect($content)
        ->toContain($lead->title)
        ->not->toContain('Secret margin note');
});
