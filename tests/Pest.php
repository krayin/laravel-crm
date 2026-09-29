<?php

use Illuminate\Support\Facades\URL;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
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
 * @return User
 */
function getDefaultAdmin()
{
    $admin = User::find(1);

    return $admin;
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
