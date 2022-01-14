<?php

use Webkul\Contact\Models\Person;

it('has all persons', function () {
    $admin = actingAsSanctumAuthenticatedAdmin();

    $response = test()->getJson(test()->versionRoute('contacts/persons'));

    $response
        ->assertOK()
        ->assertJson([
            'data' => [],
        ]);
});

test('check person with specific id', function () {
    $admin = actingAsSanctumAuthenticatedAdmin();

    $person = Person::factory()->create();

    $response = test()->getJson(test()->versionRoute('contacts/persons/' . $person->id));

    $response
        ->assertOK()
        ->assertJson([
            'data' => [
                'id'   => $person->id,
                'name' => $person->name,
            ],
        ]);
});
