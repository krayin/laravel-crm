<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Webkul\Contact\Repositories\PersonRepository;

uses(DatabaseTransactions::class);

function sanitizePersonData(array $contactNumbers): array
{
    $repository = app(PersonRepository::class);

    $method = new ReflectionMethod($repository, 'sanitizeRequestedPersonData');

    $method->setAccessible(true);

    return $method->invoke($repository, [
        'name' => 'Phone Case',
        'user_id' => 1,
        'emails' => [['value' => 'phone-case@example.invalid', 'label' => 'work']],
        'contact_numbers' => $contactNumbers,
    ]);
}

it('saves a person whose only contact number is blank', function () {
    $data = sanitizePersonData([['value' => null, 'label' => 'work']]);

    expect($data['contact_numbers'])->toBe([]);

    expect($data['unique_id'])->not->toEndWith('|');
});

it('keeps a filled contact number that follows a blank one', function () {
    $data = sanitizePersonData([
        ['value' => null, 'label' => 'work'],
        ['value' => '555-1234', 'label' => 'home'],
    ]);

    expect($data['contact_numbers'])->toHaveCount(1);

    expect($data['contact_numbers'][0]['value'])->toBe('555-1234');

    expect($data['unique_id'])->toEndWith('|555-1234');
});

it('builds the identity from the first of several contact numbers', function () {
    $data = sanitizePersonData([
        ['value' => '555-0001', 'label' => 'work'],
        ['value' => '555-0002', 'label' => 'home'],
    ]);

    expect($data['contact_numbers'])->toHaveCount(2);

    expect($data['unique_id'])->toEndWith('|555-0001');
});

it('saves a person when every contact number row is blank', function () {
    $data = sanitizePersonData([
        ['value' => null, 'label' => 'work'],
        ['value' => null, 'label' => 'home'],
    ]);

    expect($data['contact_numbers'])->toBe([]);

    expect($data['unique_id'])->not->toEndWith('|');
});

it('saves a person when a contact number row carries no value at all', function () {
    $data = sanitizePersonData([['label' => 'work']]);

    expect($data['contact_numbers'])->toBe([]);

    expect($data['unique_id'])->not->toEndWith('|');
});
