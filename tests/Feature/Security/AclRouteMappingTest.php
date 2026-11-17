<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Webkul\Attribute\Models\Attribute;
use Webkul\Attribute\Models\AttributeOption;
use Webkul\Attribute\Models\AttributeValue;
use Webkul\Contact\Models\Person;
use Webkul\Lead\Models\Lead;
use Webkul\Lead\Models\Source;
use Webkul\Lead\Models\Type;
use Webkul\Product\Models\Product;
use Webkul\User\Models\Role;
use Webkul\User\Models\User;
use Webkul\Warehouse\Models\Warehouse;

uses(DatabaseTransactions::class);

const ACL_MISSING_ID = 999999999;

function aclProbeUser(array $permissions): User
{
    $role = Role::create([
        'name' => 'ACL Probe Role '.bin2hex(random_bytes(6)),
        'description' => 'Created by the ACL route mapping tests.',
        'permission_type' => 'custom',
        'permissions' => array_values($permissions),
    ]);

    return User::create([
        'name' => 'ACL Probe',
        'email' => 'acl-probe-'.bin2hex(random_bytes(6)).'@example.invalid',
        'password' => bcrypt('correct-horse-battery-staple'),
        'status' => 1,
        'role_id' => $role->id,
        'view_permission' => 'global',
    ]);
}

function customAttributeWithData(): Attribute
{
    $attribute = Attribute::create([
        'code' => 'acl_probe_'.bin2hex(random_bytes(4)),
        'name' => 'ACL probe',
        'type' => 'select',
        'entity_type' => 'leads',
        'is_required' => 0,
        'is_unique' => 0,
        'quick_add' => 0,
        'is_user_defined' => 1,
    ]);

    AttributeOption::create(['attribute_id' => $attribute->id, 'name' => 'Probe option', 'sort_order' => 1]);

    AttributeValue::create([
        'attribute_id' => $attribute->id,
        'entity_type' => 'leads',
        'entity_id' => ACL_MISSING_ID,
        'text_value' => 'synthetic',
    ]);

    return $attribute;
}

function attributeDataCounts(Attribute $attribute): array
{
    return [
        'attribute' => Attribute::query()->whereKey($attribute->id)->count(),
        'options' => AttributeOption::query()->where('attribute_id', $attribute->id)->count(),
        'values' => AttributeValue::query()->where('attribute_id', $attribute->id)->count(),
    ];
}

const ATTRIBUTE_VIEW_CREATE = ['settings', 'settings.automation', 'settings.automation.attributes', 'settings.automation.attributes.create'];

it('denies single attribute delete without the delete permission', function () {
    $attribute = customAttributeWithData();

    test()->actingAs(aclProbeUser(ATTRIBUTE_VIEW_CREATE), 'user')
        ->deleteJson(route('admin.settings.attributes.delete', $attribute->id))
        ->assertUnauthorized();

    expect(attributeDataCounts($attribute))->toBe(['attribute' => 1, 'options' => 1, 'values' => 1]);
});

it('denies attribute mass delete without the delete permission', function () {
    $attribute = customAttributeWithData();

    $response = test()->actingAs(aclProbeUser(ATTRIBUTE_VIEW_CREATE), 'user')
        ->postJson(route('admin.settings.attributes.mass_delete'), ['indices' => [$attribute->id]]);

    expect(attributeDataCounts($attribute))->toBe(['attribute' => 1, 'options' => 1, 'values' => 1]);

    $response->assertUnauthorized();
});

it('allows attribute mass delete with the delete permission and cascades its data', function () {
    $attribute = customAttributeWithData();

    test()->actingAs(aclProbeUser([...ATTRIBUTE_VIEW_CREATE, 'settings.automation.attributes.delete']), 'user')
        ->postJson(route('admin.settings.attributes.mass_delete'), ['indices' => [$attribute->id]])
        ->assertOk();

    expect(attributeDataCounts($attribute))->toBe(['attribute' => 0, 'options' => 0, 'values' => 0]);
});

it('allows attribute mass delete for an administrator', function () {
    $attribute = customAttributeWithData();

    test()->actingAs(getDefaultAdmin(), 'user')
        ->postJson(route('admin.settings.attributes.mass_delete'), ['indices' => [$attribute->id]])
        ->assertOk();

    expect(attributeDataCounts($attribute)['attribute'])->toBe(0);
});

dataset('unmapped mutating routes', [
    'lead stage update' => [['leads', 'leads.view'], 'leads.edit', 'PUT', 'admin.leads.stage.update', [ACL_MISSING_ID]],
    'lead attributes update' => [['leads', 'leads.view'], 'leads.edit', 'PUT', 'admin.leads.attributes.update', [ACL_MISSING_ID]],
    'lead product add' => [['leads', 'leads.view'], 'leads.edit', 'PUT', 'admin.leads.product.add', [ACL_MISSING_ID]],
    'lead product remove' => [['leads', 'leads.view'], 'leads.edit', 'DELETE', 'admin.leads.product.remove', [ACL_MISSING_ID]],
    'lead email detach' => [['leads', 'leads.view'], 'leads.edit', 'DELETE', 'admin.leads.emails.detach', [ACL_MISSING_ID]],
    'lead email send' => [['leads', 'leads.view'], 'mail.compose', 'POST', 'admin.leads.emails.store', [ACL_MISSING_ID]],
    'lead create by AI' => [['leads', 'leads.view'], 'leads.create', 'POST', 'admin.leads.create_by_ai', []],
    'lead quote unlink' => [['quotes', 'quotes.mail'], 'quotes.delete', 'DELETE', 'admin.leads.quotes.delete', [ACL_MISSING_ID]],
    'person tag attach' => [['contacts', 'contacts.persons', 'contacts.persons.view'], 'contacts.persons.edit', 'POST', 'admin.contacts.persons.tags.attach', [ACL_MISSING_ID]],
    'person tag detach' => [['contacts', 'contacts.persons', 'contacts.persons.view'], 'contacts.persons.edit', 'DELETE', 'admin.contacts.persons.tags.detach', [ACL_MISSING_ID]],
    'product tag attach' => [['products', 'products.view'], 'products.edit', 'POST', 'admin.products.tags.attach', [ACL_MISSING_ID]],
    'product tag detach' => [['products', 'products.view'], 'products.edit', 'DELETE', 'admin.products.tags.detach', [ACL_MISSING_ID]],
    'product inventory store' => [['products', 'products.view'], 'products.edit', 'POST', 'admin.products.inventories.store', [ACL_MISSING_ID]],
    'warehouse tag attach' => [['settings', 'settings.inventory', 'settings.inventory.warehouse'], 'settings.inventory.warehouse.edit', 'POST', 'admin.settings.warehouses.tags.attach', [ACL_MISSING_ID]],
    'warehouse tag detach' => [['settings', 'settings.inventory', 'settings.inventory.warehouse'], 'settings.inventory.warehouse.edit', 'DELETE', 'admin.settings.warehouses.tags.detach', [ACL_MISSING_ID]],
    'location store' => [['settings', 'settings.other_settings', 'settings.other_settings.tags'], 'settings.inventory.warehouse.edit', 'POST', 'admin.settings.locations.store', []],
    'location update' => [['settings', 'settings.other_settings', 'settings.other_settings.tags'], 'settings.inventory.warehouse.edit', 'PUT', 'admin.settings.locations.update', [ACL_MISSING_ID]],
    'location delete' => [['settings', 'settings.other_settings', 'settings.other_settings.tags'], 'settings.inventory.warehouse.edit', 'DELETE', 'admin.settings.locations.delete', [ACL_MISSING_ID]],
]);

it('stops a mutating route at the ACL gate without its permission', function (array $granted, string $required, string $method, string $route, array $params) {
    $response = test()->actingAs(aclProbeUser($granted), 'user')
        ->json($method, route($route, $params));

    expect($response->status())->toBe(401, "{$route} passed the ACL gate with only [".implode(', ', $granted).']');
})->with('unmapped mutating routes');

it('lets a mutating route past the ACL gate with its permission', function (array $granted, string $required, string $method, string $route, array $params) {
    $response = test()->actingAs(aclProbeUser([...$granted, $required]), 'user')
        ->json($method, route($route, $params));

    expect($response->status())->not->toBe(401, "{$route} was refused at the ACL gate despite holding {$required}");
})->with('unmapped mutating routes');

function aclUiLead(): Lead
{
    $pipeline = securityTestPipeline();

    return Lead::create([
        'title' => 'ACL UI lead '.uniqid(),
        'status' => 1,
        'user_id' => getDefaultAdmin()->id,
        'lead_pipeline_id' => $pipeline->id,
        'lead_pipeline_stage_id' => $pipeline->stages()->first()->id,
        'lead_source_id' => Source::query()->first()->id,
        'lead_type_id' => Type::query()->first()->id,
    ]);
}

function aclUiPerson(): Person
{
    return Person::create([
        'name' => 'ACL UI contact '.uniqid(),
        'emails' => [['value' => uniqid().'@example.invalid', 'label' => 'work']],
        'user_id' => getDefaultAdmin()->id,
    ]);
}

function aclUiProduct(): Product
{
    return Product::create([
        'sku' => 'ACL-UI-'.uniqid(),
        'name' => 'ACL UI product',
        'price' => 100,
        'quantity' => 10,
    ]);
}

function aclUiWarehouse(): Warehouse
{
    return Warehouse::create([
        'name' => 'ACL UI warehouse '.uniqid(),
        'contact_name' => 'ACL UI',
        'contact_emails' => [['value' => uniqid().'@example.invalid', 'label' => 'work']],
        'contact_numbers' => [['value' => '5550100', 'label' => 'work']],
        'contact_address' => ['address' => '1 Test Street', 'city' => 'Test', 'country' => 'IN', 'state' => 'UP', 'postcode' => '201301'],
    ]);
}

it('hides lead edit controls from a role without lead edit', function () {
    $lead = aclUiLead();

    test()->actingAs(aclProbeUser(['leads', 'leads.view']), 'user')
        ->get(route('admin.leads.view', $lead->id))
        ->assertOk()
        ->assertSee('canUpdate: false', false)
        ->assertDontSee(route('admin.leads.emails.detach', $lead->id), false)
        ->assertDontSee(trans('admin::app.leads.view.products.add-product'), false);

    test()->actingAs(aclProbeUser(['leads', 'leads.view']), 'user')
        ->get(route('admin.leads.index'))
        ->assertOk()
        ->assertSee(':disabled="true"', false);
});

it('shows lead edit controls to an administrator', function () {
    $lead = aclUiLead();

    test()->actingAs(getDefaultAdmin(), 'user')
        ->get(route('admin.leads.view', $lead->id))
        ->assertOk()
        ->assertSee('canUpdate: true', false)
        ->assertSee(route('admin.leads.emails.detach', $lead->id), false)
        ->assertSee(trans('admin::app.leads.view.products.add-product'), false);

    test()->actingAs(getDefaultAdmin(), 'user')
        ->get(route('admin.leads.index'))
        ->assertOk()
        ->assertSee(':disabled="false"', false);
});

it('renders tags and inventory read-only without the edit permission', function (array $permissions, Closure $page, array $hidden) {
    $response = test()->actingAs(aclProbeUser($permissions), 'user')->get($page());

    $response->assertOk()->assertSee(':editable="false"', false);

    foreach ($hidden as $marker) {
        $response->assertDontSee($marker, false);
    }
})->with([
    'person' => [['contacts', 'contacts.persons', 'contacts.persons.view'], fn () => route('admin.contacts.persons.view', aclUiPerson()->id), []],
    'product' => [['products', 'products.view'], fn () => route('admin.products.view', aclUiProduct()->id), ['@click="addWarehouse(warehouse)"', '@click="selectWarehouse(warehouse)"']],
    'warehouse' => [['settings', 'settings.inventory', 'settings.inventory.warehouse'], fn () => route('admin.settings.warehouses.view', aclUiWarehouse()->id), ['@click="openModal()"', '@click="remove(location)"']],
]);

it('renders tags and inventory editable for an administrator', function (Closure $page, array $shown) {
    $response = test()->actingAs(getDefaultAdmin(), 'user')->get($page());

    $response->assertOk()->assertSee(':editable="true"', false);

    foreach ($shown as $marker) {
        $response->assertSee($marker, false);
    }
})->with([
    'person' => [fn () => route('admin.contacts.persons.view', aclUiPerson()->id), []],
    'product' => [fn () => route('admin.products.view', aclUiProduct()->id), ['@click="addWarehouse(warehouse)"', '@click="selectWarehouse(warehouse)"']],
    'warehouse' => [fn () => route('admin.settings.warehouses.view', aclUiWarehouse()->id), ['@click="openModal()"', '@click="remove(location)"']],
]);
