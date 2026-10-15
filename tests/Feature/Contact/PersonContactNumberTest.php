<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Webkul\Contact\Repositories\PersonRepository;

/**
 * A contact number is optional, but the identity string a person is stored under was built from the
 * first one without checking that a first one survived.
 *
 * The phone field always submits at least one row, so saving a person with the phone left blank
 * sent a single row whose value had been turned into null by the empty-string middleware. Filtering
 * those rows away left an empty array, and reading index 0 of it raised a warning that the error
 * handler turns into an `ErrorException` — answering the save with an HTTP 500.
 *
 * Filtering also preserved the original keys, so a blank row followed by a filled one left the
 * surviving number at index 1: the same missing index 0, and an identity string that gained a
 * separator with no number behind it.
 */
uses(DatabaseTransactions::class);

/**
 * The method is protected and shared by create() and update(), so it is exercised directly rather
 * than through both callers.
 */
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

    /**
     * Nothing survived the filter, so the identity string ends at the email rather than trailing a
     * separator with no number behind it.
     */
    expect($data['unique_id'])->not->toEndWith('|');
});

it('keeps a filled contact number that follows a blank one', function () {
    $data = sanitizePersonData([
        ['value' => null, 'label' => 'work'],
        ['value' => '555-1234', 'label' => 'home'],
    ]);

    /**
     * Reindexed, so the surviving number is the first one and is the one the identity string is
     * built from.
     */
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
    /**
     * A row submitted without the key, rather than with a null value, must not be read for one.
     */
    $data = sanitizePersonData([['label' => 'work']]);

    expect($data['contact_numbers'])->toBe([]);

    expect($data['unique_id'])->not->toEndWith('|');
});
