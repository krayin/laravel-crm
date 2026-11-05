<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Webkul\User\Models\Role;
use Webkul\User\Models\User;

uses(DatabaseTransactions::class);

const USER_MANAGER_PERMISSIONS = [
    'settings',
    'settings.user',
    'settings.user.users',
    'settings.user.users.create',
    'settings.user.users.edit',
    'settings.user.users.delete',
];

function userManagerRole(): Role
{
    return Role::create([
        'name' => 'User manager '.bin2hex(random_bytes(4)),
        'description' => 'Created by the user management scope tests.',
        'permission_type' => 'custom',
        'permissions' => USER_MANAGER_PERMISSIONS,
    ]);
}

function scopedUser(Role $role, string $viewPermission, ?int $createdBy = null): User
{
    return User::create([
        'name' => 'Scoped '.bin2hex(random_bytes(3)),
        'email' => 'scoped-'.bin2hex(random_bytes(5)).'@example.invalid',
        'password' => bcrypt('original-password'),
        'status' => 1,
        'view_permission' => $viewPermission,
        'role_id' => $role->id,
        'created_by' => $createdBy ?? getDefaultAdmin()->id,
    ]);
}

function userUpdatePayload(User $user, array $overrides = []): array
{
    return array_merge([
        'name' => $user->name,
        'email' => $user->email,
        'role_id' => $user->role_id,
        'view_permission' => $user->view_permission,
        'status' => 1,
        'password' => 'taken-over-123',
        'confirm_password' => 'taken-over-123',
    ], $overrides);
}

beforeEach(function () {
    $this->role = userManagerRole();

    $this->editor = scopedUser($this->role, 'individual');
});

it('refuses to reset the password of a user outside the editor data scope', function () {
    $victim = scopedUser($this->role, 'individual');

    $this->actingAs($this->editor, 'user')
        ->putJson(route('admin.settings.users.update', $victim->id), userUpdatePayload($victim))
        ->assertUnauthorized();

    expect(Hash::check('original-password', $victim->fresh()->password))->toBeTrue();
});

it('refuses to take over a same role user holding a broader data scope', function () {
    $victim = scopedUser($this->role, 'global');

    $this->actingAs($this->editor, 'user')
        ->putJson(route('admin.settings.users.update', $victim->id), userUpdatePayload($victim))
        ->assertUnauthorized();

    expect(Hash::check('original-password', $victim->fresh()->password))->toBeTrue();
});

it('refuses to take over a user by changing their email', function () {
    $victim = scopedUser($this->role, 'global');

    $this->actingAs($this->editor, 'user')
        ->putJson(route('admin.settings.users.update', $victim->id), userUpdatePayload($victim, [
            'email' => 'attacker-'.bin2hex(random_bytes(4)).'@example.invalid',
            'password' => null,
            'confirm_password' => null,
        ]))
        ->assertUnauthorized();
});

it('refuses to show a user outside the editor data scope', function () {
    $victim = scopedUser($this->role, 'individual');

    $this->actingAs($this->editor, 'user')
        ->getJson(route('admin.settings.users.edit', $victim->id))
        ->assertUnauthorized();
});

it('refuses to delete or deactivate a user outside the editor data scope', function () {
    $victim = scopedUser($this->role, 'individual');

    $this->actingAs($this->editor, 'user')
        ->deleteJson(route('admin.settings.users.delete', $victim->id));

    $this->actingAs($this->editor, 'user')
        ->postJson(route('admin.settings.users.mass_update'), ['indices' => [$victim->id], 'value' => 0]);

    $this->actingAs($this->editor, 'user')
        ->postJson(route('admin.settings.users.mass_delete'), ['indices' => [$victim->id]]);

    $victim = User::find($victim->id);

    expect($victim)->not->toBeNull()
        ->and((int) $victim->status)->toBe(1);
});

it('still lets the editor manage a user they created', function () {
    $own = scopedUser($this->role, 'individual', $this->editor->id);

    $this->actingAs($this->editor, 'user')
        ->putJson(route('admin.settings.users.update', $own->id), userUpdatePayload($own))
        ->assertOk();

    expect(Hash::check('taken-over-123', $own->fresh()->password))->toBeTrue();
});

it('still lets an administrator manage any user', function () {
    $victim = scopedUser($this->role, 'global');

    $this->actingAs(getDefaultAdmin(), 'user')
        ->putJson(route('admin.settings.users.update', $victim->id), userUpdatePayload($victim))
        ->assertOk();

    expect(Hash::check('taken-over-123', $victim->fresh()->password))->toBeTrue();
});
