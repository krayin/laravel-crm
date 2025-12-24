<?php

// Create role
$role = \Webkul\User\Models\Role::firstOrCreate(
    ['id' => 1],
    [
        'name' => 'Administrator',
        'description' => 'Admin Role',
        'permission_type' => 'all'
    ]
);

echo "Role created: {$role->name}\n";

// Create user
$user = \Webkul\User\Models\User::firstOrCreate(
    ['email' => 'admin@admin.com'],
    [
        'name' => 'Admin',
        'password' => bcrypt('admin123'),
        'role_id' => 1,
        'status' => 1,
        'view_permission' => 'global'
    ]
);

echo "User created: {$user->email}\n";
echo "Login at: http://127.0.0.1:8000/admin/login\n";
echo "Email: admin@admin.com\n";
echo "Password: admin123\n";
