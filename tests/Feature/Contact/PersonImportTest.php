<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Webkul\Contact\Models\Person;
use Webkul\DataTransfer\Helpers\Import;
use Webkul\DataTransfer\Models\Import as ImportModel;

uses(DatabaseTransactions::class);

beforeEach(function () {
    $this->diskRoot = sys_get_temp_dir().'/krayin-person-import-'.bin2hex(random_bytes(8));

    config(['filesystems.disks.local.root' => $this->diskRoot.'/app']);

    Storage::forgetDisk('local');
});

afterEach(function () {
    File::deleteDirectory($this->diskRoot);
});

it('imports a person without a contact number', function () {
    $csv = implode("\n", [
        'name,emails,contact_numbers,organization_id,job_title,user_id',
        'No Phone,"[{""label"": ""work"", ""value"": ""no-phone@example.invalid""}]",,,,1',
    ])."\n";

    $this->actingAs(getDefaultAdmin(), 'user')
        ->post(route('admin.settings.data_transfer.imports.store'), [
            'type' => 'persons',
            'action' => Import::ACTION_APPEND,
            'validation_strategy' => Import::VALIDATION_STRATEGY_SKIP_ERRORS,
            'allowed_errors' => 10,
            'field_separator' => ',',
            'file' => UploadedFile::fake()->createWithContent('persons.csv', $csv),
        ]);

    $import = ImportModel::query()->latest('id')->firstOrFail();

    $this->get(route('admin.settings.data_transfer.imports.validate', $import->id))
        ->assertJsonPath('is_valid', true);

    $this->get(route('admin.settings.data_transfer.imports.start', $import->id))
        ->assertOk();

    expect(Person::query()->where('name', 'No Phone')->value('unique_id'))
        ->toBe('1||no-phone@example.invalid');
});
