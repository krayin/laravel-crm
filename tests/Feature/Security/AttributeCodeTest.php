<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Webkul\Attribute\Models\Attribute;
use Webkul\Attribute\Models\AttributeValue;
use Webkul\Contact\Models\Person;
use Webkul\User\Models\Role;
use Webkul\User\Models\User;

/**
 * Attribute codes, and the uniqueness checks built on them.
 */
uses(DatabaseTransactions::class);

function attributeScopeUser(string $viewPermission): User
{
    $role = Role::create([
        'name' => 'Attribute Scope Role '.bin2hex(random_bytes(6)),
        'description' => 'Created by the attribute code tests.',
        'permission_type' => 'all',
    ]);

    return User::create([
        'name' => 'Attribute Scope Probe',
        'email' => 'attribute-scope-'.bin2hex(random_bytes(6)).'@example.invalid',
        'password' => bcrypt('correct-horse-battery-staple'),
        'status' => 1,
        'role_id' => $role->id,
        'view_permission' => $viewPermission,
    ]);
}

function attributePayload(array $overrides = []): array
{
    return array_merge([
        'code' => 'custom_'.bin2hex(random_bytes(4)),
        'name' => 'Custom field',
        'type' => 'text',
        'entity_type' => 'leads',
    ], $overrides);
}

it('refuses a code that names one of the entity own columns or request keys', function (string $entityType, string $code) {
    $count = Attribute::query()->count();

    test()->actingAs(getDefaultAdmin(), 'user')
        ->post(route('admin.settings.attributes.store'), attributePayload(['entity_type' => $entityType, 'code' => $code]))
        ->assertSessionHasErrors('code');

    expect(Attribute::query()->count())->toBe($count);
})->with([
    'lead status' => ['leads', 'status'],
    'lead closed_at' => ['leads', 'closed_at'],
    'lead lost_reason' => ['leads', 'lost_reason'],
    'lead person_id' => ['leads', 'person_id'],
    'person id' => ['persons', 'id'],
    'person unique_id' => ['persons', 'unique_id'],
    'person email_id' => ['persons', 'email_id'],
    'quote lead_id' => ['quotes', 'lead_id'],
]);

it('refuses a code another attribute of the entity already has', function () {
    $existing = Attribute::create(attributePayload());

    test()->actingAs(getDefaultAdmin(), 'user')
        ->post(route('admin.settings.attributes.store'), attributePayload(['code' => $existing->code]))
        ->assertSessionHasErrors('code');
});

it('refuses an entity type that is not configured', function (string $entityType) {
    test()->actingAs(getDefaultAdmin(), 'user')
        ->post(route('admin.settings.attributes.store'), attributePayload(['entity_type' => $entityType]))
        ->assertSessionHasErrors('entity_type');
})->with([
    'unknown' => ['invoices'],
    'extra unique conditions' => ['leads,code,anything'],
]);

it('creates a custom attribute, always as user defined', function () {
    $payload = attributePayload(['is_user_defined' => 0]);

    test()->actingAs(getDefaultAdmin(), 'user')
        ->post(route('admin.settings.attributes.store'), $payload)
        ->assertSessionHasNoErrors()
        ->assertRedirect();

    expect(Attribute::where('code', $payload['code'])->value('is_user_defined'))->toBe(1);
});

it('does not let an update change the code, type, entity or system flag', function () {
    $attribute = Attribute::create(attributePayload(['is_user_defined' => 0]));

    test()->actingAs(getDefaultAdmin(), 'user')
        ->put(route('admin.settings.attributes.update', $attribute->id), [
            'code' => 'status',
            'type' => 'boolean',
            'entity_type' => 'persons',
            'is_user_defined' => 1,
            'name' => 'Renamed label',
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect();

    $attribute->refresh();

    expect($attribute->name)->toBe('Renamed label')
        ->and($attribute->code)->not->toBe('status')
        ->and($attribute->type)->toBe('text')
        ->and($attribute->entity_type)->toBe('leads')
        ->and($attribute->is_user_defined)->toBe(0);

    test()->actingAs(getDefaultAdmin(), 'user')
        ->deleteJson(route('admin.settings.attributes.delete', $attribute->id))
        ->assertStatus(400);

    expect(Attribute::find($attribute->id))->not->toBeNull();
});

/**
 * The live uniqueness check: `entity_id` names the record being edited, which is left out.
 */
function uniqueEmailAttribute(): Attribute
{
    $attribute = webFormAttributeFor('persons', 'emails');

    $attribute->update(['is_unique' => 1]);

    return $attribute;
}

function personWithEmail(User $owner, Attribute $emails, string $email): Person
{
    $person = Person::create([
        'name' => 'Unique Check Person',
        'user_id' => $owner->id,
        'emails' => [['value' => $email, 'label' => 'work']],
    ]);

    AttributeValue::create([
        'entity_type' => 'persons',
        'entity_id' => $person->id,
        'attribute_id' => $emails->id,
        'json_value' => [['value' => $email, 'label' => 'work']],
    ]);

    return $person;
}

function checkUnique(User $user, array $params): bool
{
    return test()->actingAs($user, 'user')
        ->getJson(route('admin.settings.attributes.check_unique_validation', $params))
        ->assertOk()
        ->json('validated');
}

it('does not tell whether another user record holds a value', function () {
    $owner = attributeScopeUser('global');
    $agent = attributeScopeUser('individual');

    $email = 'unique-'.bin2hex(random_bytes(4)).'@example.invalid';

    $victim = personWithEmail($owner, uniqueEmailAttribute(), $email);

    $params = ['entity_type' => 'persons', 'attribute_code' => 'emails', 'attribute_value' => $email];

    /**
     * With the victim's id the answer used to flip to "unique", proving the value was theirs.
     */
    expect(checkUnique($agent, $params))->toBeFalse()
        ->and(checkUnique($agent, [...$params, 'entity_id' => $victim->id]))->toBeFalse();
});

it('still leaves the caller own record out of the uniqueness check', function () {
    $agent = attributeScopeUser('individual');

    $email = 'unique-'.bin2hex(random_bytes(4)).'@example.invalid';

    $own = personWithEmail($agent, uniqueEmailAttribute(), $email);

    expect(checkUnique($agent, [
        'entity_type' => 'persons',
        'attribute_code' => 'emails',
        'attribute_value' => $email,
        'entity_id' => $own->id,
    ]))->toBeTrue();
});

it('answers nothing about the values of an attribute that need not be unique', function () {
    $owner = attributeScopeUser('global');
    $agent = attributeScopeUser('individual');

    $attribute = Attribute::create(attributePayload(['entity_type' => 'persons', 'is_unique' => 0]));

    $person = Person::create([
        'name' => 'Oracle Target',
        'user_id' => $owner->id,
        'emails' => [['value' => 'target-'.bin2hex(random_bytes(4)).'@example.invalid', 'label' => 'work']],
    ]);

    AttributeValue::create([
        'entity_type' => 'persons',
        'entity_id' => $person->id,
        'attribute_id' => $attribute->id,
        'text_value' => 'Confidential salary band C',
    ]);

    expect(checkUnique($agent, [
        'entity_type' => 'persons',
        'attribute_code' => $attribute->code,
        'attribute_value' => 'Confidential salary band C',
    ]))->toBeTrue();
});

it('answers 404 rather than failing for an unknown attribute code', function () {
    test()->actingAs(getDefaultAdmin(), 'user')
        ->getJson(route('admin.settings.attributes.check_unique_validation', [
            'entity_type' => 'persons',
            'attribute_code' => 'no_such_attribute',
            'attribute_value' => 'anything',
        ]))
        ->assertNotFound();
});
