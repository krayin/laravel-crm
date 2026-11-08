<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Webkul\DataTransfer\Helpers\Import;
use Webkul\DataTransfer\Models\Import as ImportModel;

uses(DatabaseTransactions::class);

/**
 * Unauthenticated exposure of import data and error reports.
 *
 * Import source files (`imports/{timestamp}-{original name}`) and error reports
 * (`imports/{timestamp}-error-report.csv`) were written to the `public` disk, which `storage:link`
 * puts in the web root. Anyone could fetch them from `/storage/imports/...` without signing in, and
 * the error report — a copy of every row plus an `error` column — was named by a timestamp alone.
 */
const IMPORT_RANDOM_PATH = '/^imports\/[A-Za-z0-9]{40}\.(csv|txt)$/';

/**
 * Point the public and private disks at directories of this test's own, so nothing touches the
 * developer's storage and parallel processes do not clear each other's files.
 */
beforeEach(function () {
    $this->diskRoot = sys_get_temp_dir().'/krayin-import-'.bin2hex(random_bytes(8));

    config([
        'filesystems.disks.local.root' => $this->diskRoot.'/app',
        'filesystems.disks.public.root' => $this->diskRoot.'/app/public',
    ]);

    Storage::forgetDisk(['local', 'public']);
});

afterEach(function () {
    File::deleteDirectory($this->diskRoot);
});

/**
 * A persons import whose second row names a user that does not exist, so validation produces an
 * error report.
 */
function sensitivePersonsCsv(): UploadedFile
{
    $csv = implode("\n", [
        'name,emails,contact_numbers,user_id',
        'Valid Person,"[{""label"": ""work"", ""value"": ""valid.person@example.com""}]","[{""label"": ""work"", ""value"": ""5550000001""}]",'.getDefaultAdmin()->id,
        'Secret Customer,"[{""label"": ""work"", ""value"": ""secret.customer@example.com""}]","[{""label"": ""work"", ""value"": ""5550000002""}]",999999999',
    ])."\n";

    return UploadedFile::fake()->createWithContent('customers-confidential.csv', $csv);
}

function importFormData(array $overrides = []): array
{
    return array_merge([
        'type' => 'persons',
        'action' => Import::ACTION_APPEND,
        'validation_strategy' => Import::VALIDATION_STRATEGY_SKIP_ERRORS,
        'allowed_errors' => 10,
        'field_separator' => ',',
    ], $overrides);
}

function storePersonsImport(UploadedFile $file): ImportModel
{
    test()->actingAs(getDefaultAdmin(), 'user')
        ->post(route('admin.settings.data_transfer.imports.store'), importFormData(['file' => $file]))
        ->assertSessionHasNoErrors()
        ->assertRedirect();

    return ImportModel::query()->latest('id')->firstOrFail();
}

/**
 * An HTML page whose first line is plain text, so the content is sniffed as `text/plain` and passes
 * the `mimes` rule; only the name gives it away.
 *
 * A real file rather than `UploadedFile::fake()`, whose MIME type comes from the name and so would
 * be rejected by `mimes` before the extension is ever looked at.
 */
function htmlPayloadNamedAsHtml(): UploadedFile
{
    return realUpload('report.html', "name,email\n<img src=x onerror=alert(document.domain)>\n");
}

/**
 * An upload whose MIME type is sniffed from its content, as in production.
 */
function realUpload(string $name, string $content): UploadedFile
{
    File::ensureDirectoryExists($directory = test()->diskRoot.'/uploads');

    file_put_contents($path = $directory.'/'.bin2hex(random_bytes(8)), $content);

    return new UploadedFile($path, $name, null, null, true);
}

it('stores the uploaded import file on the private disk under a random name', function () {
    $import = storePersonsImport(sensitivePersonsCsv());

    expect($import->file_path)->toMatch(IMPORT_RANDOM_PATH)
        ->and($import->file_path)->not->toContain('customers-confidential');

    Storage::disk(Import::STORAGE_DISK)->assertExists($import->file_path);

    expect(Storage::disk('public')->allFiles())->toBeEmpty();
});

it('writes the error report to the private disk under a random name', function () {
    $import = storePersonsImport(sensitivePersonsCsv());

    test()->actingAs(getDefaultAdmin(), 'user')
        ->get(route('admin.settings.data_transfer.imports.validate', $import->id))
        ->assertOk();

    $import->refresh();

    expect($import->error_file_path)->toMatch(IMPORT_RANDOM_PATH)
        ->and($import->error_file_path)->not->toContain('error-report');

    Storage::disk(Import::STORAGE_DISK)->assertExists($import->error_file_path);

    expect(Storage::disk('public')->allFiles())->toBeEmpty();
});

it('serves the error report, not the source file, from the error report download', function () {
    $import = storePersonsImport(sensitivePersonsCsv());

    test()->actingAs(getDefaultAdmin(), 'user')
        ->get(route('admin.settings.data_transfer.imports.validate', $import->id));

    $content = test()->actingAs(getDefaultAdmin(), 'user')
        ->get(route('admin.settings.data_transfer.imports.download_error_report', $import->id))
        ->assertOk()
        ->streamedContent();

    expect($content)->toContain('error')
        ->and($content)->toContain('secret.customer@example.com');
});

it('requires authentication to download import files', function () {
    $import = storePersonsImport(sensitivePersonsCsv());

    auth('user')->logout();

    test()->get(route('admin.settings.data_transfer.imports.download', $import->id))
        ->assertRedirect(route('admin.session.create'));

    test()->get(route('admin.settings.data_transfer.imports.download_error_report', $import->id))
        ->assertRedirect(route('admin.session.create'));
});

it('answers 404 when an import has no error report', function () {
    $import = storePersonsImport(sensitivePersonsCsv());

    test()->actingAs(getDefaultAdmin(), 'user')
        ->get(route('admin.settings.data_transfer.imports.download_error_report', $import->id))
        ->assertNotFound();
});

it('replaces the file on the private disk when an import is updated', function () {
    $import = storePersonsImport(sensitivePersonsCsv());

    $oldPath = $import->file_path;

    test()->actingAs(getDefaultAdmin(), 'user')
        ->put(
            route('admin.settings.data_transfer.imports.update', $import->id),
            importFormData(['file' => sensitivePersonsCsv()])
        )
        ->assertSessionHasNoErrors()
        ->assertRedirect();

    $import->refresh();

    expect($import->file_path)->toMatch(IMPORT_RANDOM_PATH)
        ->and($import->file_path)->not->toBe($oldPath);

    Storage::disk(Import::STORAGE_DISK)->assertMissing($oldPath);
    Storage::disk(Import::STORAGE_DISK)->assertExists($import->file_path);

    expect(Storage::disk('public')->allFiles())->toBeEmpty();
});

it('moves files earlier imports left on the public disk to the private disk', function () {
    Storage::disk('public')->put('imports/1791955455-leads.csv', 'name,emails');
    Storage::disk('public')->put('imports/1791955456-error-report.csv', 'name,emails,error');
    Storage::disk('public')->put('data-transfer/samples/leads.csv', 'sample');

    $migration = require base_path(
        'packages/Webkul/DataTransfer/src/Database/Migrations/2026_11_05_000000_move_import_files_to_private_disk.php'
    );

    $migration->up();

    Storage::disk(Import::STORAGE_DISK)->assertExists('imports/1791955455-leads.csv');
    Storage::disk(Import::STORAGE_DISK)->assertExists('imports/1791955456-error-report.csv');

    expect(Storage::disk('public')->allFiles('imports'))->toBeEmpty()
        ->and(Storage::disk(Import::STORAGE_DISK)->get('imports/1791955455-leads.csv'))->toBe('name,emails');

    /**
     * Files outside the imports directory, such as the downloadable samples, are left alone.
     */
    Storage::disk('public')->assertExists('data-transfer/samples/leads.csv');

    /**
     * Running it again changes nothing.
     */
    $migration->up();

    Storage::disk(Import::STORAGE_DISK)->assertExists('imports/1791955455-leads.csv');
});

/**
 * Stored XSS through the import upload.
 *
 * `mimes` checks only the sniffed content type, and the file used to be stored on the public disk
 * under its client name, so `report.html` with a plain-text first line was served as an HTML page
 * in the CRM origin.
 */
it('rejects an import file with a non-spreadsheet extension even when its content passes mimes', function () {
    $file = htmlPayloadNamedAsHtml();

    expect($file->guessExtension())->toBe('txt');

    $count = ImportModel::query()->count();

    test()->actingAs(getDefaultAdmin(), 'user')
        ->post(route('admin.settings.data_transfer.imports.store'), importFormData(['file' => $file]))
        ->assertSessionHasErrors('file');

    expect(ImportModel::query()->count())->toBe($count)
        ->and(Storage::disk(Import::STORAGE_DISK)->allFiles())->toBeEmpty()
        ->and(Storage::disk('public')->allFiles())->toBeEmpty();
});

it('rejects a non-spreadsheet extension when an import file is replaced', function () {
    $import = storePersonsImport(sensitivePersonsCsv());

    test()->actingAs(getDefaultAdmin(), 'user')
        ->put(
            route('admin.settings.data_transfer.imports.update', $import->id),
            importFormData(['file' => htmlPayloadNamedAsHtml()])
        )
        ->assertSessionHasErrors('file');

    expect($import->fresh()->file_path)->toBe($import->file_path);

    Storage::disk(Import::STORAGE_DISK)->assertExists($import->file_path);

    expect(Storage::disk(Import::STORAGE_DISK)->allFiles())->toHaveCount(1);
});

it('accepts an upper-case spreadsheet extension and stores it lower-cased', function () {
    $file = UploadedFile::fake()->createWithContent('CUSTOMERS.CSV', "name,emails\n");

    $import = storePersonsImport($file);

    expect($import->file_path)->toMatch(IMPORT_RANDOM_PATH)
        ->and($import->file_path)->toEndWith('.csv');
});

it('rejects an unknown import action or validation strategy', function (array $overrides, string $field) {
    test()->actingAs(getDefaultAdmin(), 'user')
        ->post(
            route('admin.settings.data_transfer.imports.store'),
            importFormData([...$overrides, 'file' => sensitivePersonsCsv()])
        )
        ->assertSessionHasErrors($field);
})->with([
    'action' => [['action' => 'truncate'], 'action'],
    'validation strategy' => [['validation_strategy' => 'ignore-everything'], 'validation_strategy'],
    'misspelt skip errors' => [['validation_strategy' => 'skip-erros'], 'validation_strategy'],
]);

it('accepts every action and validation strategy the import form offers', function (string $action, string $strategy) {
    $import = storePersonsImport(sensitivePersonsCsv());

    test()->actingAs(getDefaultAdmin(), 'user')
        ->put(
            route('admin.settings.data_transfer.imports.update', $import->id),
            importFormData(['action' => $action, 'validation_strategy' => $strategy])
        )
        ->assertSessionHasNoErrors()
        ->assertRedirect();

    expect($import->fresh())
        ->action->toBe($action)
        ->validation_strategy->toBe($strategy);
})->with([
    [Import::ACTION_APPEND, Import::VALIDATION_STRATEGY_STOP_ON_ERROR],
    [Import::ACTION_DELETE, Import::VALIDATION_STRATEGY_SKIP_ERRORS],
]);

it('renames the misspelt skip errors strategy saved by earlier imports', function () {
    $import = storePersonsImport(sensitivePersonsCsv());

    $import->update(['validation_strategy' => 'skip-erros']);

    $migration = require base_path(
        'packages/Webkul/DataTransfer/src/Database/Migrations/2026_11_05_000001_fix_skip_errors_import_validation_strategy.php'
    );

    $migration->up();

    expect($import->fresh()->validation_strategy)->toBe(Import::VALIDATION_STRATEGY_SKIP_ERRORS);
});

it('shows the original file name of an import created before random names', function () {
    expect((new ImportModel(['file_path' => 'imports/1791955455-leads.csv']))->file_name)->toBe('leads.csv')
        ->and((new ImportModel(['file_path' => 'imports/AbCd1234.csv']))->file_name)->toBe('AbCd1234.csv');
});

/**
 * A header-only, single-column or semicolon-delimited CSV is sniffed as `text/plain`, so the content
 * check has to keep accepting `txt` even though the name may not end in `.txt`.
 */
it('accepts a csv whose content is sniffed as plain text', function (string $content) {
    $file = realUpload('customers.csv', $content);

    expect($file->guessExtension())->toBe('txt');

    expect(storePersonsImport($file)->file_path)->toEndWith('.csv');
})->with([
    'semicolon delimited' => ["name;emails\nAlice;alice@example.invalid\n"],
    'header only' => ["name,emails\n"],
]);

it('rejects an import file named .txt', function () {
    test()->actingAs(getDefaultAdmin(), 'user')
        ->post(route('admin.settings.data_transfer.imports.store'), importFormData([
            'file' => realUpload('customers.txt', "name,emails\n"),
        ]))
        ->assertSessionHasErrors('file');
});

it('removes the previous error report when an import is validated again', function () {
    $import = storePersonsImport(sensitivePersonsCsv());

    test()->actingAs(getDefaultAdmin(), 'user')
        ->get(route('admin.settings.data_transfer.imports.validate', $import->id));

    $firstReport = $import->fresh()->error_file_path;

    test()->actingAs(getDefaultAdmin(), 'user')
        ->get(route('admin.settings.data_transfer.imports.validate', $import->id));

    $secondReport = $import->fresh()->error_file_path;

    expect($secondReport)->not->toBeNull()->not->toBe($firstReport);

    Storage::disk(Import::STORAGE_DISK)->assertMissing($firstReport);
    Storage::disk(Import::STORAGE_DISK)->assertExists($secondReport);
});
