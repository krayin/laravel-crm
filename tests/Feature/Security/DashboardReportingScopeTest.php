<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Webkul\Contact\Repositories\PersonRepository;
use Webkul\Lead\Models\Lead;
use Webkul\Product\Models\Product;
use Webkul\User\Models\Role;
use Webkul\User\Models\User;

uses(DatabaseTransactions::class);

/**
 * The dashboard's top-customer and top-product reports are held to the reader's data scope.
 *
 * They were computed across every record, and the customer rows carry the contact's name, email
 * addresses and telephone numbers — so a user restricted to their own records could read the
 * details of people the person listing and search both refuse to show them.
 */
function dashboardScopeUser(string $viewPermission): User
{
    $role = Role::create([
        'name' => 'dashboard-'.uniqid(),
        'description' => 'test',
        'permission_type' => 'all',
    ]);

    return User::create([
        'name' => 'Dashboard User',
        'email' => uniqid().'@example.com',
        'password' => bcrypt('password'),
        'status' => 1,
        'view_permission' => $viewPermission,
        'role_id' => $role->id,
    ]);
}

/**
 * A closed lead worth `$value`, owned by `$owner`, against a contact of their own.
 */
function dashboardClosedLead(User $owner, string $email, int $value): Lead
{
    $pipeline = securityTestPipeline();

    $person = app(PersonRepository::class)->create([
        'name' => 'Dashboard Person '.uniqid(),
        'entity_type' => 'persons',
        'emails' => [['value' => $email, 'label' => 'work']],
        'user_id' => $owner->id,
    ]);

    return Lead::create([
        'title' => 'Dashboard Lead '.uniqid(),
        'user_id' => $owner->id,
        'person_id' => $person->id,
        'lead_value' => $value,
        'closed_at' => now()->subDay(),
        'lead_pipeline_id' => $pipeline->id,
        'lead_pipeline_stage_id' => $pipeline->stages()->value('id'),
    ]);
}

function dashboardStats(User $actor, string $type): string
{
    return test()->actingAs($actor, 'user')
        ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
        ->get(route('admin.dashboard.stats').'?type='.$type
            .'&start='.now()->subMonth()->format('Y-m-d')
            .'&end='.now()->addDay()->format('Y-m-d'))
        ->getContent();
}

it('does not report another owner\'s contact details', function () {
    $owner = dashboardScopeUser('global');

    $outsider = dashboardScopeUser('individual');

    $email = 'vip-'.bin2hex(random_bytes(4)).'@bigcorp.invalid';

    dashboardClosedLead($owner, $email, 500000);

    expect(dashboardStats($outsider, 'top-persons'))->not->toContain($email);
});

it('still reports the reader\'s own contacts', function () {
    $owner = dashboardScopeUser('individual');

    $email = 'mine-'.bin2hex(random_bytes(4)).'@example.invalid';

    dashboardClosedLead($owner, $email, 1000);

    expect(dashboardStats($owner, 'top-persons'))->toContain($email);
});

/**
 * A reader who may see everything still does, so the scope is applied rather than the report
 * simply emptied.
 */
it('reports every contact to a reader whose scope covers them all', function () {
    $owner = dashboardScopeUser('individual');

    $administrator = dashboardScopeUser('global');

    $email = 'global-'.bin2hex(random_bytes(4)).'@example.invalid';

    dashboardClosedLead($owner, $email, 2000);

    expect(dashboardStats($administrator, 'top-persons'))->toContain($email);
});

/**
 * The product report counts the same leads, so it is scoped with them: its figures would otherwise
 * describe a pipeline the reader is not shown anywhere else.
 */
it('counts top products only from leads the reader may see', function () {
    $owner = dashboardScopeUser('global');

    $outsider = dashboardScopeUser('individual');

    $lead = dashboardClosedLead($owner, 'product-'.uniqid().'@example.invalid', 750000);

    $product = Product::create([
        'sku' => 'dash-'.uniqid(),
        'name' => 'Dashboard Product '.uniqid(),
        'price' => 750000,
        'quantity' => 1,
    ]);

    $lead->products()->create([
        'product_id' => $product->id,
        'name' => $product->name,
        'quantity' => 1,
        'price' => 750000,
        'amount' => 750000,
    ]);

    expect(dashboardStats($outsider, 'top-selling-products'))->not->toContain($product->name);

    expect(dashboardStats($owner, 'top-selling-products'))->toContain($product->name);
});
