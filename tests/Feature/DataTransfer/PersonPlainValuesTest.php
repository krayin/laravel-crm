<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Storage;
use Webkul\Contact\Models\Person;
use Webkul\DataTransfer\Helpers\Error;
use Webkul\DataTransfer\Helpers\Import;
use Webkul\DataTransfer\Helpers\Importers\Persons\Importer;
use Webkul\DataTransfer\Helpers\Sources\CSV;
use Webkul\DataTransfer\Models\Import as ImportModel;

uses(DatabaseTransactions::class);

function reviewPersonCsv(array $rows, string $action = Import::ACTION_APPEND): array
{
    Storage::fake('public');
    $file = fopen('php://temp', 'r+');
    fputcsv($file, ['name', 'emails', 'contact_numbers', 'user_id', 'organization_id']);

    foreach ($rows as $row) {
        fputcsv($file, [...$row, getDefaultAdmin()->id, '']);
    }

    rewind($file);
    Storage::disk('public')->put('persons.csv', stream_get_contents($file));
    fclose($file);

    $import = ImportModel::create([
        'type' => 'persons',
        'action' => $action,
        'validation_strategy' => 'skip-errors',
        'field_separator' => ',',
        'file_path' => 'persons.csv',
    ]);
    $errors = new Error;
    $importer = app(Importer::class)
        ->setImport($import)
        ->setErrorHelper($errors)
        ->setSource(new CSV('persons.csv'));
    $importer->validateData();

    return [$importer, $import, $errors];
}

function executePersonCsv(Importer $importer, ImportModel $import): void
{
    foreach ($import->batches as $batch) {
        $importer->importBatch($batch);
    }
}

it('imports plain values and matches the existing person when a later CSV changes their phone', function () {
    [$importer, $import, $errors] = reviewPersonCsv([
        ['CSV Person', ' csv.person@example.com ', ' 00123456789 '],
    ]);
    expect($errors->getErrorsCount())->toBe(0);
    expect(json_decode($import->batches->first()->data[0]['contact_numbers'], true))
        ->toBe([['value' => '00123456789', 'label' => 'work']]);
    executePersonCsv($importer, $import);

    $person = Person::where('name', 'CSV Person')->sole();
    expect($person->emails)->toEqual([['value' => 'csv.person@example.com', 'label' => 'work']])
        ->and($person->contact_numbers)->toEqual([['value' => '00123456789', 'label' => 'work']])
        ->and($person->contact_numbers[0]['value'])->toBe('00123456789');

    [$importer, $import, $errors] = reviewPersonCsv([
        ['CSV Person Updated', 'csv.person@example.com', '00987654321'],
    ]);
    expect($errors->getErrorsCount())->toBe(0);
    executePersonCsv($importer, $import);
    expect(Person::where('name', 'like', 'CSV Person%')->count())->toBe(1)
        ->and($person->fresh()->name)->toBe('CSV Person Updated')
        ->and($person->fresh()->contact_numbers[0]['value'])->toBe('00987654321');
});

it('preserves JSON lists and their labels in a file that also contains plain values', function () {
    $emails = [['value' => 'json.person@example.com', 'label' => 'home'], ['value' => 'second@example.com', 'label' => 'work']];
    $phones = [['value' => '00123456789', 'label' => 'home']];
    [$importer, $import, $errors] = reviewPersonCsv([
        ['JSON Person', json_encode($emails), json_encode($phones)],
        ['Plain Person', 'plain.person@example.com', '00987654321'],
    ]);
    expect($errors->getErrorsCount())->toBe(0);
    executePersonCsv($importer, $import);
    expect(Person::where('name', 'JSON Person')->sole()->emails)->toEqual($emails)
        ->and(Person::where('name', 'JSON Person')->sole()->contact_numbers)->toEqual($phones)
        ->and(Person::where('name', 'Plain Person')->count())->toBe(1);
});

it('rejects invalid plain values and malformed JSON during review', function (string $email, string $phone) {
    [, $import, $errors] = reviewPersonCsv([['Invalid Person', $email, $phone]]);
    expect($errors->getErrorsCount())->toBeGreaterThan(0)
        ->and($import->batches()->count())->toBe(0);
})->with([
    'invalid email' => ['not-an-email', '123456789'],
    'invalid phone' => ['valid@example.com', 'not-a-phone'],
    'malformed JSON' => ['[{"value":"valid@example.com"', '123456789'],
]);

it('detects duplicate emails across plain and JSON rows', function () {
    [, $import, $errors] = reviewPersonCsv([
        ['First Person', 'duplicate@example.com', '123456789'],
        ['Second Person', json_encode([['value' => 'duplicate@example.com', 'label' => 'home']]), '987654321'],
    ]);
    expect($errors->getErrorsCount())->toBeGreaterThan(0)
        ->and($import->batches->first()->data)->toHaveCount(1);
});

it('supports deleting an existing person using a plain email', function () {
    $person = Person::create([
        'name' => 'Delete CSV Person',
        'emails' => [['value' => 'delete.csv@example.com', 'label' => 'home']],
    ]);
    [$importer, $import, $errors] = reviewPersonCsv([
        ['Delete CSV Person', 'delete.csv@example.com', ''],
    ], Import::ACTION_DELETE);
    expect($errors->getErrorsCount())->toBe(0);
    executePersonCsv($importer, $import);
    expect(Person::find($person->id))->toBeNull();
});

it('imports a plain email with an empty optional phone', function (string $phone) {
    [$importer, $import, $errors] = reviewPersonCsv([
        ['No Phone Person', 'no.phone@example.com', $phone],
    ]);
    expect($errors->getErrorsCount())->toBe(0);
    executePersonCsv($importer, $import);
    expect(Person::where('name', 'No Phone Person')->sole()->emails[0]['value'])
        ->toBe('no.phone@example.com');
})->with(['', 'null', '[]']);
