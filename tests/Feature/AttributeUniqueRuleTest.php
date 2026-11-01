<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Webkul\Attribute\Models\Attribute;

uses(DatabaseTransactions::class);

/**
 * The attribute code is unique within its entity type.
 *
 * The entity type from the request was concatenated into the `unique` rule string, so a value
 * carrying commas shifted the rule's positional arguments and turned the duplicate check into a
 * condition that matched no row.
 */
function storeAttributeAsAdmin(string $code, string $entityType)
{
    return test()
        ->actingAs(getDefaultAdmin(), 'user')
        ->withHeader('X-Requested-With', 'XMLHttpRequest')
        ->postJson(route('admin.settings.attributes.store'), attributeUniqueRulePayload($code, $entityType));
}

function attributeUniqueRulePayload(string $code, string $entityType): array
{
    return [
        'code' => $code,
        'name' => 'Unique Rule Attribute',
        'type' => 'text',
        'entity_type' => $entityType,
    ];
}

it('rejects a duplicate attribute code for the same entity type', function () {
    $code = 'uniq_'.uniqid();

    storeAttributeAsAdmin($code, 'leads')
        ->assertOk();

    storeAttributeAsAdmin($code, 'leads')
        ->assertUnprocessable()
        ->assertJsonValidationErrors('code');

    expect(Attribute::query()->where('code', $code)->count())->toBe(1);
});

it('treats a comma in the entity type as part of the value when checking for duplicates', function () {
    $code = 'uniq_'.uniqid();
    $entityType = 'leads,code,different_scope_xyz';

    storeAttributeAsAdmin($code, $entityType)
        ->assertOk();

    storeAttributeAsAdmin($code, $entityType)
        ->assertUnprocessable()
        ->assertJsonValidationErrors('code');

    expect(Attribute::query()->where('code', $code)->count())->toBe(1);
});
