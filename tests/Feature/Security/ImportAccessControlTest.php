<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Webkul\DataTransfer\Helpers\Import;
use Webkul\DataTransfer\Models\Import as ImportModel;
use Webkul\User\Models\Role;
use Webkul\User\Models\User;

/**
 * Access control for bulk import (Settings → Data Transfer → Imports), complementing
 * ImportFileExposureTest (file storage and upload validation).
 *
 * The "Import" permission named a route that does not exist, so it guarded nothing, and storing,
 * validating and running an import were unmapped: each fell back to any data transfer permission.
 * A role allowed only to view imports could upload a file and run it — creating, updating or
 * deleting records in bulk.
 */
uses(DatabaseTransactions::class);

const IMPORT_PERMISSIONS = [
    'settings',
    'settings.automation',
    'settings.automation.data_transfer',
    'settings.automation.data_transfer.imports',
    'settings.automation.data_transfer.imports.create',
    'settings.automation.data_transfer.imports.edit',
    'settings.automation.data_transfer.imports.delete',
    'settings.automation.data_transfer.imports.import',
];

const IMPORT_VIEW = 'settings.automation.data_transfer.imports';

beforeEach(function () {
    $this->diskRoot = sys_get_temp_dir().'/krayin-import-acl-'.bin2hex(random_bytes(8));

    config([
        'filesystems.disks.local.root' => $this->diskRoot.'/app',
        'filesystems.disks.public.root' => $this->diskRoot.'/app/public',
    ]);

    Storage::forgetDisk(['local', 'public']);
});

afterEach(function () {
    File::deleteDirectory($this->diskRoot);
});

function importRoleUser(array $permissions): User
{
    $role = Role::create([
        'name' => 'Import ACL Role '.bin2hex(random_bytes(6)),
        'description' => 'Created by the import access control tests.',
        'permission_type' => 'custom',
        'permissions' => array_values($permissions),
    ]);

    return User::create([
        'name' => 'Import ACL Probe',
        'email' => 'import-acl-'.bin2hex(random_bytes(6)).'@example.invalid',
        'password' => bcrypt('correct-horse-battery-staple'),
        'status' => 1,
        'role_id' => $role->id,
        'view_permission' => 'global',
    ]);
}

function existingImport(): ImportModel
{
    $path = Import::STORAGE_DIRECTORY.'/'.bin2hex(random_bytes(20)).'.csv';

    Storage::disk(Import::STORAGE_DISK)->put($path, "name,emails\n");

    return ImportModel::create([
        'type' => 'persons',
        'action' => Import::ACTION_APPEND,
        'validation_strategy' => Import::VALIDATION_STRATEGY_SKIP_ERRORS,
        'allowed_errors' => 10,
        'field_separator' => ',',
        'process_in_queue' => 0,
        'state' => Import::STATE_PENDING,
        'file_path' => $path,
    ]);
}

function importFormPayload(array $overrides = []): array
{
    return array_merge([
        'type' => 'persons',
        'action' => Import::ACTION_APPEND,
        'validation_strategy' => Import::VALIDATION_STRATEGY_SKIP_ERRORS,
        'allowed_errors' => 10,
        'field_separator' => ',',
        'file' => UploadedFile::fake()->createWithContent('persons.csv', "name,emails\n"),
    ], $overrides);
}

/**
 * Each import action and the permission it requires.
 */
function importActions(): array
{
    $view = IMPORT_VIEW;
    $create = IMPORT_VIEW.'.create';
    $edit = IMPORT_VIEW.'.edit';
    $delete = IMPORT_VIEW.'.delete';
    $import = IMPORT_VIEW.'.import';

    return [
        'list imports' => [$view, fn () => test()->get(route('admin.settings.data_transfer.imports.index'))],
        'download the uploaded file' => [$view, fn () => test()->get(route('admin.settings.data_transfer.imports.download', existingImport()->id))],
        'download the error report' => [$view, fn () => test()->get(route('admin.settings.data_transfer.imports.download_error_report', existingImport()->id))],
        'open the create form' => [$create, fn () => test()->get(route('admin.settings.data_transfer.imports.create'))],
        'upload an import' => [$create, fn () => test()->post(route('admin.settings.data_transfer.imports.store'), importFormPayload())],
        'download a sample' => [$create, fn () => test()->get(route('admin.settings.data_transfer.imports.download_sample', 'persons'))],
        'open the edit form' => [$edit, fn () => test()->get(route('admin.settings.data_transfer.imports.edit', existingImport()->id))],
        'replace an import' => [$edit, fn () => test()->put(route('admin.settings.data_transfer.imports.update', existingImport()->id), importFormPayload())],
        'delete an import' => [$delete, fn () => test()->deleteJson(route('admin.settings.data_transfer.imports.delete', existingImport()->id))],
        'open the import page' => [$import, fn () => test()->get(route('admin.settings.data_transfer.imports.import', existingImport()->id))],
        'validate an import' => [$import, fn () => test()->getJson(route('admin.settings.data_transfer.imports.validate', existingImport()->id))],
        'start an import' => [$import, fn () => test()->getJson(route('admin.settings.data_transfer.imports.start', existingImport()->id))],
        'link imported records' => [$import, fn () => test()->getJson(route('admin.settings.data_transfer.imports.link', existingImport()->id))],
        'index imported records' => [$import, fn () => test()->getJson(route('admin.settings.data_transfer.imports.index_data', existingImport()->id))],
        'poll import progress' => [$import, fn () => test()->getJson(route('admin.settings.data_transfer.imports.stats', existingImport()->id))],
    ];
}

it('sends a signed-out visitor to the login page from every import route', function (string $method, string $uri) {
    test()->call($method, $uri)->assertRedirect(route('admin.session.create'));
})->with([
    'list' => ['GET', '/admin/settings/data-transfer/imports'],
    'upload' => ['POST', '/admin/settings/data-transfer/imports/create'],
    'replace' => ['PUT', '/admin/settings/data-transfer/imports/update/1'],
    'delete' => ['DELETE', '/admin/settings/data-transfer/imports/destroy/1'],
    'validate' => ['GET', '/admin/settings/data-transfer/imports/validate/1'],
    'start' => ['GET', '/admin/settings/data-transfer/imports/start/1'],
    'download' => ['GET', '/admin/settings/data-transfer/imports/download/1'],
    'error report' => ['GET', '/admin/settings/data-transfer/imports/download-error-report/1'],
]);

it('refuses an import action without its permission', function (string $required, Closure $request) {
    test()->actingAs(importRoleUser(array_diff(IMPORT_PERMISSIONS, [$required])), 'user');

    expect($request()->getStatusCode())->toBe(401);
})->with(importActions());

it('allows an import action with its permission', function (string $required, Closure $request) {
    test()->actingAs(importRoleUser(['settings', $required]), 'user');

    expect($request()->getStatusCode())->not->toBe(401);
})->with(importActions());

it('does not let a view-only user upload or run an import', function () {
    $count = ImportModel::query()->count();

    test()->actingAs(importRoleUser(['settings', IMPORT_VIEW]), 'user');

    test()->post(route('admin.settings.data_transfer.imports.store'), importFormPayload())->assertStatus(401);

    expect(ImportModel::query()->count())->toBe($count)
        ->and(Storage::disk(Import::STORAGE_DISK)->allFiles())->toBeEmpty();

    $import = existingImport();

    test()->getJson(route('admin.settings.data_transfer.imports.validate', $import->id))->assertStatus(401);
    test()->getJson(route('admin.settings.data_transfer.imports.start', $import->id))->assertStatus(401);

    expect($import->fresh()->state)->toBe(Import::STATE_PENDING);
});

it('does not let an import be run with only the delete permission', function () {
    test()->actingAs(importRoleUser(['settings', IMPORT_VIEW.'.delete']), 'user')
        ->getJson(route('admin.settings.data_transfer.imports.start', existingImport()->id))
        ->assertStatus(401);
});
