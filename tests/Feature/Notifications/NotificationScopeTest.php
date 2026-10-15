<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Webkul\Admin\Repositories\NotificationRepository;
use Webkul\User\Models\Role;
use Webkul\User\Models\User;

/**
 * Who a notification is addressed to.
 *
 * Data scope and role permissions are independent in Krayin, so both are checked: a user with a
 * global scope but no access to a module would otherwise be told about every record in it — the
 * record's title and owner — while being unable to open any of them.
 */
uses(DatabaseTransactions::class);

function makeNotificationScopeUser(string $scope, string $permissionType, array $permissions = [], int $status = 1): User
{
    $role = Role::create([
        'name' => 'role-'.uniqid(),
        'description' => 'test',
        'permission_type' => $permissionType,
        'permissions' => $permissions,
    ]);

    return User::create([
        'name' => 'user-'.uniqid(),
        'email' => uniqid().'@example.com',
        'password' => bcrypt('password'),
        'status' => $status,
        'view_permission' => $scope,
        'role_id' => $role->id,
    ]);
}

it('does not tell a user about records their role cannot reach', function () {
    $contactsOnly = makeNotificationScopeUser('global', 'custom', ['contacts', 'contacts.persons']);

    $recipients = app(NotificationRepository::class)->resolveRecipients('lead', 1, null);

    expect($recipients)->not->toContain($contactsOnly->id);
});

it('tells a user about records their role does reach', function () {
    $contactsOnly = makeNotificationScopeUser('global', 'custom', ['contacts', 'contacts.persons']);

    $recipients = app(NotificationRepository::class)->resolveRecipients('person', 1, null);

    expect($recipients)->toContain($contactsOnly->id);
});

it('tells a user holding every permission about every kind of record', function () {
    $admin = makeNotificationScopeUser('global', 'all');

    $repository = app(NotificationRepository::class);

    foreach (['lead', 'quote', 'person', 'product', 'activity', 'email'] as $entity) {
        expect($repository->resolveRecipients($entity, 1, null))->toContain($admin->id);
    }
});

it('does not address a deactivated account', function () {
    $inactive = makeNotificationScopeUser('global', 'all', [], 0);

    $recipients = app(NotificationRepository::class)->resolveRecipients('lead', 1, null);

    expect($recipients)->not->toContain($inactive->id);
});

it('tells nobody about an entity it has no permission mapped for', function () {
    makeNotificationScopeUser('global', 'all');

    $recipients = app(NotificationRepository::class)->resolveRecipients('widget', 1, null);

    expect($recipients)->toBeEmpty();
});

it('keeps a group user to their own group', function () {
    $owner = makeNotificationScopeUser('global', 'all');

    $outsider = makeNotificationScopeUser('group', 'all');

    $recipients = app(NotificationRepository::class)->resolveRecipients('lead', $owner->id, null);

    expect($recipients)->not->toContain($outsider->id);
});

it('never tells a user about their own action', function () {
    $actor = makeNotificationScopeUser('global', 'all');

    $recipients = app(NotificationRepository::class)->resolveRecipients('lead', $actor->id, $actor->id);

    expect($recipients)->not->toContain($actor->id);
});

it('only tells global-scope users about a record nobody owns', function () {
    $individual = makeNotificationScopeUser('individual', 'all');

    $recipients = app(NotificationRepository::class)->resolveRecipients('lead', null, null);

    expect($recipients)->not->toContain($individual->id);
});
