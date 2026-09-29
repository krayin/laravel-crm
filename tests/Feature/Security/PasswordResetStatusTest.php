<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Webkul\Admin\Http\Controllers\User\ResetPasswordController;
use Webkul\User\Models\Role;
use Webkul\User\Models\User;

/**
 * Completing a password reset must not sign in a deactivated account.
 *
 * Login refuses `status == 0` and logs the user straight back out, so resetting a password must
 * not become the way around that check.
 *
 * Every test here runs inside a transaction that is rolled back afterwards, so the temporary
 * accounts never persist in the database, and each creates the role it needs rather than assuming
 * the database has been seeded.
 */
uses(DatabaseTransactions::class);

/**
 * Invoke the controller's protected resetPassword() the way the password broker does.
 */
function completeResetFor(User $user): void
{
    $controller = app(ResetPasswordController::class);

    $method = (new ReflectionClass($controller))->getMethod('resetPassword');
    $method->setAccessible(true);
    $method->invoke($controller, $user, 'brand-new-password');
}

function makeUser(int $status): User
{
    /**
     * Created here rather than assumed: a freshly migrated test database has no roles, and the
     * users table has a foreign key onto them.
     */
    $role = Role::create([
        'name' => 'Reset Probe Role '.bin2hex(random_bytes(6)),
        'description' => 'Created by the password reset status tests.',
        'permission_type' => 'all',
    ]);

    return User::create([
        'name' => 'Reset Status Probe',
        'email' => 'reset-probe-'.bin2hex(random_bytes(6)).'@example.invalid',
        'password' => bcrypt('original-password'),
        'status' => $status,
        'role_id' => $role->id,
        'view_permission' => 'global',
    ]);
}

afterEach(function () {
    auth()->guard('user')->logout();
});

it('does not sign in a deactivated account after a password reset', function () {
    $user = makeUser(status: 0);

    completeResetFor($user);

    expect(auth()->guard('user')->check())->toBeFalse();
});

it('still signs in an active account after a password reset', function () {
    $user = makeUser(status: 1);

    completeResetFor($user);

    expect(auth()->guard('user')->check())->toBeTrue()
        ->and(auth()->guard('user')->id())->toBe($user->id);
});

it('changes the password for a deactivated account even though it does not sign in', function () {
    $user = makeUser(status: 0);

    completeResetFor($user);

    expect(Hash::check('brand-new-password', $user->fresh()->password))->toBeTrue();
});
