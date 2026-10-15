<?php

use Illuminate\Support\Facades\URL;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Webkul\Attribute\Models\Attribute;
use Webkul\Lead\Models\Pipeline;
use Webkul\Lead\Models\Source;
use Webkul\Lead\Models\Type;
use Webkul\User\Models\Role;
use Webkul\User\Models\User;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
 */

uses(TestCase::class)->in('Feature');

/**
 * The configured APP_URL may point at a subdirectory (for example when the application is served
 * from `/public` under a project folder). Laravel's test client prepends that URL to every request
 * path, so the resulting path info would carry the subdirectory and no route would ever match,
 * failing every feature test with a 404. Pinning a bare root URL keeps request paths aligned with
 * the routes as registered, whatever APP_URL happens to be on the machine running the tests.
 */
uses()->beforeEach(function () {
    URL::forceRootUrl('http://localhost');
})->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
 */

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
 */

/**
 * Get default admin which is created on fresh instance.
 *
 * The parallel test databases are migrated but not seeded, so the seeded administrator is not
 * always there to find; one is created when it is missing, rather than handing back null and
 * failing the caller on a property read.
 *
 * @return User
 */
function getDefaultAdmin()
{
    $admin = User::find(1) ?? User::query()->whereHas(
        'role',
        fn ($query) => $query->where('permission_type', 'all')
    )->first();

    if ($admin) {
        return $admin;
    }

    $role = Role::query()->firstOrCreate(
        ['name' => 'Administrator'],
        ['description' => 'Administrator role', 'permission_type' => 'all']
    );

    return User::create([
        'name' => 'Example',
        'email' => 'admin@example.com',
        'password' => bcrypt('admin123'),
        'status' => 1,
        'view_permission' => 'global',
        'role_id' => $role->id,
    ]);
}

/**
 * Sanctum authenticated admin.
 *
 * @return User
 */
function actingAsSanctumAuthenticatedAdmin()
{
    return Sanctum::actingAs(
        getDefaultAdmin(),
        ['*']
    );
}

/**
 * Get first name.
 *
 * @param  string  $fullName
 * @return string
 */
function getFirstName($fullName)
{
    return explode(' ', $fullName)[0];
}

/**
 * The default lead pipeline and its first stage, created when they are absent.
 *
 * The suite runs in parallel against separate databases, which are migrated but not seeded. A test
 * that hardcoded pipeline id 1 therefore referred to a row that existed only in the developer's own
 * database, and inserting a lead failed on its foreign key.
 */
function securityTestPipeline(): Pipeline
{
    /**
     * A lead also needs a source and a type: the web form controller falls back to the first of
     * each when the submission does not name one, and dereferences the result without checking.
     */
    if (! Source::query()->count()) {
        Source::create(['name' => 'Web Form']);
    }

    if (! Type::query()->count()) {
        Type::create(['name' => 'New Business']);
    }

    $pipeline = Pipeline::query()->first();

    if (! $pipeline) {
        $pipeline = Pipeline::create([
            'name' => 'Security Test Pipeline',
            'is_default' => 1,
            'rotten_days' => 30,
        ]);
    }

    if (! $pipeline->stages()->count()) {
        $pipeline->stages()->create([
            'name' => 'New',
            'code' => 'new',
            'probability' => 100,
            'sort_order' => 1,
        ]);
    }

    return $pipeline;
}

/**
 * An attribute by entity and code, created when it is absent.
 *
 * The parallel test databases are migrated but not seeded, so they carry only the attributes that
 * migrations add — `leads.title`, `persons.name` and the rest are missing. A test that skipped a
 * missing attribute silently built a web form with no fields and then failed on an assertion that
 * had nothing to do with the cause.
 */
function webFormAttributeFor(string $entityType, string $code): Attribute
{
    $attribute = Attribute::query()
        ->where('entity_type', $entityType)
        ->where('code', $code)
        ->first();

    if ($attribute) {
        return $attribute;
    }

    /**
     * The type decides how a value is stored and validated — an `email` or `phone` attribute holds
     * a list of entries, where a `text` one holds a string — so creating these as plain text made
     * the insert fail on converting an array to a string.
     */
    $types = [
        'emails' => 'email',
        'contact_numbers' => 'phone',
    ];

    return Attribute::create([
        'code' => $code,
        'name' => ucfirst(str_replace('_', ' ', $code)),
        'type' => $types[$code] ?? 'text',
        'entity_type' => $entityType,
        'is_required' => 0,
        'is_unique' => 0,
        'quick_add' => 0,
        'sort_order' => 1,
    ]);
}
